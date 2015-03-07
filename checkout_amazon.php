<?php
/* --------------------------------------------------------------
	Amazon Advanced Payment APIs Modul  V2.00
   	checkout_amazon.php 2014-06-03

   	alkim media
   	http://www.alkim.de

   	patworx multimedia GmbH
   	http://www.patworx.de/

	Released under the GNU General Public License
--------------------------------------------------------------
*/
?><?php
include('includes/application_top.php');
include_once('lang/' . $_SESSION["language"] . '/modules/payment/am_apa.php');
$breadcrumb->add(AMAZON_CHECKOUT, xtc_href_link('checkout_amazon.php', '', 'SSL'));
$_SESSION['payment'] = 'am_apa';
unset($_SESSION["amazon_target"]);
// create smarty elements
$smarty              = new Smarty;
// include boxes
require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions

require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
require_once(DIR_FS_INC . 'xtc_count_shipping_modules.inc.php');
require_once(DIR_FS_INC . 'xtc_create_password.inc.php');

require(DIR_WS_CLASSES . 'http_client.php');


if(isset($_GET["amazon_action"]) && $_GET["amazon_action"] == 'cancelOrder'){
    if(isset($_SESSION['amazon_id']) && $_SESSION['amazon_id'] != ''){
        include_once('AmazonAdvancedPayment/.config.inc.php');
        include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
        include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonTransactions.class.php');
        AlkimAmazonTransactions::cancelOrder($_SESSION['amazon_id']);
    }
    xtc_redirect(xtc_href_link('shopping_cart.php', '', 'SSL'));
    die;
}
// Process the Order start
if ((isset($_POST['action'])) && ($_POST['action'] == 'process')) {
    if (!isset($_SESSION['cart']) || $_SESSION['cart']->count_contents() < 1)
        xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));

    $pos = strpos($_POST['amazon_id'], 'amzn');
    if ($pos === false) {
        $_POST['amazon_id'] = $_SESSION['amazon_id'];
    }

    include_once('AmazonAdvancedPayment/.config.inc.php');
    include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
    // Service initialisieren
    $config                     = AlkimAmazonHandler::getConfig();
    $service                    = new OffAmazonPaymentsService_Client($config);
    $getOrderRefDetailsReqModel = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest(array(
        'AmazonOrderReferenceId' => $_POST['amazon_id'],
        'SellerId' => AWS_MERCHANT_ID
    ));

    try {
        $getOrderRefDetailsResp = $service->getOrderReferenceDetails($getOrderRefDetailsReqModel);
    }
    catch (OffAmazonPaymentsService_Exception $e) {

    }

    $_SESSION["amzOrderReference"] = $_POST['amazon_id'];
    if(!is_object($getOrderRefDetailsResp->getGetOrderReferenceDetailsResult()->getOrderReferenceDetails()->getDestination())){
        $noShippingAddress = true;
        $_SESSION['shipping'] = false;
	    $_SESSION['sendto'] = false;
    }
    if(!$noShippingAddress){
    $iso_code                      = (string) $getOrderRefDetailsResp->getGetOrderReferenceDetailsResult()->getOrderReferenceDetails()->getDestination()->getPhysicalDestination()->getCountryCode();
    $sql                           = "SELECT countries_name, countries_id FROM " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . $iso_code . "' LIMIT 1";
    $country_query                 = xtc_db_query($sql);
    $country_result                = xtc_db_fetch_array($country_query);
    $cba_address_array             = array();
    $_SESSION['delivery_zone']     = $iso_code;
    }else{
        $sql                           = "SELECT * FROM " . TABLE_COUNTRIES . " WHERE countries_id = '" . (int)STORE_COUNTRY . "' LIMIT 1";
        $country_query                 = xtc_db_query($sql);
        $country_result                = xtc_db_fetch_array($country_query);
        $_SESSION['AMZ_COUNTRY_ID'] = STORE_COUNTRY;
        $_SESSION['AMZ_ZONE_ID'] = STORE_COUNTRY;
        $_SESSION['delivery_zone']      = $country_result["countries_iso_code_2"];
    }
    require_once(DIR_WS_CLASSES . 'order.php');
    $order = new order();
    $order->delivery['country_id']       = $_SESSION['AMZ_COUNTRY_ID'];
    $order->delivery['country']['id']    = $_SESSION['AMZ_COUNTRY_ID'];
    $order->delivery['zone_id']          = $_SESSION['AMZ_ZONE_ID'];
    if(AMZ_DOWNLOAD_ONLY == 'True'){
        $order->content_type = 'virtual';
    }
    
    //UPDATE 2014-06-30
    require_once (DIR_WS_CLASSES . 'shipping.php');
    $shipping_modules = new shipping($_SESSION['shipping']);
    require_once(DIR_WS_CLASSES . 'order_total.php');
    $order_total_modules = new order_total();
    $order_totals        = $order_total_modules->process();
    $totalOrderAmount    = $order_totals[count($order_totals) - 1]['value'];

    if($totalOrderAmount == 0){
        $_SESSION['info_message'] = AMZ_AMOUNT_TOO_LOW_ERROR;
        xtc_redirect(xtc_href_link('shopping_cart.php', 'info_message='.urlencode(AMZ_AMOUNT_TOO_LOW_ERROR), 'SSL'));
    }



    $orderTotal = new OffAmazonPaymentsService_Model_OrderTotal();
    $orderTotal->setAmount($totalOrderAmount);
    $orderTotal->setCurrencyCode('EUR');
    


    $orderReferenceAttributes = new OffAmazonPaymentsService_Model_OrderReferenceAttributes();
    $orderReferenceAttributes->setOrderTotal($orderTotal);
    $orderReferenceAttributes->setPlatformId('AYJ786YBX3WE4');
    if(AMZ_SET_SELLER_ORDER_ID == 'True'){
        $_SESSION["amzReservedOrdersId"] = AlkimAmazonHandler::reserveNextOrdersId();
        $SellerOrderAttributes = new OffAmazonPaymentsService_Model_SellerOrderAttributes();
        $SellerOrderAttributes->setSellerOrderId($_SESSION["amzReservedOrdersId"]);
        $orderReferenceAttributes->setSellerOrderAttributes($SellerOrderAttributes);
    }
    $setOrderRefDetailsReqModel = new OffAmazonPaymentsService_Model_SetOrderReferenceDetailsRequest();
    $setOrderRefDetailsReqModel->setAmazonOrderReferenceId($_POST['amazon_id']);
    $setOrderRefDetailsReqModel->setSellerId(AWS_MERCHANT_ID);
    $setOrderRefDetailsReqModel->setOrderReferenceAttributes($orderReferenceAttributes);
    
    

    try {
        $setOrderRefDetailsResp = $service->setOrderReferenceDetails($setOrderRefDetailsReqModel);
    }
    catch (OffAmazonPaymentsService_Exception $e) {

    }
    $confirmOrderRefReqModel = new OffAmazonPaymentsService_Model_ConfirmOrderReferenceRequest;
    $confirmOrderRefReqModel->setAmazonOrderReferenceId($_POST['amazon_id']);
    $confirmOrderRefReqModel->setSellerId(AWS_MERCHANT_ID);
    try {
        $confirmOrderRefResp = $service->confirmOrderReference($confirmOrderRefReqModel);
    }
    catch (OffAmazonPaymentsService_Exception $e) {

    }


    try {
        $getOrderRefDetailsResp = $service->getOrderReferenceDetails($getOrderRefDetailsReqModel);
    }
    catch (OffAmazonPaymentsService_Exception $e) {

    }

    $details = $getOrderRefDetailsResp->GetOrderReferenceDetailsResult->getOrderReferenceDetails();
    $sqlArr  = array(
        'amz_tx_time' => time(),
        'amz_tx_type' => 'order_ref',
        'amz_tx_status' => $details->getOrderReferenceStatus()->getState(),
        'amz_tx_order_reference' => $_POST['amazon_id'],
        'amz_tx_expiration' => strtotime($details->getExpirationTimestamp()),
        'amz_tx_reference' => $_POST['amazon_id'],
        'amz_tx_amz_id' => $_POST['amazon_id'],
        'amz_tx_last_change' => time(),
        'amz_tx_amount' => $details->getOrderTotal()->getAmount(),
        'amz_tx_merchant_id'=>AWS_MERCHANT_ID
    );
    xtc_db_perform('amz_transactions', $sqlArr);
    if(!$noShippingAddress){
        $address     = $details->getDestination()->getPhysicalDestination();

        $cba_address_array = AlkimAmazonHandler::getAddressArrayFromAmzAddress($address);
    }else{
        $cba_address_array = array();
    }
    $email             = $getOrderRefDetailsResp->GetOrderReferenceDetailsResult->getOrderReferenceDetails()->getBuyer()->getEmail();

    $address_book_sql_array = array(
        'customers_id' => '',
        'entry_firstname' => $cba_address_array['firstname'],
        'entry_lastname' => $cba_address_array['lastname'],
        'entry_company' => $cba_address_array['company'],
        'entry_suburb' => $cba_address_array['suburb'],
        'entry_street_address' => $cba_address_array["street_address"],
        'entry_postcode' => $cba_address_array['postcode'],
        'entry_city' => $cba_address_array['city'],
        'entry_country_id' => $cba_address_array['country']["id"]
    );
    // if guest: create entries for `customers` and `address_book`

    if (!$_SESSION['customer_id']) {

        $password = xtc_create_password(8);

        $sql_data_array = array(
            'customers_status' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST,
            'customers_gender' => '',
            'customers_firstname' => $cba_address_array["firstname"],
            'customers_lastname' => $cba_address_array['lastname'],
            'customers_dob' => '0000-00-00 00:00:00',
            'customers_email_address' => $email,
            'customers_default_address_id' => '0',
            'customers_telephone' => (string) $phone,
            'customers_password' => $password,
            'customers_newsletter' => 0,
            'customers_newsletter_mode' => 0,
            'member_flag' => 0,
            'delete_user' => 1,
            'account_type' => 1
        );

        xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);
        $customer_insert_id = xtc_db_insert_id();

        $address_book_sql_array["customers_id"] = $customer_insert_id;
        xtc_db_perform(TABLE_ADDRESS_BOOK, $address_book_sql_array);
        $customer_address_id = xtc_db_insert_id();
        xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_cid = 'AMZ" . $customer_insert_id . "', customers_default_address_id = '" . $customer_address_id . "' where customers_id = '" . (int) $customer_insert_id . "'");
        xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int) $customer_insert_id . "', '0', now())");
        $_SESSION['customer_id'] = $customer_insert_id;
        $_SESSION["sendto"]      = $customer_address_id;
        unset($_SESSION["billto"]);

    } else {
        $q  = "SELECT * FROM " . TABLE_ADDRESS_BOOK . " WHERE
                        customers_id = " . (int) $_SESSION['customer_id'] . "
                            AND
                        entry_firstname = '" . xtc_db_input($cba_address_array['firstname']) . "'
                            AND
                        entry_lastname = '" . xtc_db_input($cba_address_array['lastname']) . "'
                            AND
                        entry_street_address = '" . xtc_db_input($cba_address_array['street_address']) . "'
                            AND
                        entry_postcode = '" . xtc_db_input($cba_address_array['postcode']) . "'
                            AND
                        entry_city = '" . xtc_db_input($cba_address_array['city']) . "'
                            AND
                        entry_country_id = '" . xtc_db_input($country_result['countries_id']) . "'";
        $rs = xtc_db_query($q);
        if (xtc_db_num_rows($rs) > 0) {
            $r                  = xtc_db_fetch_array($rs);
            $_SESSION["sendto"] = $r["address_book_id"];
            unset($_SESSION["billto"]);
        } else {
            $address_book_sql_array["customers_id"] = (int) $_SESSION['customer_id'];
            xtc_db_perform(TABLE_ADDRESS_BOOK, $address_book_sql_array);
            $customer_address_id = xtc_db_insert_id();
            $_SESSION["sendto"]  = $customer_address_id;
            unset($_SESSION["billto"]);
        }
    }
    
    
    //OVERWRITE EMPTY DEFAULT AMAZON ADDRESS FROM AMAZON LOGIN 
    $q = "SELECT ab.* FROM address_book ab, customers c WHERE ab.address_book_id = c.customers_default_address_id AND c.customers_id = ".(int) $_SESSION['customer_id']." AND ab.customers_id = c.customers_id";
    $rs = xtc_db_query($q);
    $r = xtc_db_fetch_array($rs);
    if($r["entry_street_address"] == '' && $r["entry_city"] == ''){
        $delAbId = $r["address_book_id"];
        $q = "SELECT * FROM address_book WHERE entry_street_address != '' AND entry_city != '' AND customers_id = ".(int) $_SESSION['customer_id'];
        $rs = xtc_db_query($q);
        if($r = xtc_db_fetch_array($rs)){
            $q = "UPDATE customers SET customers_default_address_id = ".(int)$r["address_book_id"]." WHERE  customers_id = ".(int) $_SESSION['customer_id'];
            xtc_db_query($q);
            $q = "DELETE FROM address_book WHERE address_book_id=".(int)$delAbId;
            xtc_db_query($q);
        }
    }

    $_SESSION["amzOrderTotal"] = $totalOrderAmount;
    $_SESSION["comments"]      = $_POST["comments"];
    if(defined('IS_GAMBIO') && IS_GAMBIO == 1){
        if(!is_object($_SESSION['coo_gprint_cart'])){
            $_SESSION['coo_gprint_cart'] = new GMGPrintCartManager;
        }
    }
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PROCESS, 'amz=1', 'SSL'));
}
// Process the Order end

if ($_SESSION['cart']->show_total() > 0) {
    if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order']) {
        $_SESSION['allow_checkout'] = 'false';
    }
    if ($_SESSION['customers_status']['customers_status_max_order'] != 0) {
        if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order']) {
            $_SESSION['allow_checkout'] = 'false';
        }
    }
}



// check if checkout is allowed
if ($_SESSION['allow_checkout'] == 'false')
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// if the customer is not logged on, redirect them to the login page
if (!isset($_SESSION['customer_id'])) {


    if (MODULE_PAYMENT_AM_APA_ALLOW_GUESTS == 'True') {
        $guest_order = true;
    } else {
        xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() <= 0) {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

// Check if Amazon ID is set and Amazon is allowed
if ((isset($_SESSION['amazon_id'])) && ($_SESSION['amazon_id'] != '') && (MODULE_PAYMENT_AM_APA_STATUS == 'True')) {
    $amazon_id = $_SESSION['amazon_id'];
} elseif(isset($_GET["fromRedirect"]) && $_SESSION["amz_access_token"]){

} else {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

require(DIR_WS_INCLUDES . 'header.php');
//check if display conditions on checkout page is true
if (GROUP_CHECK == 'true') {
    $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
}

$shop_content_query = xtc_db_query("SELECT
	                                                content_title,
	                                                content_heading,
	                                                content_text,
	                                                content_file
	                                                FROM " . TABLE_CONTENT_MANAGER . "
	                                                WHERE content_group='" . (int) AMZ_AGB_ID . "' " . $group_check . "
	                                                AND languages_id='" . $_SESSION['languages_id'] . "'");
$shop_content_data  = xtc_db_fetch_array($shop_content_query);

if ($shop_content_data['content_file'] != '') {
    $conditions = '<iframe src="' . (defined('GM_HTTP_SERVER') ? GM_HTTPS_SERVER : HTTPS_SERVER) . DIR_WS_CATALOG . 'media/content/' . $shop_content_data['content_file'] . '" width="99%" height="150">';
    $conditions .= '</iframe>';
} else {
    $conditions = '<div style="height:150px; border: 1px solid #666; overflow: auto; padding: 10px;"  name="conditions_text" >' . $shop_content_data['content_text'] . '</div>';
}

$smarty->assign('AGB', $conditions);
$smarty->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO));

if (isset($_GET['step']) && $_GET['step'] == 'step2') {
    $smarty->assign('CHECKBOX_AGB', '<input type="checkbox" value="conditions" name="conditions" id="cba_conditions" checked="checked"  class="cba_confirm_boxes"/>');
} else {
    unset($_SESSION['conditions']);
    $smarty->assign('CHECKBOX_AGB', '<input type="checkbox" value="conditions" name="conditions" id="cba_conditions"  class="cba_confirm_boxes"/>');
}

if (GROUP_CHECK == 'true') {
    $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
}

if(defined('IS_GAMBIO') && IS_GAMBIO == 1){
    include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonForGambio.class.php');
    $withdrawal = AlkimAmazonForGambio::getWithdrawalHtml();
}else{
    if(AMZ_REVOCATION_ID != ''){
        $shop_content_query = xtc_db_query("SELECT
	                                                        content_title,
	                                                        content_heading,
	                                                        content_text,
	                                                        content_file
	                                                        FROM " . TABLE_CONTENT_MANAGER . "
	                                                        WHERE content_group='" . (int) AMZ_REVOCATION_ID . "' " . $group_check . "
	                                                        AND languages_id='" . $_SESSION['languages_id'] . "'");
        $shop_content_data  = xtc_db_fetch_array($shop_content_query);

        if ($shop_content_data['content_file'] != '') {

            $withdrawal = '<iframe src="' . (defined('GM_HTTP_SERVER') ? GM_HTTPS_SERVER : HTTPS_SERVER) . DIR_WS_CATALOG . 'media/content/' . $shop_content_data['content_file'] . '" width="99%" height="150">';
            $withdrawal .= '</iframe>';
        } else {
            $withdrawal = '<div style="height:150px; border: 1px solid #666; overflow: auto; padding: 10px;" name="withdrawal_text">' . $shop_content_data['content_text'] . '</div>';
        }
    }    
}
require_once(DIR_WS_CLASSES . 'order.php');
 $order = new order();
    if(AMZ_DOWNLOAD_ONLY == 'True'){
        $order->content_type = 'virtual';
    }
if($order->content_type == 'virtual' && (AMZ_AUTHORIZATION_MODE == 'fast_auth' || $_SESSION['customer_id'])){
    $smarty->assign('NO_SHIPPING', 1);
    $_SESSION['AMZ_COUNTRY_ID'] = STORE_COUNTRY;
    $_SESSION['AMZ_ZONE_ID'] = STORE_ZONE;
}
if($withdrawal != ''){
    $smarty->assign('WITHDRAWAL', $withdrawal);
}
unset($_SESSION['withdrawal']);
$smarty->assign('CHECKBOX_WITHDRAWAL', '<input type="checkbox" value="withdrawal" name="withdrawal" id="cba_withdrawal" class="cba_confirm_boxes" />');
$smarty->assign('AGB_SHORT_TEXT', sprintf(AGB_SHORT_TEXT, xtc_href_link(FILENAME_CONTENT, 'coID=' . (int) AMZ_AGB_ID, $request_type), xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . (int) AMZ_AGB_ID, $request_type)) . '<br />' . sprintf(REVOCATION_SHORT_TEXT, xtc_href_link(FILENAME_CONTENT, 'coID=' . (int) AMZ_REVOCATION_ID, $request_type), xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . (int) AMZ_REVOCATION_ID, $request_type)));
$smarty->assign('COMMENTS', xtc_draw_textarea_field('comments', 'soft', '60', '5', $_SESSION["comments"], 'style="width:96%;"') . xtc_draw_hidden_field('comments_added', 'YES'));
$smarty->assign('AMZ_ZOLL', AMZ_ZOLL);
$smarty->assign('AMZ_NO_PAYMENT', NO_SHIPPING_TO_ADDRESS);
# BUGFIX 2014-08-05 BOM  
if(defined('IS_GAMBIO') && IS_GAMBIO == 1){
    $smarty->assign('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
    $smarty->assign('LIGHTBOX_CLOSE', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
}
# BUGFIX 2014-08-05 EOM  

if ($_SESSION['style_edit_mode'] == 'edit')
    $smarty->assign('STYLE_EDIT', 1);
else
    $smarty->assign('STYLE_EDIT', 0);

$smarty->assign('FORM_ACTION', xtc_draw_form('checkout_amazon', xtc_href_link('checkout_amazon.php', '', 'SSL')) . xtc_draw_hidden_field('action', 'process') . xtc_draw_hidden_field('amazon_id', $_GET['amazon_id']));
if ($_GET["error"]) {
    $smarty->assign('ERROR', $error);
}

$smarty->assign('BUTTON_BACK', '<a href="' . FILENAME_SHOPPING_CART . '"><img src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/button_back.gif" title="' . CANCEL . '" alt="' . CANCEL . '"/></a>');
$smarty->assign('BUTTON_CONTINUE', '<img style="cursor:pointer;" onclick="cba_submit_order()" title="' . IMAGE_BUTTON_CONFIRM_ORDER . '" alt="'.IMAGE_BUTTON_CONFIRM_ORDER.'" src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/button_buy_now.gif">');
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content    = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_amazon.html');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM)) {
    $smarty->loadFilter('output', 'note');
}
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include('includes/application_bottom.php');
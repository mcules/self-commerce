<?php
/* --------------------------------------------------------------
Amazon Advanced Payment APIs Modul  V2.00
checkout_amazon_handler.php 2014-06-03

alkim media
http://www.alkim.de

patworx multimedia GmbH
http://www.patworx.de/

Released under the GNU General Public License
--------------------------------------------------------------
*/
?><?php

include('includes/application_top.php');
include_once('AmazonAdvancedPayment/.config.inc.php');

include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
require_once(DIR_WS_CLASSES.'order.php');
require_once(DIR_WS_CLASSES.'order_total.php');
require_once(DIR_WS_CLASSES.'shipping.php');
require_once (DIR_FS_INC.'xtc_count_shipping_modules.inc.php');

error_reporting(E_NONE);
$caBundleFile = DIR_FS_DOCUMENT_ROOT . 'AmazonAdvancedPayment/ca-bundle.crt';

// Service initialisieren
$config              = AlkimAmazonHandler::getConfig();
$service             = new OffAmazonPaymentsService_Client($config);
$_SESSION['payment'] = 'am_apa';

$handlerAction = (string) $_GET['handleraction'];


if ($handlerAction == 'setusertoshop' || $handlerAction == 'redirectAuthentication') {
    require_once(DIR_FS_INC . 'xtc_write_user_info.inc.php');
    
    if (!empty($_REQUEST['amazon_target'])) {
        $_SESSION["amazon_target"] = $_REQUEST["amazon_target"];
    }
    if (isset($_REQUEST['access_token'])) {
        $_SESSION["amz_access_token"]          = $_REQUEST['access_token'];
        $_SESSION["amz_access_token_set_time"] = time();
    } else {
        if ($handlerAction == 'redirectAuthentication') {
            xtc_redirect(xtc_href_link('index.php'));
        } else {
            die('error');
        }
    }
    
    if (MODULE_PAYMENT_AM_APA_MODE == 'live')
        $c = curl_init('https://api.amazon.de/auth/o2/tokeninfo?access_token=' . urlencode($_REQUEST['access_token']));
    else
        $c = curl_init('https://api.sandbox.amazon.de/auth/o2/tokeninfo?access_token=' . urlencode($_REQUEST['access_token']));
    
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_CAINFO, $caBundleFile);
    $r = curl_exec($c);
    curl_close($c);
    
    $d = json_decode($r);
    if ($d->aud != MODULE_PAYMENT_AM_APA_CLIENTID) {
        if ($handlerAction == 'redirectAuthentication') {
            xtc_redirect(xtc_href_link('index.php'));
        } else {
            die('error');
        }
    }
    
    // exchange the access token for user profile
    if (MODULE_PAYMENT_AM_APA_MODE == 'live')
        $c = curl_init('https://api.amazon.de/user/profile');
    else
        $c = curl_init('https://api.sandbox.amazon.de/user/profile');
    curl_setopt($c, CURLOPT_HTTPHEADER, array(
        'Authorization: bearer ' . $_REQUEST['access_token']
    ));
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_CAINFO, $caBundleFile);
    $r = curl_exec($c);
    curl_close($c);
    $d               = json_decode($r);
    $customer_userid = $d->user_id;
    $customer_name   = $d->name;
    $customer_email  = $d->email;
    $postcode        = $d->postal_code;
    
    $check = xtc_db_query("SELECT customers_id, customers_vat_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, customers_default_address_id 
							 FROM `" . TABLE_CUSTOMERS . "`
							WHERE `amazon_customer_id` = '" . xtc_db_prepare_input($customer_userid) . "'");
    
    if (xtc_db_num_rows($check) > 0) {
        // Customer already exists - login
        $check_customer = xtc_db_fetch_array($check);
        if (SESSION_RECREATE == 'True') {
            xtc_session_recreate();
        }
        
        $check_country_query = xtc_db_query("select entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $check_customer['customers_id'] . "' and address_book_id = '" . $check_customer['customers_default_address_id'] . "'");
        $check_country       = xtc_db_fetch_array($check_country_query);
        
        $_SESSION['customer_gender']             = $check_customer['customers_gender'];
        $_SESSION['customer_first_name']         = $check_customer['customers_firstname'];
        $_SESSION['customer_last_name']          = $check_customer['customers_lastname'];
        $_SESSION['customer_id']                 = $check_customer['customers_id'];
        $_SESSION['customer_vat_id']             = $check_customer['customers_vat_id'];
        $_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
        $_SESSION['customer_country_id']         = $check_country['entry_country_id'];
        $_SESSION['customer_zone_id']            = $check_country['entry_zone_id'];
        
        $date_now = date('Ymd');
        
        xtc_db_query("update " . TABLE_CUSTOMERS_INFO . " SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '" . (int) $_SESSION['customer_id'] . "'");
        xtc_write_user_info((int) $_SESSION['customer_id']);
        $_SESSION['cart']->restore_contents();
        if (is_object($econda))
            $econda->_loginUser();
        
        if ((isset($_GET["action"]) && $_GET["action"] == 'checkout') || $_SESSION["amazon_target"] == 'checkout') {
            $goto = xtc_href_link('checkout_amazon.php', 'fromRedirect=1', 'SSL');
        } elseif ($_SESSION['cart']->count_contents() > 0) {
            $goto = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
        } else {
            $goto = xtc_href_link(FILENAME_DEFAULT);
        }
        
        if ($handlerAction == 'redirectAuthentication') {
            xtc_redirect($goto);
        } else {
            echo $goto;
        }
    } else {
        
        
        
        $emailCheck = xtc_db_query("SELECT 
		                                *
							        FROM 
							            `" . TABLE_CUSTOMERS . "`
							        WHERE 
							            `customers_email_address` = '" . xtc_db_prepare_input($customer_email) . "'
							                AND
							            `account_type` = '0'
							      ");
        
        if (xtc_db_num_rows($emailCheck) > 0) {
            $_SESSION["amzConnectEmail"]      = $customer_email;
            $_SESSION["amzConnectCustomerId"] = $customer_userid;
            $goto                             = xtc_href_link('checkout_amazon_connect_accounts.php', (isset($_GET["action"]) && $_GET["action"] == 'checkout') ? 'fromRedirect=1' : '', 'SSL');
            if ($handlerAction == 'redirectAuthentication') {
                xtc_redirect($goto);
            } else {
                echo $goto;
            }
            
            
            
        } else {
            // Customer does not exist - Create account
            $firstname = '';
            $lastname  = '';
            if (strpos(trim($customer_name), " ") !== false) {
                list($firstname, $lastname) = explode(' ', trim($customer_name));
            }
            require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
            $sql_data_array = array(
                'customers_status' => DEFAULT_CUSTOMERS_STATUS_ID,
                'customers_firstname' => $firstname,
                'customers_lastname' => $lastname,
                'customers_email_address' => $customer_email,
                'customers_password' => xtc_encrypt_password(md5(date("Y-md"))),
                'customers_date_added' => 'now()',
                'customers_last_modified' => 'now()',
                'amazon_customer_id' => $customer_userid
            );
            
            xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);
            
            $_SESSION['customer_id'] = xtc_db_insert_id();
            $user_id                 = xtc_db_insert_id();
            xtc_write_user_info($user_id);
            $sql_data_array = array(
                'customers_id' => $_SESSION['customer_id'],
                'entry_firstname' => $firstname,
                'entry_lastname' => $lastname,
                'entry_street_address' => '',
                'entry_postcode' => $postcode,
                'entry_country_id' => 81,
                'address_date_added' => 'now()',
                'address_last_modified' => 'now()'
            );
            
            xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
            $address_id = xtc_db_insert_id();
            xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . $address_id . "' where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
            xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int) $_SESSION['customer_id'] . "', '0', now())");
            if (SESSION_RECREATE == 'True') {
                xtc_session_recreate();
            }
            $_SESSION['customer_first_name']         = $firstname;
            $_SESSION['customer_last_name']          = $lastname;
            $_SESSION['customer_default_address_id'] = $address_id;
            $_SESSION['customer_country_id']         = $country;
            $_SESSION['customer_zone_id']            = $zone_id;
            $_SESSION['customer_vat_id']             = $vat;
            $_SESSION['cart']->restore_contents();
            $name                  = $customer_name;
            $_SESSION['amazon_id'] = $_GET['amazon_id'];
            $goto                  = xtc_href_link('checkout_amazon_set_address.php', (isset($_GET["action"]) && $_GET["action"] == 'checkout') ? 'fromRedirect=1' : '', 'SSL');
            if ($handlerAction == 'redirectAuthentication') {
                xtc_redirect($goto);
            } else {
                echo $goto;
            }
        }
    }
    
    
    die();
    
} elseif ($handlerAction == 'setAddress') {
    if ($_GET['amazon_id']) {
        $_SESSION['amazon_id'] = $_GET['amazon_id'];
    }
    include_once('AmazonAdvancedPayment/.config.inc.php');
    include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
    // Service initialisieren
    $config                     = AlkimAmazonHandler::getConfig();
    $service                    = new OffAmazonPaymentsService_Client($config);
    $getOrderRefDetailsReqModel = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest(array(
        'AmazonOrderReferenceId' => $_SESSION['amazon_id'],
        'SellerId' => AWS_MERCHANT_ID
    ));
    $getOrderRefDetailsReqModel->setAddressConsentToken($_SESSION["amz_access_token"]);
    
    try {
        $getOrderRefDetailsResp = $service->getOrderReferenceDetails($getOrderRefDetailsReqModel);
    }
    catch (OffAmazonPaymentsService_Exception $e) {
        
    }
    
    $address           = $getOrderRefDetailsResp->GetOrderReferenceDetailsResult->getOrderReferenceDetails()->getDestination()->getPhysicalDestination();
    $cba_address_array = AlkimAmazonHandler::getAddressArrayFromAmzAddress($address);
    $q                 = "SELECT customers_default_address_id FROM " . TABLE_CUSTOMERS . " WHERE customers_id = " . (int) $_SESSION["customer_id"];
    $rs                = xtc_db_query($q);
    $r                 = xtc_db_fetch_array($rs);
    
    
    $address_book_sql_array = array(
        'entry_firstname' => $cba_address_array['firstname'],
        'entry_lastname' => $cba_address_array['lastname'],
        'entry_company' => $cba_address_array['company'],
        'entry_suburb' => $cba_address_array['suburb'],
        'entry_street_address' => $cba_address_array["street_address"],
        'entry_postcode' => $cba_address_array['postcode'],
        'entry_city' => $cba_address_array['city'],
        'entry_country_id' => $cba_address_array['country']["id"]
    );
    
    xtc_db_perform(TABLE_ADDRESS_BOOK, $address_book_sql_array, 'update', ' address_book_id = ' . (int) $r["customers_default_address_id"]);
    
    
    $customers_sql_array = array(
        'customers_firstname' => $cba_address_array['firstname'],
        'customers_lastname' => $cba_address_array['lastname']
    );
    
    xtc_db_perform(TABLE_CUSTOMERS, $customers_sql_array, 'update', ' customers_id = ' . (int) $_SESSION["customer_id"]);
    
    
    die;
    
    
}

if ($handlerAction == 'cot_gv_setter') {
    if ($_GET['cot_gv'] == '1') {
        $_SESSION['cot_gv_amazon'] = true;
        echo '1';
    } else {
        unset($_SESSION['cot_gv_amazon']);
        echo '0';
    }
    die;
}

if (isset($_SESSION['cot_gv_amazon']) && $_SESSION['cot_gv_amazon'] == true) {
    $_SESSION['cot_gv'] = true;
} else {
    unset($_SESSION['cot_gv']);
}

if ($handlerAction == 'setsession') {
    $_SESSION['amazon_id'] = $_GET['amazon_id'];
}


if ($_SESSION['amazon_id']) {
    $order = new order;
    $getOrderRefDetailsReqModel = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest(array(
        'AmazonOrderReferenceId' => $_SESSION['amazon_id'],
        'SellerId' => AWS_MERCHANT_ID
    ));
    
    
    try {
        $getOrderRefDetailsResp = $service->getOrderReferenceDetails($getOrderRefDetailsReqModel);
    }
    catch (OffAmazonPaymentsService_Exception $e) {
        
    }
    
    $dest = $getOrderRefDetailsResp->GetOrderReferenceDetailsResult->getOrderReferenceDetails();
    if (is_object($dest->getDestination())) {
        $iso_code                            = (string) $dest->getDestination()->getPhysicalDestination()->getCountryCode();
        $_SESSION['delivery_zone']           = $iso_code;
        $c1_query                            = xtc_db_query("SELECT * FROM " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . $iso_code . "' LIMIT 1");
        $c1_result                           = xtc_db_fetch_array($c1_query);
        $c2_query                            = xtc_db_query("SELECT * FROM " . TABLE_ZONES . " WHERE zone_country_id = '" . $c1_result['countries_id'] . "' LIMIT 1");
        $c2_result                           = xtc_db_fetch_array($c2_query);
        $order->delivery['country_id']       = $c1_result['countries_id'];
        $order->delivery['country']['id']    = $c1_result['countries_id'];
        $order->delivery['country']['iso_code_2'] = $iso_code;
        $_SESSION['AMZ_COUNTRY_ID']          = $c1_result['countries_id'];
        $order->delivery['country']['title'] = $c1_result['countries_name'];
        $order->delivery['zone_id']          = $c2_result['zone_id'];
        $_SESSION['AMZ_ZONE_ID']             = $c2_result['zone_id'];
    }
    if (AMZ_DOWNLOAD_ONLY == 'True') {
        $order->content_type = 'virtual';
    }
}else{
    echo 'ERROR: AmazonOrderReferenceId is not set';
    die;
}

if(empty($_GET["cba_select_shipping"]) && !empty($_SESSION["shipping"]["id"])){
    $_GET['cba_select_shipping'] = $_SESSION["shipping"]["id"];
}

if (!empty($_GET['cba_select_shipping'])) {

if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_virtual() == 0)) {
	$_SESSION['shipping'] = false;
	$_SESSION['sendto'] = false;
}

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();
$shipping_modules = new shipping;

if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
	switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
		case 'national' :
			if ($order->delivery['country_id'] == STORE_COUNTRY)
				$pass = true;
			break;
		case 'international' :
			if ($order->delivery['country_id'] != STORE_COUNTRY)
				$pass = true;
			break;
		case 'both' :
			$pass = true;
			break;
		default :
			$pass = false;
			break;
	}

	$free_shipping = false;
	if (($pass == true) && ($order->info['total'] - $order->info['shipping_cost'] >= $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, false, 0, true))) {
		$free_shipping = true;
        include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/order_total/ot_shipping.php');
	}
} else {
	$free_shipping = false;
}

	if ((xtc_count_shipping_modules() > 0) || ($free_shipping == true)) {
		if ((isset ($_GET['cba_select_shipping'])) && (strpos($_GET['cba_select_shipping'], '_'))) {
			$_SESSION['shipping'] = $_GET['cba_select_shipping'];
            list ($module, $method) = explode('_', $_SESSION['shipping']);
			if (is_object($$module) || ($_SESSION['shipping'] == 'free_free')) {
				if ($_SESSION['shipping'] == 'free_free') {
					$quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
					$quote[0]['methods'][0]['cost'] = '0';
				} else {
					$quote = $shipping_modules->quote($method, $module);
				}
				if (isset ($quote['error'])) {
					unset ($_SESSION['shipping']);
				} else {
					if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
						$_SESSION['shipping'] = array ('id' => $_SESSION['shipping'], 'title' => (($free_shipping == true) ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);
					}
				}
			} else {
				unset ($_SESSION['shipping']);
			}
		}
	} else {
		$_SESSION['shipping'] = false;
	}
}



// UPDATE 2014-06-30
$shipping_modules = new shipping($_SESSION['shipping']);
$order                            = new order();
$order->delivery['country']['id'] = $_SESSION['AMZ_COUNTRY_ID'];
$order->delivery['country']['iso_code_2'] = $iso_code;
$order->delivery['zone_id']       = $_SESSION['AMZ_ZONE_ID'];

$order_total_modules = new order_total();
$order_totals        = $order_total_modules->process();


if (AMZ_DOWNLOAD_ONLY == 'True') {
    $order->content_type = 'virtual';
}


if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = false;
    $_SESSION['sendto']   = false;
}



$productsRet = '';
$shippingRet = '';
$totalRet    = '';
$showButton  = true;

$total = 0;


if ($_SESSION['cart']->count_contents() > 0) {
    $products = $_SESSION['cart']->get_products();
    
    for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
        if (isset($products[$i]['attributes'])) {
            while (list($option, $value) = each($products[$i]['attributes'])) {
                $hidden_options .= xtc_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
                $attributes                                            = xtc_db_query("select popt.products_options_name,
											   poval.products_options_values_name,
											   pa.options_values_price,
											   pa.price_prefix,
											   pa.attributes_stock,
											   pa.products_attributes_id,
											   pa.attributes_model
										from " . TABLE_PRODUCTS_OPTIONS . " popt,
											 " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval,
											 " . TABLE_PRODUCTS_ATTRIBUTES . " pa
												where pa.products_id = '" . $products[$i]['id'] . "'
												and pa.options_id = '" . $option . "'
												and pa.options_id = popt.products_options_id
												and pa.options_values_id = '" . $value . "'
												and pa.options_values_id = poval.products_options_values_id
												and popt.language_id = '" . (int) $_SESSION['languages_id'] . "'
												and poval.language_id = '" . (int) $_SESSION['languages_id'] . "'");
                $attributes_values                                     = xtc_db_fetch_array($attributes);
                $products[$i][$option]['products_options_name']        = $attributes_values['products_options_name'];
                $products[$i][$option]['options_values_id']            = $value;
                $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
                $products[$i][$option]['options_values_price']         = $attributes_values['options_values_price'];
                $products[$i][$option]['price_prefix']                 = $attributes_values['price_prefix'];
                $products[$i][$option]['weight_prefix']                = $attributes_values['weight_prefix'];
                $products[$i][$option]['options_values_weight']        = $attributes_values['options_values_weight'];
                $products[$i][$option]['attributes_stock']             = $attributes_values['attributes_stock'];
                $products[$i][$option]['products_attributes_id']       = $attributes_values['products_attributes_id'];
                $products[$i][$option]['attributes_model']             = $attributes_values['attributes_model'];
            }
        }
    }
    
    
    $products_in_cart = array();
    $qty              = 0;
    for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
        
        $attribs          = array();
        $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);
        if ($attributes_exist == 1) {
            reset($products[$i]['attributes']);
            while (list($option, $value) = each($products[$i]['attributes'])) {
                if ($products[$i][$option]['products_options_name'] != '')
                    $attribs[] = htmlentities($products[$i][$option]['products_options_name'] . ': ' . $products[$i][$option]['products_options_values_name']);
            }
        }
        
        if (defined('IS_GAMBIO') && IS_GAMBIO == 1) {
            $coo_gprint_content_manager = new GMGPrintContentManager();
            $t_gm_gprint_data           = $coo_gprint_content_manager->get_content($products[$i]['id'], 'cart');
            
            for ($j = 0; $j < count($t_gm_gprint_data); $j++) {
                $attribs[] = $t_gm_gprint_data[$j]['NAME'] . ': ' . $t_gm_gprint_data[$j]['VALUE'];
                
            }
            
            $coo_properties_control = MainFactory::create_object('PropertiesControl');
            $coo_properties_view    = MainFactory::create_object('PropertiesView');
            
            #properties
            $t_properties       = '';
            $t_combis_id        = '';
            $t_properties_array = array();
            
            if (strpos($products[$i]['id'], 'x') !== false) {
                $t_combis_id = (int) substr($products[$i]['id'], strpos($products[$i]['id'], 'x') + 1);
            }
            if ($t_combis_id != '') {
                $t_properties = $coo_properties_view->get_order_details_by_combis_id($t_combis_id, 'cart');
                
                $t_properties_array = $coo_properties_view->v_coo_properties_control->get_properties_combis_details($t_combis_id, $_SESSION['languages_id']);
                
                if ($t_properties) {
                    $attribs[] = $t_properties;
                }
            }
            
        }
        
        
        
        if (sizeof($attribs) > 0) {
            $attribs = '<div class="amzAttr">' . join('<br />', $attribs) . '</div>';
        } else {
            $attribs = '';
        }
        
        $qty += $products[$i]['quantity'];
        
        $descs = xtc_db_query("SELECT products_short_description, products_description FROM products_description WHERE products_id = '" . (int) $products[$i]['id'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
        $descs = xtc_db_fetch_array($descs);
        $desc  = trim(strip_tags($descs['products_short_description']));
        if ($desc == '')
            $desc = trim(strip_tags($descs['products_description']));
        
        $maxLength = (AMZ_TEMPLATE == '2' ? 200 : 50);
        if (strlen($desc) > $maxLength) {
            $desc = substr($desc, 0, $maxLength) . '...';
        }
        $productRes = xtc_db_query("SELECT products_image FROM products WHERE products_id = '" . (int) $products[$i]['id'] . "'");
        $productRes = xtc_db_fetch_array($productRes);
        
        $image = '';
        if ($productRes['products_image'] != '') {
            $image = DIR_WS_THUMBNAIL_IMAGES . $productRes['products_image'];
        }
        
        $products_in_cart[] = array(
            'QTY' => (int) $products[$i]['quantity'],
            'LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($products[$i]['id'], $products[$i]['name'])),
            'NAME' => htmlentities($products[$i]['name']),
            'ATTRIBS' => $attribs,
            'ID' => $products[$i]['id'],
            'DESC' => $desc,
            'IMAGE' => $image,
            'SINGLEPRICE' => $xtPrice->xtcFormat($products[$i]['price'], true),
            'PRICE' => $xtPrice->xtcFormat($products[$i]['price'] * round($products[$i]['quantity'], 0), true)
        );
        $total              = $total + ($products[$i]['price'] * round($products[$i]['quantity'], 0));
    }
    
    
    if (sizeof($products_in_cart > 0)) {
        $productsRet .= '<table>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">' . AMZ_SINGLE_PRICE . '</td>
                                <td align="right">' . AMZ_TOTAL_PRICE . '</td>
                             </tr>';
        
        foreach ($products_in_cart as $key => $p) {
            
            $productsRet .= '<tr class="cba_item_list' . ($key % 2 == 0 ? '' : '_odd') . '" valign="top">
                            <td>
                                <div class="cba_item_img">' . ($p['IMAGE'] != '' ? '<img src="' . $p['IMAGE'] . '" />' : '') . '</div>
                            </td>
                            <td>
                                <div>' . $p['QTY'] . ' x
                                ' . ((defined('IS_GAMBIO') && IS_GAMBIO == 1) ? '<a class="lightbox_iframe" target="_blank" href="' . xtc_href_link('request_port.php', 'module=ProductDetails&id=' . $p['ID'], 'SSL') . '">' : '<a href="' . $p['LINK'] . '">') . $p['NAME'] . '</a>


                                </div>
                                <div>' . $p['ATTRIBS'] . '</div>
                                <div>' . $p['DESC'] . '</div>
                            </td>
                            <td align="right">
                                ' . trim(prepareMoneyInfo($p['SINGLEPRICE'])) . '
                            </td>
                            <td align="right">
                                ' . trim(prepareMoneyInfo($p['PRICE'])) . '
                            </td>
                          </tr>';
        }
        $productsRet .= '</table>';
    }
    
    
}


$eu = array(
    'BE',
    'GR',
    'CZ',
    'DK',
    'DE',
    'EE',
    'IE',
    'ES',
    'FR',
    'IT',
    'CY',
    'LV',
    'LT',
    'LU',
    'HU',
    'MT',
    'NL',
    'AT',
    'PL',
    'PT',
    'SI',
    'SK',
    'FI',
    'SE',
    'GB'
);



if ($order->content_type != 'virtual') {
    $allowed_zones = explode(',', constant(MODULE_PAYMENT_ . "AM_APA" . _ALLOWED));
    $isAllowed     = true;
    if (!in_array($iso_code, $allowed_zones) && (count($allowed_zones) > 1 || $allowed_zones[0] != '')) {
        $showButton = false;
        $isAllowed  = false;
        $shippingRet .= '<script> $("#paymenthinweis").show(); </script>';
    } else {
        $shippingRet .= '<script> $("#paymenthinweis").hide(); </script>';
        if (!in_array($iso_code, $eu)) {
            $shippingRet .= '<script> $("#zollhinweis").show(); </script>';
        } else {
            $shippingRet .= '<script> $("#zollhinweis").hide(); </script>';
        }
    }
    
    
    $_SESSION['delivery_zone']           = $iso_code;
    $c1_query                            = xtc_db_query("SELECT * FROM " . TABLE_COUNTRIES . " WHERE  countries_iso_code_2 = '" . $iso_code . "' LIMIT 1");
    $c1_result                           = xtc_db_fetch_array($c1_query);
    $c2_query                            = xtc_db_query("SELECT * FROM " . TABLE_ZONES . " WHERE  zone_country_id = '" . $c1_result['countries_id'] . "' LIMIT 1");
    $c2_result                           = xtc_db_fetch_array($c2_query);
    $order->delivery['country_id']       = $c1_result['countries_id'];
    $order->delivery['country']['id']    = $c1_result['countries_id'];
    $order->delivery['country']['title'] = $c1_result['countries_name'];
    $order->delivery['country']['iso_code_2'] = $iso_code;
    $order->delivery['zone_id']          = $c2_result['zone_id'];
    $order->delivery['postcode']         = (string) $amazon_address->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->PostalCode;
    
    // MwSt Fix start
    $_SESSION['customer_country_id'] = $c1_result['countries_id'];
    // MwSt Fix end
    


/****************************************************/
/******        SHIPPING METHODS OUTPUT         ******/
/****************************************************/

    if ($free_shipping == true) {
        $shippingRet .= '<input type="hidden" name="cba_allow_shipping" id="cba_allow_shipping" value="1" />';
        $shippingRet .= '<input type="hidden" name="cba_select_shipping" id="free" value="free_free" />';
        $shippingRet .= '<div style="font-size:11px;">' . FREE_SHIPPING_AT . ' ' . $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true) . '</div>';
    } else {

        # BUGFIX 2014-08-05 BOM  
        $shipping_modules                 = new shipping;
        # BUGFIX 2014-08-05 EOM  
        $quotes                           = $shipping_modules->quote();
        
        $count = 0;
        
        $freeamount = false;
        $n_quotes   = array();
        $excluded   = AlkimAmazonHandler::getExcludedShipping();
        foreach ($quotes as $key => $tmp_qoute) {
            if (in_array($quotes[$key]['id'], $excluded)) {
                unset($quotes[$key]);
                # BUGFIX 2014-08-05 BOM    
            } else {
                $t_allowed = trim(constant('MODULE_SHIPPING_' . strtoupper($quotes[$key]['id']) . '_ALLOWED'));
                if ($t_allowed != '') {
                    
                    if (strpos($t_allowed, ',') !== false) {
                        $allowed_zones = explode(',', $t_allowed);
                    } else {
                        $allowed_zones = array(
                            $t_allowed
                        );
                    }
                    
                } else {
                    $allowed_zones = array();
                }
                if (count($allowed_zones) > 0 && !in_array($_SESSION['delivery_zone'], $allowed_zones)) {
                    unset($quotes[$key]);
                }
            }
            # BUGFIX 2014-08-05 EOM
            ($quotes[$key]['id'] == 'freeamount') ? $freeamount = true : false;
            ($quotes[$key]['methods'][0]['cost'] <= 0) ? $n_quotes[] = $quotes[$key] : false;
        }
        
        if (sizeof($quotes > 0) && $isAllowed) {
            foreach ($quotes as $sh) {
                if (!isset($sh['error'])) {
                    if (isset($sh['methods'])) {
                        
                        $count++;
                        if ($count == 1) {
                            $select_string = ' checked ';
                            
                            $_SESSION['shipping'] = array(
                                'id' => $mid . '_' . $mid,
                                'title' => $sh['module'],
                                'cost' => $sh['methods'][0]['cost']
                            );
                        } else {
                            $select_string = '';
                        }
                        if ($sh['tax'] > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
                            $sh['methods'][0]['cost'] = xtc_add_tax($sh['methods'][0]['cost'], $sh['tax']);
                        }
                        $mid = $sh['methods'][0]['id'];
                        $shippingRet .= '<div style="float:left; width:20px;"><input ' . $select_string . ' type="radio" onchange="updateAmzBoxes()" name="cba_select_shipping" id="' . $mid . '" value="' . $sh['id'].'_'.$sh['methods'][0]['id'].'" /></div>';
                        $shippingRet .= '<div class="shMethod">';
                        $shippingRet .= $sh['module'] . ' (' . trim($xtPrice->xtcFormat($sh['methods'][0]['cost'], true)).')';

                        $shippingRet .= '</div>';
                        $shippingRet .= '<div style="clear:both;"></div>';
                    }
                } else {
                    $shippingRet .= '<div style="float:left; width:20px;">&nbsp;</div>';
                    $shippingRet .= '<div class="shMethod">';
                    $shippingRet .= $sh['module'] . '&nbsp;(' . $sh['error'] . ')';
                    $shippingRet .= '</div>';
                    $shippingRet .= '<div style="clear:both;"></div>';
                }
                
            }
            
        }
        // Keine gueltigen Versandmethoden
        if ($count == 0 || !$isAllowed) {
            $shippingRet .= '<input type="hidden" name="cba_allow_shipping" id="cba_allow_shipping" value="0" />';
            $shippingRet .= '<div>' . NO_SHIPPING_TO_ADDRESS . '</div>';
            $showButton = false;
        } else {
            $shippingRet .= '<input type="hidden" name="cba_allow_shipping" id="cba_allow_shipping" value="1" />';
        }
    }
} else {
    $shippingRet .= '<div>' . AMZ_VIRTUAL_TEXT . '</div>';
}



/****************************************************/
/******          ORDER TOTAL OUTPUT            ******/
/****************************************************/

$totalRet .= '<table>';
$i      = 0;
if (sizeof($order_totals) > 0) {
    foreach ($order_totals as $ot) {
        $totalRet .= '<tr class="amzOtLine">
                                <td class="amzOtTitle">' . $ot['title'] . '</td>
                                <td class="amzOtPrice">
                                     ' . prepareMoneyInfo($ot['text']) . '
                                </td>
                         </tr>';
        $i++;
    }
    
  
    
    if ($i == 0) {
        $totalRet .= '<tr class="amzOtLine"><td>' . NO_POSITIONS . '</td></tr>';
    }
}

$totalRet .= '</table>';

if (ACTIVATE_GIFT_SYSTEM == 'true') {
    $creditsel = $order_total_modules->credit_selection();
    
    if (isset($_SESSION['cot_gv_amazon']) && $_SESSION['cot_gv_amazon'] == true) {
        $_SESSION['cot_gv'] = true;
    } else {
        unset($_SESSION['cot_gv']);
    }
    $creditsel = str_replace('submitFunction', 'submitFunctionGV', $creditsel);
    $creditsel = str_replace(array(
        'nowrap="nowrap"',
        'nowrap'
    ), "", $creditsel);
    if (isset($_SESSION['cot_gv_amazon']) && $_SESSION['cot_gv_amazon'] == true) {
        $creditsel = str_replace('type="checkbox"', 'type="checkbox" checked="checked"', $creditsel);
    }
    $totalRet .= $creditsel;
}


echo json_encode(array(
    'shipping' => AlkimAmazonHandler::autoEncode($shippingRet),
    'products' => AlkimAmazonHandler::autoEncode($productsRet),
    'total' => AlkimAmazonHandler::autoEncode($totalRet),
    'showButton' => $showButton
));
die;

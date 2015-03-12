<?php
include ('includes/application_top.php');

# Include translations
include('lang/'.$_SESSION['language'].'/ajax_checkout.php');

# Create smarty elements
$smarty = new Smarty;

# Create checkout object
require (DIR_WS_CLASSES . 'ajax_checkout.php');
$checkout = new Checkout;

# required functions
require_once(DIR_FS_INC.'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once (DIR_FS_INC . 'xtc_get_products_image.inc.php');
require_once(DIR_FS_INC.'xtc_display_tax_value.inc.php');
require_once(DIR_FS_INC.'xtc_count_shipping_modules.inc.php');
require_once(DIR_FS_INC.'xtc_get_country_list.inc.php');
require_once(DIR_WS_CLASSES . 'order_total.php');
require_once(DIR_WS_CLASSES . 'order.php');
require_once(DIR_WS_CLASSES . 'payment.php');
require_once(DIR_WS_CLASSES . 'shipping.php');
if (!function_exists('json_encode')) {
  require_once('lib/jsonwrapper/jsonwrapper.php');
}

xtc_checkout_site('onestep');

# register a random ID in the session to check throughout the checkout procedure
# against alterations in the shopping cart contents
$_SESSION['cartID'] = $_SESSION['cart']->cartID;
$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();

# if no shipping destination address was selected, use the customers own address as default
if (!isset ($_SESSION['sendto'])) {
  $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
} else {
  # verify the selected shipping address
  $check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
  $check_address = xtc_db_fetch_array($check_address_query);

  if ($check_address['total'] != '1') {
    $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
    if (isset ($_SESSION['shipping'])) unset ($_SESSION['shipping']);
  }
}

# if no billing destination address was selected, use the customers own address as default
if (!isset($_SESSION['billto'])) {
  $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
} else {
  # verify the selected billing address
  $check_address_query = xtc_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int) $_SESSION['customer_id'] . "' and address_book_id = '" . (int) $_SESSION['billto'] . "'");
  $check_address = xtc_db_fetch_array($check_address_query);

  if ($check_address['total'] != '1') {
    $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
    if (isset($_SESSION['payment'])) {
      unset($_SESSION['checkout_payment_form_data']);
      unset($_SESSION['payment']);
    }
  }
}

if (!isset($_SESSION['sendto']) || $_SESSION['sendto'] == "") {
  $_SESSION['sendto'] = $_SESSION['billto'];
}


# --------------------- Handle ajax requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  include("includes/ajax_checkout_actions.php");
  die();
}


# Unset variables
$_SESSION['cot_gv'] = false;
unset($_SESSION['old_payment']);
unset($_SESSION['tmp_oID']);
if ($_SESSION['payment'] == 'no_payment') {
  unset($_SESSION['checkout_payment_form_data']);
  unset($_SESSION['payment']);
}

# check if checkout is allowed
if ($_SESSION['allow_checkout'] == 'false') {
  xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

# if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1) {
  xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

$order = new order;
if ($order->delivery['country']['iso_code_2'] != '') {
  $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
}

if (isset($_SESSION['credit_covers'])) {
  $_SESSION['payment'] = 'no_payment'; // GV Code Start/End ICW added for CREDIT CLASS
}

$order_total_modules = new order_total;
$shipping_modules = new shipping;
$payment_modules = new payment;

# SHOW PAYMENT ERROR
if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
  $smarty->assign('error', htmlspecialchars($error['error']));
}

# load all enabled shipping modules
$free_shipping = $checkout->isFreeShipping();
if ($free_shipping) {
  include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/order_total/ot_shipping.php');
}
$quotes = $shipping_modules->quote();
$payment_modules->update_status();

# Initial checks
$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
  for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
    if (xtc_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
      $any_out_of_stock = true;
    }
  }
  # Out of Stock
  if ((STOCK_ALLOW_CHECKOUT != 'true') && $any_out_of_stock) {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
  }
}

# Disable payment/shipping modules
if (!empty($_SESSION['shipping']['id']) && $_SESSION['shipping']['id'] != 'selfpickup_selfpickup') {
  if (is_object($GLOBALS['cash'])) $GLOBALS['cash']->enabled = false;
}
if (!empty($_SESSION['shipping']['id']) && $_SESSION['shipping']['id'] == 'selfpickup_selfpickup') {
  if (is_object($GLOBALS['cod'])) $GLOBALS['cod']->enabled = false;
}

# ORDER STUFF
$order = new order;
$order_total_modules = new order_total();
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();
$order_total_modules->process();

# LOGIN/REGISTER
$smarty->assign('LOGGED_IN', isset($_SESSION['customer_id']));
$smarty->assign('ACCOUNT_OPTIONS', ACCOUNT_OPTIONS);
$smarty->assign('REGISTER_GENDER', (ACCOUNT_GENDER == 'true'));
$smarty->assign('REGISTER_BIRTHDATE', (ACCOUNT_DOB == 'true'));
$smarty->assign('REGISTER_COMPANY', (ACCOUNT_COMPANY == 'true'));
$smarty->assign('REGISTER_VAT', (ACCOUNT_COMPANY_VAT_CHECK == 'true'));
$smarty->assign('REGISTER_SUBURB', (ACCOUNT_SUBURB == 'true'));
//--- XTC:Modified start
if (PROJECT_VERSION == 'xtcModified') {
  $smarty->assign('REGISTER_PRIVACY_CHECK', true);
  $smarty->assign('PRIVACY_LINK', $main->getContentLink(2, MORE_INFO, $request_type));
  $smarty->assign('REGISTER_EMAIL_CONFIRM', true);
}
// XTC:Modified end ---//

if (ACCOUNT_STATE == 'true') {
  $smarty->assign('REGISTER_STATE', true);
  $zones_array = array();
  $zones_query = xtc_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".xtc_db_input(STORE_COUNTRY)."' order by zone_name");
  while ($zones_values = xtc_db_fetch_array($zones_query)) {
    $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
  }
  if (count($zones_array) > 0) {
    $entry_state = xtc_draw_pull_down_menuNote(array('name' => 'state'), $zones_array);
    $smarty->assign('INPUT_STATE', $entry_state);
  }
}
$smarty->assign('SELECT_COUNTRY', xtc_get_country_list(array('name' => 'country'), STORE_COUNTRY, 'id="ajax-checkout-country"'));

# SHIPPING MODULES
$smarty->assign('NO_SHIPPINGS', !xtc_not_null(MODULE_SHIPPING_INSTALLED));
$smarty->assign('SHIPPING_BLOCK', $checkout->getShippingBlock());
$smarty->assign('FREE_SHIPPING', $free_shipping);
$smarty->assign('FREE_SHIPPING_DESCRIPTION', sprintf(FREE_SHIPPING_DESCRIPTION, $xtPrice->xtcFormat(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER, true, 0, true)).xtc_draw_hidden_field('shipping', 'free_free'));
$smarty->assign('FREE_SHIPPING_ICON', $quotes[$i]['icon']);

# PAYMENT MODULES
$no_payments = !xtc_not_null(MODULE_PAYMENT_INSTALLED);
$smarty->assign('NO_PAYMENTS', $no_payments);
$smarty->assign('PAYMENT_BLOCK', $checkout->getPaymentBlock());
$smarty->assign('GV_COVER', ($order->info['total'] <= 0));
if (ACTIVATE_GIFT_SYSTEM == 'true') {
  $smarty->assign('GIFT_MODULE', $order_total_modules->credit_selection());
}

# SHIPPING ADDRESS
$smarty->assign('IS_VIRTUAL', $checkout->isVirtual());
$smarty->assign('TEXT_VIRTUAL', CHECKOUT_TEXT_VIRTUAL);
$smarty->assign('SHIPPING_ADDRESS_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />'));
$smarty->assign('SHIPPING_ADDRESS_DROPDOWN', $checkout->getAddresses('shipping'));

# PAYMENT ADDRESS
$smarty->assign('PAYMENT_ADDRESS_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />'));
$smarty->assign('PAYMENT_ADDRESS_DROPDOWN', $checkout->getAddresses('payment'));

# BOTH
require (DIR_WS_MODULES.'ajax_checkout_new_address.php');
$smarty->assign('IS_NEW_ADDRESS_POSSIBLE', $checkout->isNewAddressPossible());

# IP
$smarty->assign('CUSTOMERS_IP', $checkout->getIp());

# PRODUCTS
$smarty->assign('PRODUCTS_BLOCK', $checkout->getProducts());
$smarty->assign('PRODUCTS_AMOUNT', $_SESSION['cart']->count_contents());

# COMMENTS
$smarty->assign('COMMENTS', $_SESSION['comments']);

# REVOCATION
$smarty->assign('REVOCATION', $checkout->getRevocation());
$smarty->assign('JS_REVOCATION_ERROR', json_encode(CHECKOUT_ERROR_REVOCATION));

# AGB
$smarty->assign('AGB', $checkout->getAGB());
$smarty->assign('JS_AGB_ERROR', json_encode(CHECKOUT_ERROR_CONDITIONS));

# LOGGED-OUT STUFF
$smarty->assign('HREF_REGISTER',        xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
$smarty->assign('HREF_LOGIN',           xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
$smarty->assign('FORM_ACTION_REGISTER', xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
$smarty->assign('FORM_ACTION_LOGIN',    xtc_href_link(FILENAME_LOGIN, 'action=process', 'SSL'));

# ORDER TOTAL + CHECKOUT BUTTON
$smarty->assign('FORM_ACTION_CHECKOUT', $checkout->getFormUrl());
$smarty->assign('ORDER_TOTAL',$checkout->getTotalBlock());
$smarty->assign('HIDDEN_PAYMENT', $checkout->getProcessButton());


# GENERAL
$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT);
require (DIR_WS_INCLUDES.'header.php');
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
$smarty->assign('half_box_css_class', CHECKOUT_AJAX_FULL_BOXES == 'true' ? 'box-full' : 'box-half');
$smarty->assign('JS_BOX_CONFIGS', json_encode($checkout->getBoxConfigs()));
$smarty->assign('JS_PAYMENT_FORM_DATA', json_encode($_SESSION['checkout_payment_form_data']));
$smarty->assign('SORTING', $checkout->getSorting());
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/ajax_checkout.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM)) {
  $smarty->loadfilter('output', 'note');
}
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>
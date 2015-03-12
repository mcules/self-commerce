<?php

/* -----------------------------------------------------------------------------------------
   $Id: checkout_confirmation.php 21 2012-06-10 15:22:06Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_confirmation.php,v 1.137 2003/05/07); www.oscommerce.com
   (c) 2003	 nextcommerce (checkout_confirmation.php,v 1.21 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   agree_conditions_1.01        	Autor:	Thomas Ploenkers (webmaster@oscommerce.at)

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC . 'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once (DIR_FS_INC . 'xtc_display_tax_value.inc.php');

// unset temporary order id when going back to confirmation to avoid order fraud
unset($_SESSION['tmp_oID']);
// unset temporary order id when going back to confirmation to avoid order fraud

//contact_us.php language file not needed any more, added constants to main language file
// Include "Single Price" in checkout_confirmation
//require (DIR_WS_LANGUAGES.$_SESSION['language'].'/checkout_confirmation.php');
// Include "Single Price" in checkout_confirmation
//contact_us.php language file not needed any more, added constants to main language file

// if the customer is not logged on, redirect them to the login page
if (!isset ($_SESSION['customer_id']))
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset ($_SESSION['cart']->cartID) && isset ($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID'])
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!isset ($_SESSION['shipping']))
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

//check if display conditions on checkout page is true

if (isset ($_POST['payment']))
    $_SESSION['payment'] = xtc_db_prepare_input($_POST['payment']);

if ($_POST['comments_added'] != '')
    $_SESSION['comments'] = xtc_db_prepare_input($_POST['comments']);

//-- TheMedia Begin check if display conditions on checkout page is true
if (isset ($_POST['cot_gv']))
    $_SESSION['cot_gv'] = true;
// if conditions are not accepted, redirect the customer to the payment method selection page

if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
    if (!isset($_POST['conditions']) || $_POST['conditions'] == false) {
        $error = str_replace('\n', '<br />', ERROR_CONDITIONS_NOT_ACCEPTED);
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($error), 'SSL', true, false));
    }
}

xtc_checkout_site('confirm');
// load the selected payment module
require_once (DIR_WS_CLASSES . 'payment.php');
if (isset ($_SESSION['credit_covers']) || !isset($_SESSION['payment'])) { //DokuMan - 2010-10-14 - check that payment is not yet set
    $_SESSION['payment'] = 'no_payment'; // GV Code Start/End ICW added for CREDIT CLASS
}
$payment_modules = new payment($_SESSION['payment']);

// GV Code ICW ADDED FOR CREDIT CLASS SYSTEM
require (DIR_WS_CLASSES . 'order_total.php');
require (DIR_WS_CLASSES . 'order.php');
$order = new order();

$payment_modules->update_status();

// GV Code Start
$order_total_modules = new order_total();
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();
// GV Code End

// moved up so GLOBALS is complete for OT process
// load the selected shipping module
require (DIR_WS_CLASSES . 'shipping.php');
$shipping_modules = new shipping($_SESSION['shipping']);
// moved up so GLOBALS is complete for OT process

//Process the Order Total Modules Earlier on the Checkout Confirmation Page
$order_total_modules->process();
//Process the Order Total Modules Earlier on the Checkout Confirmation Page

// GV Code line changed
if(isset($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') { //web28 - 2012-04-27 - fix for coupon amount == order total
    if ((is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && (!is_object($$_SESSION['payment'])) && (!isset ($_SESSION['credit_covers']))) || (is_object($$_SESSION['payment']) && ($$_SESSION['payment']->enabled == false))) {
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
    }
}

if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
}
// moved up so GLOBALS is complete for OT process
// load the selected shipping module
// require (DIR_WS_CLASSES . 'shipping.php');
// $shipping_modules = new shipping($_SESSION['shipping']);
// moved up so GLOBALS is complete for OT process

// Stock Check
$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        if (xtc_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
            $any_out_of_stock = true;
        }
    }
    // Out of Stock
    if ((STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true)) {
        xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
    }
}

$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_CONFIRMATION, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_CONFIRMATION);

require (DIR_WS_INCLUDES . 'header.php');
if (SHOW_IP_LOG == 'true') {
    $smarty->assign('IP_LOG', 'true');
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
        $customers_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $customers_ip = $_SERVER['REMOTE_ADDR'];
    }
    $smarty->assign('CUSTOMERS_IP', $customers_ip);
}
$smarty->assign('DELIVERY_LABEL', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
if (!isset($_SESSION['credit_covers']) || $_SESSION['credit_covers'] != '1') {
    $smarty->assign('BILLING_LABEL', xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
}
$smarty->assign('PRODUCTS_EDIT', xtc_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL')); // web28 - 2011-04-14 - change SSL -> NONSSL
$smarty->assign('SHIPPING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'));
$smarty->assign('BILLING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

if ($_SESSION['sendto'] != false) {
    if ($order->info['shipping_method']) {
        $smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']);
        $smarty->assign('SHIPPING_EDIT', xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
}

$data_products = '<table class="checkoutProducts" width="100%" border="0" cellspacing="0" cellpadding="0">';




$data_products.= '<tr>' . "\n" . '  <td class="main_header" align="left" style="padding-bottom: 5px;" valign="top"><strong>' . HEADER_QTY . '</strong></td>'
    . "\n" . '  <td class="main_header" align="left" valign="top"><strong>' . HEADER_ARTICLE . '</strong></td>'
    . "\n" . '  <td class="main_header" align="right" valign="top"><strong>' . HEADER_SINGLE . '</strong></td>'
    . "\n" . '  <td class="main_header" align="right" valign="top"><strong>' . HEADER_TOTAL . '</strong></td>
         </tr>' . "\n";

for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
    $data_products .= '<tr>' . "\n" . '  <td class="main_row" align="left" valign="top">' . $order->products[$i]['qty'] . ' x ' . '</td>'
        . "\n" . '  <td class="main_row" align="left" valign="top">' . $order->products[$i]['name'] . '</td>'
        . "\n"  . '  <td class="main_row" align="right" valign="top"><nobr>' . $xtPrice->xtcFormat($order->products[$i]['price'], true) . '</nobr></td>'
        . "\n"  . '  <td class="main_row" align="right" valign="top"><nobr>' . $xtPrice->xtcFormat($order->products[$i]['final_price'], true) . '</nobr></td>
             </tr>' . "\n";

	/* $data_products .= '<tr>
              <td class="main" align="left" valign="top">&nbsp;</td>
              <td class="description" class="main" align="left" valign="top"><small>' . (!empty($order->products[$i]['short_description'])?$order->products[$i]['short_description']:$order->products[$i]['description']). '</small></td>
              <td class="main" align="right" valign="top">&nbsp;</td>
              <td class="main" align="right" valign="top">&nbsp;</td>
              </tr>';*/

    if (ACTIVATE_SHIPPING_STATUS == 'true') {

        $data_products .= '<tr>
              <td class="main" align="left" valign="top">&nbsp;</td>
              <td class="main" align="left" valign="top">
              <nobr><small>' . SHIPPING_TIME . $order->products[$i]['shipping_time'] . '
              </small></nobr></td>
              <td class="main" align="right" valign="top">&nbsp;</td>
              <td class="main" align="right" valign="top">&nbsp;</td>
              </tr>';
    }
	$data_products .= '<tr>
						  <td colspan="4" class="main" align="left" valign="top">&nbsp;</td>
					  </tr>';


    if ((isset ($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++) {
            $data_products .= '<tr>
                          <td class="main" align="left" valign="top">&nbsp;</td>
                <td class="main" align="left" valign="top">
                <nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '
                </i></small></nobr></td>
                <td class="main" align="right" valign="top">&nbsp;</td>
                <td class="main" align="right" valign="top">&nbsp;</td></tr>';
        }
    }

    $data_products .= '' . "\n";

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
        if (sizeof($order->info['tax_groups']) > 1)
            $data_products .= '            <td class="main" valign="top" align="right">' . xtc_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }
    $data_products .= '</tr>' . "\n";
}
$data_products .= '</table>';
$smarty->assign('PRODUCTS_BLOCK', $data_products);

if ($order->info['payment_method'] != 'no_payment' && $order->info['payment_method'] != '') {
    include_once (DIR_WS_LANGUAGES . '/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
    $smarty->assign('PAYMENT_METHOD', constant('MODULE_PAYMENT_' . strtoupper($order->info['payment_method']) . '_TEXT_TITLE'));
}
$smarty->assign('PAYMENT_EDIT', xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

$total_block = '<table style="margin-top: 10px;" align="right">';
if (MODULE_ORDER_TOTAL_INSTALLED) {
    //Process the Order Total Modules Earlier on the Checkout Confirmation Page
    //$order_total_modules->process();
    //Process the Order Total Modules Earlier on the Checkout Confirmation Page
    $total_block .= $order_total_modules->output();
}
$total_block .= '</table><div style="clear:both"></div>';
$smarty->assign('TOTAL_BLOCK', $total_block);

if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {
        $payment_info = $confirmation['title'];
        if (isset($confirmation['fields'])) { //DokuMan - 2010-09-17 - Undefined index
            for ($i = 0, $n = sizeof($confirmation['fields']); $i < $n; $i++) {
                $confirmation_text= isset($confirmation['fields'][$i]['field']) ? stripslashes($confirmation['fields'][$i]['field']) : '&nbsp;';
                $payment_info .= '<table><tr>
                              <td>' . xtc_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                              <td class="main">' . $confirmation['fields'][$i]['title'] . '</td>
                              <td>' . xtc_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                              <td class="main">' . $confirmation_text . '</td>
                            </tr></table>';
            }
        }
        $smarty->assign('PAYMENT_INFORMATION', $payment_info);
    }
}

if (xtc_not_null($order->info['comments'])) {
    $smarty->assign('ORDER_COMMENTS', nl2br(htmlspecialchars($order->info['comments'])) . xtc_draw_hidden_field('comments', $order->info['comments']));
}

if (isset ($$_SESSION['payment']->form_action_url) && (!isset($$_SESSION['payment']->tmpOrders) || !$$_SESSION['payment']->tmpOrders)) {
    $form_action_url = $$_SESSION['payment']->form_action_url;
} else {
    $form_action_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
}
$smarty->assign('CHECKOUT_FORM', xtc_draw_form('checkout_confirmation', $form_action_url, 'post'));
$payment_button = '';

if (is_array($payment_modules->modules)) {
    $payment_button .= $payment_modules->process_button();
}
$smarty->assign('MODULE_BUTTONS', $payment_button);
$smarty->assign('CHECKOUT_BUTTON', xtc_image_submit('button_buy_now.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</form>' . "\n");

//check if display conditions on checkout page is true
if (DISPLAY_REVOCATION_ON_CHECKOUT == 'true') {

    if (GROUP_CHECK == 'true') {
        $group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
    }

    $shop_content_query = "SELECT
                             content_title,
                             content_heading,
                             content_text,
                             content_file
                             FROM " . TABLE_CONTENT_MANAGER . "
                             WHERE content_group='" . REVOCATION_ID . "'
                             " . $group_check . "
                             AND languages_id='" . $_SESSION['languages_id'] . "'
                             LIMIT 1"; //DokuMan - 2011-05-13 - added LIMIT 1

    $shop_content_query = xtc_db_query($shop_content_query);
    $shop_content_data = xtc_db_fetch_array($shop_content_query);

    if ($shop_content_data['content_file'] != '') {
        ob_start();
        if (strpos($shop_content_data['content_file'], '.txt'))
            echo '<pre>';
        include (DIR_FS_CATALOG . 'media/content/' . $shop_content_data['content_file']);
        if (strpos($shop_content_data['content_file'], '.txt'))
            echo '</pre>';
        $revocation = ob_get_contents();
        ob_end_clean();
    } else {
        $revocation = $shop_content_data['content_text'];
    }

    $smarty->assign('REVOCATION', $revocation);
    $smarty->assign('REVOCATION_TITLE', $shop_content_data['content_heading']);
    $smarty->assign('REVOCATION_LINK', $main->getContentLink(REVOCATION_ID, MORE_INFO,'SSL')); // Hetfield - 2009-07-29 - SSL for Content-Links per getContentLink

    $shop_content_query = "SELECT
                               content_title,
                               content_heading,
                               content_text,
                               content_file
                         FROM " . TABLE_CONTENT_MANAGER . "
                         WHERE content_group='3'
                         " . $group_check . "
                         AND languages_id='" . $_SESSION['languages_id'] . "'
                         LIMIT 1"; //DokuMan - 2011-05-13 - added LIMIT 1

    $shop_content_query = xtc_db_query($shop_content_query);
    $shop_content_data = xtc_db_fetch_array($shop_content_query);

    $smarty->assign('AGB_TITLE', $shop_content_data['content_heading']);
    $smarty->assign('AGB_LINK', $main->getContentLink(3, MORE_INFO,'SSL')); // Hetfield - 2009-07-29 - SSL for Content-Links per getContentLink
}

$smarty->assign('language', $_SESSION['language']);
//$smarty->assign('PAYMENT_BLOCK', $payment_block); //DokuMan - PAYMENT_BLOCK not needed in checkout_confimation
$main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_confirmation.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined('RM')) {
    $smarty->loadfilter('output', 'note');
}
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include ('includes/application_bottom.php');

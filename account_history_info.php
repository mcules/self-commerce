<?php

/* -----------------------------------------------------------------------------------------
   $Id: account_history_info.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(account_history_info.php,v 1.97 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC.'xtc_date_short.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_image_button.inc.php');
require_once (DIR_FS_INC.'xtc_display_tax_value.inc.php');
require_once (DIR_FS_INC.'xtc_format_price_order.inc.php');

//security checks
if (!isset ($_SESSION['customer_id'])) { xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL')); }
if (!isset ($_GET['order_id']) || (isset ($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
   xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}
$customer_info_query = xtc_db_query("select customers_id from ".TABLE_ORDERS." where orders_id = '".(int) $_GET['order_id']."'");
$customer_info = xtc_db_fetch_array($customer_info_query);
if ($customer_info['customers_id'] != $_SESSION['customer_id']) { xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL')); }

$breadcrumb->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO, xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$breadcrumb->add(sprintf(NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO, (int)$_GET['order_id']), xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id='.(int)$_GET['order_id'], 'SSL'));

require (DIR_WS_CLASSES.'order.php');
$order = new order((int)$_GET['order_id']);
require (DIR_WS_INCLUDES.'header.php');

// Delivery Info
if ($order->delivery != false) {
	$smarty->assign('DELIVERY_LABEL', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
	if ($order->info['shipping_method']) { $smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']); }
}

$smarty->assign('order_data', $order->getOrderData($order->info['order_id']));

// Products data
$data_products = '<table style="width: 100%; border: none; padding: 0;">';
for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {
	$data_products .= '          <tr>'."\n".'            <td style="text-align: left; vertical-align: top; white-space: nowrap;">'.$order->products[$i]['qty'].' x '.$order->products[$i]['name'].'</td>'."\n".'
	<td style="text-align: left; vertical-align: top;">'.xtc_format_price_order($order->products[$i]['price'], 1, $order->info['currency']).'</td></tr>'."\n";

	if ((isset ($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0)) {
		for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j ++) {
			$data_products .= '<tr>
			        <td style="text-align: left; vertical-align: top; white-space: nowrap;">
			        <small>&nbsp;<i> - '.$order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value'].'</i></small></td>
			        <td style="text-align: right; vertical-align: top; white-space: nowrap;"></td></tr>';
		}
	}

	$data_products .= "\n";

	if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
		if (sizeof($order->info['tax_groups']) > 1)
			$data_products .= '            <td style="text-align: right; vertical-align: top; white-space: nowrap;">'.xtc_display_tax_value($order->products[$i]['tax']).'%</td>'."\n";
	}
}
$data_products .= '</table>';
$smarty->assign('PRODUCTS_BLOCK', $data_products);

// Payment Method
if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
	include (DIR_WS_LANGUAGES.'/'.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
	$smarty->assign('PAYMENT_METHOD', constant(MODULE_PAYMENT_.strtoupper($order->info['payment_method'])._TEXT_TITLE));
}

// Order-Total Info
$total_block = '<table summary="layout">';
for ($i = 0, $n = sizeof($order->totals); $i < $n; $i ++) {
	$total_block .= '            <tr>'."\n".'                <td style="text-align: right; width:100%;">'.$order->totals[$i]['title'].'</td>'."\n".'                <td class="main" nowrap align="right">'.$order->totals[$i]['text'].'</td>'."\n".'              </tr>'."\n";
}
$total_block .= '</table>';
$smarty->assign('TOTAL_BLOCK', $total_block);

// Order History
$history_block = '<table summary="order history">';
$statuses_query = xtc_db_query("select os.orders_status_name, osh.date_added, osh.comments from ".TABLE_ORDERS_STATUS." os, ".TABLE_ORDERS_STATUS_HISTORY." osh where osh.orders_id = '".(int) $_GET['order_id']."' and osh.orders_status_id = os.orders_status_id and os.language_id = '".(int) $_SESSION['languages_id']."' order by osh.date_added");
while ($statuses = xtc_db_fetch_array($statuses_query)) {
	$history_block .= '              <tr>'."\n".'                <td style="vertical-align:top;">'.xtc_date_short($statuses['date_added']).'</td>'."\n".'                <td style="vertical-align:top;">'.$statuses['orders_status_name'].'</td>'."\n".'                <td style="vertical-align:top;">'. (empty ($statuses['comments']) ? '&nbsp;' : nl2br(htmlspecialchars($statuses['comments']))).'</td>'."\n".'              </tr>'."\n";
}
$history_block .= '</table>';
$smarty->assign('HISTORY_BLOCK', $history_block);

// Download-Products
if (DOWNLOAD_ENABLED == 'true') include (DIR_WS_MODULES.'downloads.php');

// Stuff
$smarty->assign('ORDER_NUMBER', (int)$_GET['order_id']);
$smarty->assign('ORDER_DATE', xtc_date_long($order->info['date_purchased']));
$smarty->assign('ORDER_STATUS', $order->info['orders_status']);
$smarty->assign('BILLING_LABEL', xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
$smarty->assign('PRODUCTS_EDIT', xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$smarty->assign('SHIPPING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL'));
$smarty->assign('BILLING_ADDRESS_EDIT', xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));
/* $smarty->assign('BUTTON_PRINT', '<a style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_ORDER, 'oID='.(int)$_GET['order_id']).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\')"><img src="'.'templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/button_print.gif" alt="'.TEXT_PRINT.'" /></a>'); */
$smarty->assign('BUTTON_PRINT', xtc_image_button('button_print.gif', TEXT_PRINT, 'style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_ORDER, 'oID='.(int)$_GET['order_id']).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\')"'));
$from_history = preg_match("/page=/", xtc_get_all_get_params()); // referer from account_history yes/no
$back_to = $from_history ? FILENAME_ACCOUNT_HISTORY : FILENAME_ACCOUNT; // if from account_history => return to account_history
$smarty->assign('BUTTON_BACK','<a href="' . xtc_href_link($back_to,xtc_get_all_get_params(array ('order_id')), 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>');

$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/account_history_info.html');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM)) { $smarty->loadfilter('output', 'note'); }
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>

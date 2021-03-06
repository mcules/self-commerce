<?php

/* -----------------------------------------------------------------------------------------
   $Id: send_order.php 46 2012-07-30 12:06:11Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (send_order.php,v 1.1 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC.'xtc_get_order_data.inc.php');
require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');
// check if customer is allowed to send this order!
$order_query_check = xtc_db_query("SELECT
  					customers_id
  					FROM ".TABLE_ORDERS."
  					WHERE orders_id='".$insert_id."'");

$order_check = xtc_db_fetch_array($order_query_check);
if ($_SESSION['customer_id'] == $order_check['customers_id']  || $send_by_amazon) {

	$order = new order($insert_id);

// Paypal Express Modul Start
	if ($_SESSION['paypal_express_new_customer'] == 'true' && $_SESSION['ACCOUNT_PASSWORD']== 'true') {
		require_once (DIR_FS_INC.'xtc_create_password.inc.php');
		require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
		$password_encrypted =  xtc_RandomString(10);
		$password = xtc_encrypt_password($password_encrypted);
		xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_password = '" . $password . "' where customers_id = '" . (int) $_SESSION['customer_id'] . "'");
		$smarty->assign('NEW_PASSWORD', $password_encrypted);
	}
// Paypal Express Modul Ende

	$smarty->assign('address_label_customer', xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$smarty->assign('address_label_shipping', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	if ($_SESSION['credit_covers'] != '1') {
		$smarty->assign('address_label_payment', xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
	}
	$smarty->assign('csID', $order->customer['csID']);
	// get products data
	$order_query = xtc_db_query("SELECT
	        				products_id,
	        				orders_products_id,
	        				products_model,
	        				products_name,
	        				final_price,
	        				products_quantity
	        				FROM ".TABLE_ORDERS_PRODUCTS."
	        				WHERE orders_id='".$insert_id."'");
	$order_data = array ();
	while ($order_data_values = xtc_db_fetch_array($order_query)) {
		$attributes_query = xtc_db_query("SELECT
		        				products_options,
		        				products_options_values,
		        				price_prefix,
		        				options_values_price
		        				FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
		        				WHERE orders_products_id='".$order_data_values['orders_products_id']."'");
		$attributes_data = '';
		$attributes_model = '';
		while ($attributes_data_values = xtc_db_fetch_array($attributes_query)) {
			$attributes_data .= $attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'].'<br />';
			$attributes_model .= xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values']).'<br />';
		}
		$order_data[] = array ('PRODUCTS_MODEL' => $order_data_values['products_model'], 'PRODUCTS_NAME' => $order_data_values['products_name'], 'PRODUCTS_ATTRIBUTES' => $attributes_data, 'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model, 'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'], true),'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price']/$order_data_values['products_quantity'], true), 'PRODUCTS_QTY' => $order_data_values['products_quantity']);
	}
	// get order_total data
	$oder_total_query = xtc_db_query("SELECT
	  					title,
	  					text,
	  					sort_order
	  					FROM ".TABLE_ORDERS_TOTAL."
	  					WHERE orders_id='".$insert_id."'
	  					ORDER BY sort_order ASC");

	$order_total = array ();
	while ($oder_total_values = xtc_db_fetch_array($oder_total_query)) {

		$order_total[] = array ('TITLE' => $oder_total_values['title'], 'TEXT' => $oder_total_values['text']);
	}

	// assign language to template for caching
	$smarty->assign('language', $_SESSION['language']);
	$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
	$smarty->assign('oID', $insert_id);
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
		include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$smarty->assign('PAYMENT_METHOD', $payment_method);
	$smarty->assign('DATE', xtc_date_long($order->info['date_purchased']));
	$smarty->assign('order_data', $order_data);
	$smarty->assign('order_total', $order_total);
	$smarty->assign('NAME', $order->customer['name']);
	$smarty->assign('COMMENTS', $order->info['comments']);
	$smarty->assign('EMAIL', $order->customer['email_address']);
	$smarty->assign('PHONE',$order->customer['telephone']);

// PayPal Bezahl-Linkerweiterung
    $smarty->assign('CURRENCY1',$order->info['currency']);
    $tmp1 = preg_replace ( '/,/i', '.' , $order->info['total'] );
    $tmp2 = floatval ($tmp1);
    $smarty->assign('TOTAL1',$tmp2);
//Ende PayPal Bezahl-Linkerweiterung


	// PAYMENT MODUL TEXTS
	// EU Bank Transfer
	if ($order->info['payment_method'] == 'eustandardtransfer') {
		$smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION);
		$smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION));
	}

	// MONEYORDER
	if ($order->info['payment_method'] == 'moneyorder') {
		$smarty->assign('PAYMENT_INFO_HTML', MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION);
		$smarty->assign('PAYMENT_INFO_TXT', str_replace("<br />", "\n", MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION));
	}

	// dont allow cache
	$smarty->caching = false;

	$html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/order_mail.html');
	$txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/order_mail.txt');

	// create subject
	$order_subject = str_replace('{$nr}', $insert_id, EMAIL_BILLING_SUBJECT_ORDER);
	$order_subject = str_replace('{$date}', strftime(DATE_FORMAT_LONG), $order_subject);
	$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
	$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

	// send mail to admin
	xtc_php_mail($order->customer['email_address'], $order->customer['firstname'], EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);

	if (ATTACH_ORDER_1 != '') {
		$attachment1 = 'attachments/'.ATTACH_ORDER_1 ;
	} else {
		$attachment1 = '';
	}

	if (ATTACH_ORDER_2 != '') {
		$attachment2 = 'attachments/'.ATTACH_ORDER_2 ;
	} else {
		$attachment2 = '';
	}
	// send mail to customer
    xtc_php_mail(EMAIL_BILLING_ADDRESS, $order->customer['firstname'], EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'], $order->customer['firstname'], '', '', $order_subject, $html_mail, $txt_mail);

	if (AFTERBUY_ACTIVATED == 'true') {
		require_once (DIR_WS_CLASSES.'afterbuy.php');
		$aBUY = new xtc_afterbuy_functions($insert_id);
		if ($aBUY->order_send())
			$aBUY->process_order();
	}

} else {
	$smarty->assign('ERROR', 'You are not allowed to view this order!');
	$smarty->display(CURRENT_TEMPLATE.'/module/error_message.html');
}
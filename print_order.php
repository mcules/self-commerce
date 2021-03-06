<?php

/* -----------------------------------------------------------------------------------------
   $Id: print_order.php 17 2012-06-04 20:33:29Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (print_order.php,v 1.5 2003/08/24); www.nextcommerce.org
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

// include needed functions
require_once (DIR_FS_INC.'xtc_get_order_data.inc.php');
require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');


$smarty = new Smarty;

// check if custmer is allowed to see this order!
$order_query_check = xtc_db_query("SELECT
  					customers_id
  					FROM ".TABLE_ORDERS."
  					WHERE orders_id='".(int) $_GET['oID']."'");

$order_check = xtc_db_fetch_array($order_query_check);
if ($_SESSION['customer_id'] == $order_check['customers_id']) {
	// get order data

	include (DIR_WS_CLASSES.'order.php');
	$order = new order($_GET['oID']);
	$smarty->assign('address_label_customer', xtc_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'));
	$smarty->assign('address_label_shipping', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'));
	$smarty->assign('address_label_payment', xtc_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'));
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
	        				WHERE orders_id='".(int) $_GET['oID']."'");
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
			$attributes_data .= '<br />'.$attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'];
			$attributes_model .= '<br />'.xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values']);
		}
		$order_data[] = array ('PRODUCTS_MODEL' => $order_data_values['products_model'], 'PRODUCTS_NAME' => $order_data_values['products_name'], 'PRODUCTS_ATTRIBUTES' => $attributes_data, 'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model, 'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'], true),'PRODUCTS_SINGLE_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price']/$order_data_values['products_quantity'], true), 'PRODUCTS_QTY' => $order_data_values['products_quantity']);
	}
	// get order_total data
	$oder_total_query = xtc_db_query("SELECT
	  					title,
	  					text,
	                    class,
	                    value,
	  					sort_order
	  					FROM ".TABLE_ORDERS_TOTAL."
	  					WHERE orders_id='".(int) $_GET['oID']."'
	  					ORDER BY sort_order ASC");

	$order_total = array ();
	while ($oder_total_values = xtc_db_fetch_array($oder_total_query)) {

		$order_total[] = array ('TITLE' => $oder_total_values['title'], 'CLASS' => $oder_total_values['class'], 'VALUE' => $oder_total_values['value'], 'TEXT' => $oder_total_values['text']);
		if ($oder_total_values['class'] = 'ot_total')
			$total = $oder_total_values['value'];
	}

	// assign language to template for caching
	$smarty->assign('language', $_SESSION['language']);
	$smarty->assign('oID', (int) $_GET['oID']);
	if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
		include (DIR_WS_LANGUAGES.$_SESSION['language'].'/modules/payment/'.$order->info['payment_method'].'.php');
		$payment_method = constant(strtoupper('MODULE_PAYMENT_'.$order->info['payment_method'].'_TEXT_TITLE'));
	}
	$smarty->assign('PAYMENT_METHOD', $payment_method);
	$smarty->assign('COMMENT', $order->info['comments']);
	$smarty->assign('DATE', xtc_date_long($order->info['date_purchased']));
	$smarty->assign('order_data', $order_data);
	$smarty->assign('order_total', $order_total);
	$path = DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/';
	$smarty->assign('tpl_path', $path);

	// dont allow cache
	$smarty->caching = false;

	$smarty->display(CURRENT_TEMPLATE.'/module/print_order.html');
} else {

	$smarty->assign('ERROR', 'You are not allowed to view this order!');
	$smarty->display(CURRENT_TEMPLATE.'/module/error_message.html');
}
?>
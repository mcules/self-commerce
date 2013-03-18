<?php
/* -----------------------------------------------------------------------------------------
   $Id: shopping_cart.php 63 2012-10-20 17:29:32Z McUles $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.71 2003/02/14); www.oscommerce.com
   (c) 2003	 nextcommerce (shopping_cart.php,v 1.24 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$cart_empty = false;
require "includes/application_top.php";

// create smarty elements
$smarty = new Smarty;
require DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php';

// include needed functions
require_once DIR_FS_INC.'xtc_array_to_string.inc.php';
require_once DIR_FS_INC.'xtc_image_submit.inc.php';

$breadcrumb->add(NAVBAR_TITLE_SHOPPING_CART, xtc_href_link(FILENAME_SHOPPING_CART));

require DIR_WS_INCLUDES.'header.php';
include DIR_WS_MODULES.'gift_cart.php';

// Paypal Express Modul Start
if(!isset($_SESSION['paypal_warten']))
	include (DIR_WS_MODULES.'gift_cart.php');
// PayPal Express abgelehnt und erneut aufrufen!
if(isset($_SESSION['reshash']['ACK']) AND strtoupper($_SESSION['reshash']['ACK'])!="SUCCESS" AND strtoupper($_SESSION['reshash']['ACK'])!="SUCCESSWITHWARNING"):
	if(isset($_SESSION['reshash']['REDIRECTREQUIRED'])  && strtoupper($_SESSION['reshash']['REDIRECTREQUIRED'])=="TRUE"):
		require (DIR_WS_CLASSES.'payment.php');
		$payment_modules = new payment($_SESSION['payment']);
		$_SESSION['paypal_fehler']=((PAYPAL_FEHLER)?PAYPAL_FEHLER:'PayPal Fehler...<br />');
		$_SESSION['paypal_warten']=((PAYPAL_WARTEN)?PAYPAL_WARTEN:'Sie müssen noch einmal zu PayPal. <br />');
		$payment_modules->giropay_process();
	endif;
endif;
unset($_SESSION['paypal_express_checkout']);
// Paypal Error Messages:
if(isset($_SESSION['paypal_fehler']) AND !isset($_SESSION['paypal_warten'])):
	if(!isset($_SESSION['reshash']['ACK'])):
		$o_paypal->paypal_second_auth_call($_SESSION['tmp_oID']);
		xtc_redirect($o_paypal->payPalURL);
	endif;
	if(isset($_SESSION['reshash']['ACK']) AND (strtoupper($_SESSION['reshash']['ACK'])=="SUCCESS" OR strtoupper($_SESSION['reshash']['ACK'])=="SUCCESSWITHWARNING")):
		$o_paypal->paypal_get_customer_data();
		if($data['PayerID'] OR $_SESSION['reshash']['PAYERID']):
			require (DIR_WS_CLASSES.'order.php');
			$data = array_merge($_SESSION['nvpReqArray'],$_SESSION['reshash']);
			if(is_array($_GET))$data = array_merge($data,$_GET);
			$o_paypal->complete_ceckout($_SESSION['tmp_oID'],$data);
			$o_paypal->write_status_history($_SESSION['tmp_oID']);
			$o_paypal->logging_status($_SESSION['tmp_oID']);
		endif;
	endif;
	$_SESSION['cart']->reset(true);
	// unregister session variables used during checkout
	$last_order =$_SESSION['tmp_oID'];
	unset ($_SESSION['sendto']);
	unset ($_SESSION['billto']);
	unset ($_SESSION['shipping']);
	unset ($_SESSION['comments']);
//	unset ($_SESSION['last_order']);
	unset ($_SESSION['tmp_oID']);
	unset ($_SESSION['cc']);
	//GV Code Start
	if (isset ($_SESSION['credit_covers']))
		unset ($_SESSION['credit_covers']);
	require (DIR_WS_CLASSES.'order_total.php');
	$order_total_modules = new order_total();
	$order_total_modules->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
	// GV Code End
	if(isset($_SESSION['reshash']['ACK']) AND (strtoupper($_SESSION['reshash']['ACK'])=="SUCCESS" OR strtoupper($_SESSION['reshash']['ACK'])=="SUCCESSWITHWARNING")):
		$redirect=((isset($_SESSION['reshash']['REDIRECTREQUIRED'])  && strtoupper($_SESSION['reshash']['REDIRECTREQUIRED'])=="TRUE")?true:false);
		$o_paypal->paypal_get_customer_data();
		if($data['PayerID'] OR $_SESSION['reshash']['PAYERID']):
			if($redirect):
				unset($_SESSION['paypal_fehler']);
				require (DIR_WS_CLASSES.'payment.php');
				$payment_modules = new payment('paypalexpress');
				$payment_modules->giropay_process();
			endif;
			$weiter=true;
		endif;
		unset($_SESSION['payment']);
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
		if($weiter):
			unset($_SESSION['paypal_fehler']);
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
		endif;
	else:
		unset($_SESSION['payment']);
		unset($_SESSION['nvpReqArray']);
		unset($_SESSION['reshash']);
	endif;
	$smarty->assign('error', $_SESSION['paypal_fehler']);
	unset($_SESSION['paypal_fehler']);
endif;
// Paypal Express Modul Ende

if ($_SESSION['cart']->count_contents() > 0) {
	// Paypal Express Modul Start
	if(!isset($_SESSION['paypal_warten'])){
		// Normaler Warenkorb
	// Paypal Express Modul Ende
	$smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')));

	$smarty->assign('FORM_END', '</form>');
	$hidden_options = '';
	$_SESSION['any_out_of_stock'] = 0;

	$products = $_SESSION['cart']->get_products();
	for ($i = 0, $n = sizeof($products); $i < $n; $i ++)
	{
		// Push all attributes information in an array
		if (isset ($products[$i]['attributes']) && is_array($products[$i]['attributes']))
		{
			while (list ($option, $value) = each($products[$i]['attributes']))
			{
				$hidden_options .= xtc_draw_hidden_field('id['.$products[$i]['id'].']['.$option.']', $value);
				$Sql = "SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock,pa.products_attributes_id,pa.attributes_model
						FROM ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_OPTIONS_VALUES." poval, ".TABLE_PRODUCTS_ATTRIBUTES." pa
						WHERE pa.products_id = '".$products[$i]['id']."'
							and pa.options_id = '".$option."'
							and pa.options_id = popt.products_options_id
							and pa.options_values_id = '".$value."'
							and pa.options_values_id = poval.products_options_values_id
							and popt.language_id = '".(int) $_SESSION['languages_id']."'
							and poval.language_id = '".(int) $_SESSION['languages_id']."'";
				$attributes = xtc_db_query($Sql);
				$attributes_values = xtc_db_fetch_array($attributes);

				$products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
				$products[$i][$option]['options_values_id'] = $value;
				$products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
				$products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
				$products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
				$products[$i][$option]['weight_prefix'] = $attributes_values['weight_prefix'];
				$products[$i][$option]['options_values_weight'] = $attributes_values['options_values_weight'];
				$products[$i][$option]['attributes_stock'] = $attributes_values['attributes_stock'];
				$products[$i][$option]['products_attributes_id'] = $attributes_values['products_attributes_id'];
				$products[$i][$option]['products_attributes_model'] = $attributes_values['products_attributes_model'];
			}
		}
	}

	$smarty->assign('HIDDEN_OPTIONS', $hidden_options);
	require DIR_WS_MODULES.'order_details_cart.php';
	$_SESSION['allow_checkout'] = 'true';
	if (STOCK_CHECK == 'true')
	{
		if ($_SESSION['any_out_of_stock'] == 1)
		{
			if (STOCK_ALLOW_CHECKOUT == 'true')
			{
				// write permission in session
				$_SESSION['allow_checkout'] = 'true';

				$smarty->assign('info_message', OUT_OF_STOCK_CAN_CHECKOUT);

			} else
			{
				$_SESSION['allow_checkout'] = 'false';
				$smarty->assign('info_message', OUT_OF_STOCK_CANT_CHECKOUT);

			}
		} else
		{
			$_SESSION['allow_checkout'] = 'true';
		}
	}

	// Paypal Express Modul Start
	}
	else {
		// 2. PayPal Aufruf - nur anzeigen
		require (DIR_WS_CLASSES.'order.php');
		$order = new order((int)$_SESSION['tmp_oID']);
		$smarty->assign('language', $_SESSION['language']);
		if ($order->delivery != false) {
			$smarty->assign('DELIVERY_LABEL', xtc_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'));
			if ($order->info['shipping_method']) { $smarty->assign('SHIPPING_METHOD', $order->info['shipping_method']); }
		}
		$order_total = $order->getTotalData((int)$_SESSION['tmp_oID']);
		$smarty->assign('order_data', $order->getOrderData((int)$_SESSION['tmp_oID']));
		$smarty->assign('order_total', $order_total['data']);
		$smarty->assign('BILLING_LABEL', xtc_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'));
		$smarty->assign('ORDER_NUMBER',$_SESSION['tmp_oID']);
		$smarty->assign('ORDER_DATE', xtc_date_long($order->info['date_purchased']));
		$smarty->assign('ORDER_STATUS', $order->info['orders_status']);
		$history_block = '<table summary="order history">';
		$order_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/account_history_info.html');
		$smarty->assign('info_message_1', $order_content);
		$smarty->assign('FORM_ACTION', '<br />'.$o_paypal->build_express_fehler_button().'<br />'.PAYPAL_NEUBUTTON);
	}
	if(isset($_SESSION['reshash']['FORMATED_ERRORS'])){
		$smarty->assign('error', $_SESSION['reshash']['FORMATED_ERRORS']);
	}
	// Paypal Express Modul Ende

	// minimum/maximum order value
	$checkout = true;
	if ($_SESSION['cart']->show_total() > 0 )
	{
		if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] )
		{
			$_SESSION['allow_checkout'] = 'false';
			$more_to_buy	= $_SESSION['customers_status']['customers_status_min_order'] - $_SESSION['cart']->show_total();
			$order_amount	= $xtPrice->xtcFormat($more_to_buy, true);
			$min_order		= $xtPrice->xtcFormat($_SESSION['customers_status']['customers_status_min_order'], true);
			$smarty->assign('info_message_1', MINIMUM_ORDER_VALUE_NOT_REACHED_1);
			$smarty->assign('info_message_2', MINIMUM_ORDER_VALUE_NOT_REACHED_2);
			$smarty->assign('order_amount', $order_amount);
			$smarty->assign('min_order', $min_order);
		}
		if  ($_SESSION['customers_status']['customers_status_max_order'] != 0)
		{
			if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] )
			{
				$_SESSION['allow_checkout'] = 'false';
				$less_to_buy	= $_SESSION['cart']->show_total() - $_SESSION['customers_status']['customers_status_max_order'];
				$max_order		= $xtPrice->xtcFormat($_SESSION['customers_status']['customers_status_max_order'], true);
				$order_amount	= $xtPrice->xtcFormat($less_to_buy, true);
				$smarty->assign('info_message_1', MAXIMUM_ORDER_VALUE_REACHED_1);
				$smarty->assign('info_message_2', MAXIMUM_ORDER_VALUE_REACHED_2);
				$smarty->assign('order_amount', $order_amount);
				$smarty->assign('min_order', $max_order);
			}
		}
	}

// Paypal Express Modul Start
	if(isset($_SESSION['paypal_warten'])):
		$smarty->assign('error', $_SESSION['paypal_warten']);
	else:
		if ($_GET['info_message'])
			$smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
		$smarty->assign('BUTTON_PAYPAL', $o_paypal->build_express_checkout_button());
		$smarty->assign('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART));
		$smarty->assign('BUTTON_CHECKOUT', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT).'</a>');
	endif;
// Paypal Express Modul Ende

	/**
	 * Warenkorbartikel anzeigen -> Start
	 */
	$Sql = "SELECT p.products_id, pd.products_name, p.products_image, p.products_tax_class_id, p.products_price
			FROM products_to_categories ptc, categories_description cd, products p, products_description pd
            WHERE cd.categories_name='Warenkorb'
            	AND cd.language_id=2
                AND ptc.categories_id=cd.categories_id
                AND p.products_id=ptc.products_id
                AND pd.products_id=p.products_id
                AND pd.language_id='".$_SESSION['languages_id']."'";
	$special_query = xtc_db_query($Sql);

	if (xtc_db_num_rows($special_query))
	{
		$module_content = array ();
		while ($special = xtc_db_fetch_array($special_query))
		{
			$special_image = '<a href="'.xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array ('action')).'action=buy_now&BUYproducts_id='.$special['products_id'], 'NONSSL').'">'.xtc_image(DIR_WS_THUMBNAIL_IMAGES.$special['products_image'], $special['products_name']).'</a>';
			$special_buy_now = '<a href="'.xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array ('action')).'action=buy_now&BUYproducts_id='.$special['products_id'], 'NONSSL').'">'.xtc_image_button('button_buy_now.gif', TEXT_BUY.$special['products_name'].TEXT_NOW).'</a>';
			$module_content[] = array ('SPECIAL_NAME' => $special['products_name'], 'SPECIAL_ID' => $special['products_id'], 'SPECIAL_IMAGE' => $special_image, 'SPECIAL_PRICE' => $xtPrice->xtcGetPrice($special['products_id'], $format = true, 1, $special['products_tax_class_id'], $special['products_price']), 'SPECIAL_BUY_NOW' => $special_buy_now);
		}
	} else
	{
		$module_content = false;
	}
	$smarty->assign('module_content', $module_content);
	/**
	 * Warenkorbartikel anzeigen -> Ende
	 */

	if ($_GET['info_message'])
	{
		$smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
	}
	$smarty->assign('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART));
	$smarty->assign('BUTTON_CHECKOUT', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT).'</a>');
} else
{

	// empty cart
	$cart_empty = true;
	if ($_GET['info_message'])
	{
		$smarty->assign('info_message', str_replace('+', ' ', htmlspecialchars($_GET['info_message'])));
	}
	$smarty->assign('cart_empty', $cart_empty);
	$smarty->assign('BUTTON_CONTINUE', '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');

}
global $breadcrumb, $cPath_array, $actual_products_id;
if (!empty($cPath_array))
{
	$smarty->assign('CONTINUE_NAME', $breadcrumb->_trail[count($breadcrumb->_trail)-2]['title']);
	$smarty->assign('CONTINUE_LINK', $breadcrumb->_trail[count($breadcrumb->_trail)-2]['link']);
	$ct_shopping = $breadcrumb->_trail[count($breadcrumb->_trail)-2]['link'];
}
if (!empty($actual_products_id))
{
	$smarty->assign('CONTINUE_NAME', $breadcrumb->_trail[count($breadcrumb->_trail)-2]['title']);
	$smarty->assign('CONTINUE_LINK', $breadcrumb->_trail[count($breadcrumb->_trail)-2]['link']);
	$ct_shopping = $breadcrumb->_trail[count($breadcrumb->_trail)-2]['link'];
}
if (!empty($ct_shopping)) $_SESSION['continue_link'] = $ct_shopping;
if (!empty($_SESSION['continue_link'])) $smarty->assign('CONTINUE_LINK', $_SESSION['continue_link']);
$smarty->assign('BUTTON_CONTINUE_SHOPPING', xtc_image_button('button_continue_shopping.gif', IMAGE_BUTTON_CONTINUE_SHOPPING));

$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/shopping_cart.html');
$smarty->assign('main_content', $main_content);

$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
if (!defined(RM))
{
	$smarty->loadfilter('output', 'note');
}
$smarty->display(CURRENT_TEMPLATE.'/index.html');

// Paypal Express Modul Start
if(!isset($_SESSION['paypal_warten'])) {
	unset($_SESSION['nvpReqArray']);
	unset($_SESSION['reshash']['FORMATED_ERRORS']);
	unset($_SESSION['reshash']);
	unset($_SESSION['tmp_oID']);
}
else {
	unset($_SESSION['paypal_warten']);
}
// Paypal Express Modul Ende

include 'includes/application_bottom.php';
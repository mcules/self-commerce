<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_success.php,v 1.48 2003/02/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (checkout_success.php,v 1.14 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

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
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

// Google Analytics
if (GOOGLE_ANAL_ON == 'true' && GOOGLE_ANAL_CODE != ''){
  require_once (DIR_FS_INC.'xtc_get_order_data.inc.php');
  require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');
  require (DIR_WS_CLASSES.'order.php');
}
// Google Analytics End

// if the customer is not logged on, redirect them to the shopping cart page
if (!isset ($_SESSION['customer_id'])) {
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

if (isset ($_GET['action']) && ($_GET['action'] == 'update')) {

	if ($_SESSION['account_type'] != 1) {
		xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
	} else {
		xtc_redirect(xtc_href_link(FILENAME_LOGOFF));
	}
}
$breadcrumb->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
$breadcrumb->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

require (DIR_WS_INCLUDES.'header.php');

$orders_query = xtc_db_query("select orders_id, orders_status from ".TABLE_ORDERS." where customers_id = '".$_SESSION['customer_id']."' order by orders_id desc limit 1");
$orders = xtc_db_fetch_array($orders_query);
$last_order = $orders['orders_id'];
$order_status = $orders['orders_status'];

$smarty->assign('FORM_ACTION', xtc_draw_form('order', xtc_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')));
$smarty->assign('BUTTON_CONTINUE', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));
$smarty->assign('BUTTON_PRINT', '<img src="'.'templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/button_print.gif" style="cursor:hand" onclick="window.open(\''.xtc_href_link(FILENAME_PRINT_ORDER, 'oID='.$orders['orders_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')" />');
$smarty->assign('FORM_END', '</form>');

// Google Analytics
if (GOOGLE_ANAL_ON == 'true' && GOOGLE_ANAL_CODE != ''){
$order = new order($orders['orders_id']);
$BPpreiscounter=0;

$order_query = xtc_db_query("SELECT
	        				products_id,
	        				orders_products_id,
	        				products_model,
	        				products_name,
	        				final_price,
	        				products_quantity
	        				FROM ".TABLE_ORDERS_PRODUCTS."
	        				WHERE orders_id='".$orders['orders_id']."'");
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
		
$BPproductKategorie=xtc_get_product_path($order_data_values['products_id']);

		while ($attributes_data_values = xtc_db_fetch_array($attributes_query)) {
			$attributes_data .= ''.$attributes_data_values['products_options'].':'.$attributes_data_values['products_options_values'];
			$attributes_model .= ''.xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values']);
		}
		
$BPsinglePrice=$xtPrice->xtcFormat($order_data_values['final_price']/$order_data_values['products_quantity'], true);
$BPsinglePrice=eregi_replace(",",".",$BPsinglePrice);
$BPsinglePrice=eregi_replace("EUR","",$BPsinglePrice);
$BPsinglePrice=eregi_replace(" ","",$BPsinglePrice);

$BPpreiscounter=$BPpreiscounter+($BPsinglePrice*1*$order_data_values['products_quantity']);

$BPcategories_meta_query=xtDBquery("SELECT categories_meta_keywords,
                                            categories_meta_description,
                                            categories_meta_title,
                                            categories_name
                                            FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                            WHERE categories_id='".$BPproductKategorie."' and
                                            language_id='".$_SESSION['languages_id']."'");
$BPcategories_meta = xtc_db_fetch_array($BPcategories_meta_query,true);
$BPproductKategorie=$BPcategories_meta['categories_name'];

		$order_data[] = array ('PRODUCTS_MODEL' => $order_data_values['products_model'], 'PRODUCTS_NAME' => $order_data_values['products_name'], 'PRODUCTS_ATTRIBUTES' => $attributes_data, 'PRODUCTS_ATTRIBUTES_MODEL' => $attributes_model, 'PRODUCTS_PRICE' => $xtPrice->xtcFormat($order_data_values['final_price'], true),'PRODUCTS_SINGLE_PRICE' => $BPsinglePrice, 'PRODUCTS_QTY' => $order_data_values['products_quantity'], 'CAT' => $BPproductKategorie);
	}

$BPtotalPrice=$order->info['total'];
$BPtotalPrice=eregi_replace(",",".",$BPtotalPrice);
$BPtotalPrice=eregi_replace("EUR","",$BPtotalPrice);
$BPtotalPrice=eregi_replace(" ","",$BPtotalPrice);

/*Needs to be changed at 2007 */
$BPcurrentTAX=1.19; 
	
$BPtax=$BPpreiscounter-round(($BPpreiscounter/$BPcurrentTAX),2);
$BPshipping=$BPtotalPrice-$BPpreiscounter;
	
$smarty->assign('oID',$orders['orders_id']);
$smarty->assign('VALUE',$BPtotalPrice);
$smarty->assign('TAX',$BPtax);
$smarty->assign('SHIPPING',$BPshipping);
$smarty->assign('CITY',$order->customer['city']);
$smarty->assign('STATE',$order->customer['state']);
$smarty->assign('COUNTRY',$order->customer['country']);
$smarty->assign('order_data', $order_data);
}
// Google Analytics End

// GV Code Start
$gv_query = xtc_db_query("select amount from ".TABLE_COUPON_GV_CUSTOMER." where customer_id='".$_SESSION['customer_id']."'");
if ($gv_result = xtc_db_fetch_array($gv_query)) {
	if ($gv_result['amount'] > 0) {
		$smarty->assign('GV_SEND_LINK', xtc_href_link(FILENAME_GV_SEND));
	}
}
// GV Code End
// Google Conversion tracking
if (GOOGLE_CONVERSION == 'true') {

	$smarty->assign('google_tracking', 'true');
	$smarty->assign('tracking_code', '
		<noscript>
		<a href="http://services.google.com/sitestats/'.GOOGLE_LANG.'.html" onclick="window.open(this.href); return false;">
		<img height=27 width=135 border=0 src="http://www.googleadservices.com/pagead/conversion/'.GOOGLE_CONVERSION_ID.'/?hl='.GOOGLE_LANG.'" />
		</a>
		</noscript>
		    ');

}
if (DOWNLOAD_ENABLED == 'true')
	include (DIR_WS_MODULES.'downloads.php');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('PAYMENT_BLOCK', $payment_block);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_success.html');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>

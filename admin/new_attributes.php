<?php
/* --------------------------------------------------------------
   $Id: new_attributes.php 23 2012-06-10 16:26:52Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_attributes); www.oscommerce.com
   (c) 2003	 nextcommerce (new_attributes.php,v 1.13 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b				Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   copy attributes                          Autor: Hubi | http://www.netz-designer.de

   Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
require(DIR_WS_MODULES.'new_attributes_config.php');
require(DIR_FS_INC .'xtc_findTitle.inc.php');
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');

if ( isset($cPathID) && $_POST['action'] == 'change') {
	include(DIR_WS_MODULES.'new_attributes_change.php');
	xtc_redirect( './' . FILENAME_CATEGORIES . '?cPath=' . $cPathID . '&pID=' . $_POST['current_product_id'] );
}
require ('includes/application_top_1.php');
?>

<!-- content -->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  switch($_POST['action']) {
    case 'edit':
		if ($_POST['copy_product_id'] != 0) {
			$attrib_query = xtc_db_query("SELECT products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = " . (int)$_POST['copy_product_id']);
			while ($attrib_res = xtc_db_fetch_array($attrib_query)) {
				xtc_db_query("INSERT into ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix) VALUES ('" . (int)$_POST['current_product_id'] . "', '" . (int)$attrib_res['options_id'] . "', '" . (int)$attrib_res['options_values_id'] . "', '" . $attrib_res['options_values_price'] . "', '" . $attrib_res['price_prefix'] . "', '" . $attrib_res['attributes_model'] . "', '" . $attrib_res['attributes_stock'] . "', '" . $attrib_res['options_values_weight'] . "', '" . $attrib_res['weight_prefix'] . "')");
			}
		}
		$pageTitle = TITLE_EDIT.': ' . xtc_findTitle((int)$_POST['current_product_id'], $languageFilter);
		include(DIR_WS_MODULES.'new_attributes_include.php');
	break;

    case 'change':
		$pageTitle = TITLE_UPDATED;
		include(DIR_WS_MODULES.'new_attributes_change.php');
		include(DIR_WS_MODULES.'new_attributes_select.php');
	break;

    default:
		$pageTitle = TITLE_EDIT;
		include(DIR_WS_MODULES.'new_attributes_select.php');
	break;
  }
?>
    </table>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

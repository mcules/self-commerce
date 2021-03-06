<?php

/* -----------------------------------------------------------------------------------------
   $Id: advanced_search_result.php 17 2012-06-04 20:33:29Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(advanced_search_result.php,v 1.68 2003/05/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (advanced_search_result.php,v 1.17 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC.'xtc_parse_search_string.inc.php');
require_once (DIR_FS_INC.'xtc_get_subcategories.inc.php');
require_once (DIR_FS_INC.'xtc_get_currencies_values.inc.php');

/*
 * check search entry
 */

$error = 0; // reset error flag to false
$errorno = 0;
$keyerror = 0;

if (isset ($_GET['keywords']) && empty ($_GET['keywords'])) {
	$keyerror = 1;
}

if ((isset ($_GET['keywords']) && empty ($_GET['keywords'])) && (isset ($_GET['pfrom']) && empty ($_GET['pfrom'])) && (isset ($_GET['pto']) && empty ($_GET['pto']))) {
	$errorno += 1;
	$error = 1;
}
elseif (isset ($_GET['keywords']) && empty ($_GET['keywords']) && !(isset ($_GET['pfrom'])) && !(isset ($_GET['pto']))) {
	$errorno += 1;
	$error = 1;
}

if (strlen($_GET['keywords']) < 3 && strlen($_GET['keywords']) > 0 && $error == 0) {
	$errorno += 1;
	$error = 1;
	$keyerror = 1;
}

if (strlen($_GET['pfrom']) > 0) {
	$pfrom_to_check = xtc_db_input($_GET['pfrom']);
	if (!settype($pfrom_to_check, "double")) {
		$errorno += 10000;
		$error = 1;
	}
}

if (strlen($_GET['pto']) > 0) {
	$pto_to_check = $_GET['pto'];
	if (!settype($pto_to_check, "double")) {
		$errorno += 100000;
		$error = 1;
	}
}

if (strlen($_GET['pfrom']) > 0 && !(($errorno & 10000) == 10000) && strlen($_GET['pto']) > 0 && !(($errorno & 100000) == 100000)) {
	if ($pfrom_to_check > $pto_to_check) {
		$errorno += 1000000;
		$error = 1;
	}
}

if (strlen($_GET['keywords']) > 0) {
	if (!xtc_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
		$errorno += 10000000;
		$error = 1;
		$keyerror = 1;
	}
}

if ($error == 1 && $keyerror != 1) {

	xtc_redirect(xtc_href_link(FILENAME_ADVANCED_SEARCH, 'errorno='.$errorno.'&'.xtc_get_all_get_params(array ('x', 'y'))));

} else {

	/*
	 *    search process starts here
	 */

	$breadcrumb->add(NAVBAR_TITLE1_ADVANCED_SEARCH, xtc_href_link(FILENAME_ADVANCED_SEARCH));
	$breadcrumb->add(NAVBAR_TITLE2_ADVANCED_SEARCH, xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords='.xtc_db_input($_GET['keywords']).'&search_in_description='.xtc_db_input($_GET['search_in_description']).'&categories_id='.(int)$_GET['categories_id'].'&inc_subcat='.xtc_db_input($_GET['inc_subcat']).'&manufacturers_id='.(int)$_GET['manufacturers_id'].'&pfrom='.xtc_db_input($_GET['pfrom']).'&pto='.xtc_db_input($_GET['pto']).'&dfrom='.xtc_db_input($_GET['dfrom']).'&dto='.xtc_db_input($_GET['dto'])));

	require (DIR_WS_INCLUDES.'header.php');

	// define additional filters //

	//fsk18 lock
	if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
		$fsk_lock = " AND p.products_fsk18 != '1' ";
	} else {
		unset ($fsk_lock);
	}

	//group check
	if (GROUP_CHECK == 'true') {
		$group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	} else {
		unset ($group_check);
	}

	//manufacturers if set
	if (isset ($_GET['manufacturers_id']) && xtc_not_null($_GET['manufacturers_id'])) {
		$manu_check = " AND p.manufacturers_id = '".(int)$_GET['manufacturers_id']."' ";
	}

	//include subcategories if needed
	if (isset ($_GET['categories_id']) && xtc_not_null($_GET['categories_id'])) {
		if ($_GET['inc_subcat'] == '1') {
			$subcategories_array = array ();
			xtc_get_subcategories($subcategories_array, (int)$_GET['categories_id']);
			$subcat_join = " LEFT OUTER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS p2c ON (p.products_id = p2c.products_id) ";
			$subcat_where = " AND p2c.categories_id IN ('".(int) $_GET['categories_id']."' ";
			foreach ($subcategories_array AS $scat) {
				$subcat_where .= ", '".$scat."'";
			}
			$subcat_where .= ") ";
		} else {
			$subcat_join = " LEFT OUTER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS p2c ON (p.products_id = p2c.products_id) ";
			$subcat_where = " AND p2c.categories_id = '".(int) $_GET['categories_id']."' ";
		}
	}

$NeedTax = false;
if ($_GET['pfrom'] || $_GET['pto']) {
	$rate = xtc_get_currencies_values($_SESSION['currency']);
	$rate = $rate['value'];
	if ($rate && $_GET['pfrom'] != '') {
		$pfrom = $_GET['pfrom'] / $rate;
	}
	if ($rate && $_GET['pto'] != '') {
		$pto = $_GET['pto'] / $rate;
	}
	if($_SESSION['customers_status']['customers_status_show_price_tax']) {
		$NeedTax = true;
	}
}


//price filters
if (($pfrom != '') && (is_numeric($pfrom))) {
	if($NeedTax)
		$pfrom_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) >= round((".$pfrom."/(1+tax_rate/100)),".PRICE_PRECISION.") ) ";
	else
		$pfrom_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) >= round(".$pfrom.",".PRICE_PRECISION.") ) ";

} else {
	unset ($pfrom_check);
}

if (($pto != '') && (is_numeric($pto))) {
	if($NeedTax)
		$pto_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) <= round((".$pto."/(1+tax_rate/100)),".PRICE_PRECISION.") ) ";
	else
		$pto_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) <= round(".$pto.",".PRICE_PRECISION.") ) ";
} else {
	unset ($pto_check);
}


	//build query
	$select_str = "SELECT distinct
	                  p.products_id,
	                  p.products_price,
	                  p.products_model,
	                  p.products_quantity,
	                  p.products_shippingtime,
	                  p.products_fsk18,
	                  p.products_image,
	                  p.products_weight,
	                  p.products_tax_class_id,
	                  pd.products_name,
	                  pd.products_short_description,
	                  pd.products_description ";

	$from_str  = "FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS pd ON (p.products_id = pd.products_id) ";
	$from_str .= "left join ".TABLE_MANUFACTURERS." as m on p.manufacturers_id = m.manufacturers_id";
  $from_str .= $subcat_join;
	if (SEARCH_IN_ATTR == 'true') { $from_str .= " LEFT OUTER JOIN ".TABLE_PRODUCTS_ATTRIBUTES." AS pa ON (p.products_id = pa.products_id) LEFT OUTER JOIN ".TABLE_PRODUCTS_OPTIONS_VALUES." AS pov ON (pa.options_values_id = pov.products_options_values_id) "; }
	$from_str .= "LEFT OUTER JOIN ".TABLE_SPECIALS." AS s ON (p.products_id = s.products_id) AND s.status = '1'";

if($NeedTax) {
	if (!isset ($_SESSION['customer_country_id'])) {
		$_SESSION['customer_country_id'] = STORE_COUNTRY;
		$_SESSION['customer_zone_id'] = STORE_ZONE;
	}
	$from_str .= " LEFT OUTER JOIN ".TABLE_TAX_RATES." tr ON (p.products_tax_class_id = tr.tax_class_id) LEFT OUTER JOIN ".TABLE_ZONES_TO_GEO_ZONES." gz ON (tr.tax_zone_id = gz.geo_zone_id) ";
	$tax_where = " AND (gz.zone_country_id IS NULL OR gz.zone_country_id = '0' OR gz.zone_country_id = '".(int) $_SESSION['customer_country_id']."') AND (gz.zone_id is null OR gz.zone_id = '0' OR gz.zone_id = '".(int) $_SESSION['customer_zone_id']."')";
} else {
	unset ($tax_where);
}


	//where-string
	$where_str = " WHERE p.products_status = '1' "." AND pd.language_id = '".(int) $_SESSION['languages_id']."'".$subcat_where.$fsk_lock.$manu_check.$group_check.$tax_where.$pfrom_check.$pto_check;

	//go for keywords... this is the main search process
	if (isset ($_GET['keywords']) && xtc_not_null($_GET['keywords'])) {
		if (xtc_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
			$where_str .= " AND ( ";
			for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i ++) {
				switch ($search_keywords[$i]) {
					case '(' :
					case ')' :
					case 'and' :
					case 'or' :
						$where_str .= " ".$search_keywords[$i]." ";
						break;
					default :
						$where_str .= " ( ";
						$where_str .= "pd.products_keywords LIKE ('%".addslashes($search_keywords[$i])."%') ";
						if (SEARCH_IN_DESC == 'true') {
						   $where_str .= "OR pd.products_description LIKE ('%".addslashes($search_keywords[$i])."%') ";
						   $where_str .= "OR pd.products_short_description LIKE ('%".addslashes($search_keywords[$i])."%') ";
						}						
						$where_str .= "OR pd.products_name LIKE ('%".addslashes($search_keywords[$i])."%') ";
						$where_str .= "OR p.products_model LIKE ('%".addslashes($search_keywords[$i])."%') ";
						if (SEARCH_IN_ATTR == 'true') {
						   $where_str .= "OR (pov.products_options_values_name LIKE ('%".addslashes($search_keywords[$i])."%') ";
						   $where_str .= "AND pov.language_id = '".(int) $_SESSION['languages_id']."')";
						}
						$where_str .= " ) ";
						break;
				}
			}
			$where_str .= " ) ";
		}
	}

	//glue together
//sorting_dropdown BEGINS
$sorting_dropdown = xtc_draw_form('sorting', FILENAME_ADVANCED_SEARCH_RESULT, 'GET') . '&nbsp;';
if (isset($_GET['manufacturers_id'])) 
$sorting_dropdown.= xtc_draw_hidden_field('manufacturers_id', $_GET['manufacturers_id']);
if (isset($_GET['inc_subcat'])) 
$sorting_dropdown.= xtc_draw_hidden_field('inc_subcat', $_GET['inc_subcat']);
if (isset($_GET['pfrom'])) 
$sorting_dropdown.= xtc_draw_hidden_field('pfrom', $_GET['pfrom']); 
if (isset($_GET['pto'])) 
$sorting_dropdown.= xtc_draw_hidden_field('pto', $_GET['pto']); 
if (isset($_GET['x'])) 
$sorting_dropdown.= xtc_draw_hidden_field('x', $_GET['x']); 
if (isset($_GET['y'])) 
$sorting_dropdown.= xtc_draw_hidden_field('y', $_GET['y']); 
if (isset($_GET['categories_id'])) 
$sorting_dropdown.= xtc_draw_hidden_field('categories_id', $_GET['categories_id']);
if (isset($_GET['keywords'])) 
$sorting_dropdown.= xtc_draw_hidden_field('keywords', $_GET['keywords']);
$options_sort = array(array('text' => SORT_CHANGE));
      $options_sort[] = array('id' => '1', 'text' => A_Z); 
      $options_sort[] = array('id' => '2', 'text' => Z_A); 
      $options_sort[] = array('id' => '3', 'text' => PRICE_UP); 
      $options_sort[] = array('id' => '4', 'text' => PRICE_DOWN); 
      $options_sort[] = array('id' => '5', 'text' => MANU_UP); 
      $options_sort[] = array('id' => '6', 'text' => MANU_DOWN); 
 
$sorting_dropdown.= xtc_draw_pull_down_menu('sorting_id', $options_sort, $_GET['sorting_id'], 'onchange="this.form.submit()"');
$sorting_dropdown.= '</form>' . "\n";
//sorting_dropdown END
switch ((int)$_GET['sorting_id']) {
case 1:
$order_str = ' group by pd.products_name ORDER BY pd.products_name ASC'; 
break;
case 2:
$order_str = ' group by pd.products_name ORDER BY pd.products_name DESC';
break;
case 3:
$order_str = ' group by pd.products_name ORDER BY p.products_price ASC';
break;
case 4:
$order_str = ' group by pd.products_name ORDER BY p.products_price DESC';
break;
case 5:
$order_str = ' group by pd.products_name ORDER BY m.manufacturers_name ASC';
break;
case 6:
$order_str = ' group by pd.products_name ORDER BY m.manufacturers_name DESC';
break;
}	
   $join_p2c = " LEFT OUTER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " AS p2c ON (p.products_id = p2c.products_id)";
   $join_cat = "LEFT OUTER JOIN " . TABLE_CATEGORIES . " AS cat ON (p2c.categories_id = cat.categories_id)";
   $where_cat = " AND cat.categories_status = '1'";
   $listing_sql = $select_str.$from_str.$join_p2c.$join_cat.$where_str.$where_cat.$order_str; 
	require (DIR_WS_MODULES.FILENAME_PRODUCT_LISTING);
}
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
if (!defined(RM))
	$smarty->loadfilter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>

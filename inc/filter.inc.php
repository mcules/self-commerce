<?php

require_once (DIR_FS_INC.'xtc_parse_search_string.inc.php');
require_once (DIR_FS_INC.'xtc_get_subcategories.inc.php');
require_once (DIR_FS_INC.'xtc_get_currencies_values.inc.php');

function sc_filter_create_link($array, $k_e, $v_e){
	$parameters_first = '';
		foreach($array as $key=>$value){
			if(in_array($v_e, $value)){
			 $key_z = array_search($v_e, $value);
			 unset($value[$key_z]);
			}
			if(in_array(0, $value)){
			 $key_z = array_search(0, $value);
			 unset($value[$key_z]);
			}
			if(count($value) == 0){continue;}
			$parameters_first .= $key.'-'.implode('-', $value);
			$parameters_first .= '|';
		}
		if(!empty($k_e) AND !array_key_exists($k_e, $array)){
			$parameters_first .= $k_e.'-'.$v_e;
		}
		return $parameters_first;
	}


function sc_filter_split_link($test_get,$recursive=false){
	$test_get = (string)$test_get;
	$test_get = preg_replace('/([^0-9-\|]+)/','',$test_get);
	if(!$recursive AND isset($_POST['f']) AND is_array($_POST['f'])){
		$str = sc_filter_create_link($_POST['f'],Array(),Array());
		return sc_filter_split_link($str.$test_get,true);
	}

	$ausgabe = Array();
	$test_array = explode('|', $test_get);
	foreach($test_array AS $values){
		if($values==''){continue;}
		$option = explode('-',$values);
		$key = $option[0];
		unset($option[0]);
		if(isset($ausgabe[$key])){
			$ausgabe[$key] = array_merge($ausgabe[$key],$option);
			$ausgabe[$key] = array_unique($ausgabe[$key]);
		}else{
			$ausgabe[$key] = $option;
		}
	}
	return $ausgabe;
}

function array_merge_recursive_distinct(array &$array1,array &$array2){
		  $merged = $array1;
		  foreach ($array2 as $key => &$value){
			if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])){
			  $merged[$key] = array_merge_recursive_distinct ($merged[$key], $value);
			}else{
			  $merged[$key] = $value;
			}
		  }
		  return $merged;
		}

function sc_filter_search_sql($filter_id,$option_search=false){

/** ############################### **/

// security fix
	$keywords = $_GET['keywords'] = isset($_GET['keywords']) && !empty($_GET['keywords']) ? stripslashes(trim(urldecode($_GET['keywords']))) : false;
	$pfrom = $_GET['pfrom'] = isset($_GET['pfrom']) && !empty($_GET['pfrom']) ? stripslashes($_GET['pfrom']) : false;
	$pto = $_GET['pto'] = isset($_GET['pto']) && !empty($_GET['pto']) ? stripslashes($_GET['pto']) : false;
	$manufacturers_id  = $_GET['manufacturers_id'] = isset($_GET['manufacturers_id']) && xtc_not_null($_GET['manufacturers_id']) ? (int)$_GET['manufacturers_id'] : false;
	$categories_id = $_GET['categories_id'] = isset($_GET['categories_id']) && xtc_not_null($_GET['categories_id']) ? (int)$_GET['categories_id'] : false;
	$_GET['inc_subcat'] = isset($_GET['inc_subcat']) && xtc_not_null($_GET['inc_subcat']) ? (int)$_GET['inc_subcat'] : null;

	$filters = $_GET['opt'] = isset($_GET['opt']) && !empty($_GET['opt']) ? $_GET['opt'] : false;
	$filters = sc_filter_split_link($filters);

	$d_filters = $_GET['dopt'] = isset($_GET['dopt']) && !empty($_GET['dopt']) ? $_GET['dopt'] : false;
	$d_filters = sc_filter_split_link($d_filters);

	$filters = array_merge_recursive_distinct($filters, $d_filters);

  // default values
  $subcat_join  = '';
  $subcat_where = '';
  $tax_where    = '';
  $cats_list    = '';
  $left_join    = '';
  $module_content = array();
  $filter_id = (int) $filter_id;

  // fsk18 lock
  $fsk_lock = $_SESSION['customers_status']['customers_fsk18_display'] == '0' ? " AND p.products_fsk18 != '1' " : "";

  // group check
  $group_check = GROUP_CHECK == 'true' ? " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 " : "";

  // manufacturers check
  $manu_check = $manufacturers_id !== false ? " AND p.manufacturers_id = '".$manufacturers_id."' " : "";

  //include subcategories if needed
  if ($categories_id !== false) {
    if (isset($_GET['inc_subcat']) && $_GET['inc_subcat'] == '1') {
      $subcategories_array = array();
      xtc_get_subcategories($subcategories_array, $categories_id);
      $subcat_join = " LEFT OUTER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS p2c ON (p.products_id = p2c.products_id) ";
      $subcat_where = " AND p2c.categories_id IN ('".$categories_id."' ";
      foreach ($subcategories_array AS $scat) {
        $subcat_where .= ", '".$scat."'";
      }
      $subcat_where .= ") ";
    } else {
      $subcat_join = " LEFT OUTER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS p2c ON (p.products_id = p2c.products_id) ";
      $subcat_where = " AND p2c.categories_id = '".$categories_id."' ";
    }
  }else{
   $subcat_join = " LEFT OUTER JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." AS p2c ON (p.products_id = p2c.products_id) ";
   $subcat_where = " ";
  }

   $subcat_join .= " LEFT OUTER JOIN ".TABLE_CATEGORIES." AS c ON (c.categories_id = p2c.categories_id) ";
   $subcat_where .= " AND c.categories_status = 1 ";


  // price by currency
  $NeedTax = false;
  if ($pfrom || $pto) {
    $rate = xtc_get_currencies_values($_SESSION['currency']);
    $rate = $rate['value'];
    if ($rate && $pfrom) {
      $pfrom = $pfrom / $rate;
    }
    if ($rate && $pto) {
      $pto = $pto / $rate;
    }
    if($_SESSION['customers_status']['customers_status_show_price_tax']) {
      $NeedTax = true;
    }
  }

  //price filters
  $pfrom_check = '';
  if (($pfrom != '') && (is_numeric($pfrom))) {
    if($NeedTax)
      $pfrom_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) >= round((".$pfrom."/(1+tax_rate/100)),".PRICE_PRECISION.") ) ";
    else
      $pfrom_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) >= round(".$pfrom.",".PRICE_PRECISION.") ) ";
  }

  $pto_check = '';
  if (($pto != '') && (is_numeric($pto))) {
    if($NeedTax)
      $pto_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) <= round((".$pto."/(1+tax_rate/100)),".PRICE_PRECISION.") ) ";
    else
      $pto_check = " AND (IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) <= round(".$pto.",".PRICE_PRECISION.") ) ";
  }

  // filtering on
  $filter_check = '';
  if (!empty($filters)) {
	$values = Array();
	$filter_values = Array();
	foreach($filters AS $filter_ids => $filter_array){
		$count_filter = count($filter_array);
		if(empty($filter_array) OR $count_filter==0 OR $filter_ids==$filter_id)continue;
		$filter_values[] = 'p.products_id IN (SELECT products_id FROM '.TABLE_PRODUCTS_FILTER.' WHERE products_id=p.products_id AND products_options_values_id'.($count_filter==1?'='.implode('',$filter_array):' IN ('.implode(',',$filter_array).')').')';
		$values[] = implode(',',$filter_array);
	}
	$filter_check = implode(' AND ',$filter_values);
	if($filter_check!=''){
		$filter_check = ' AND '.$filter_check.' ';
	}
	if($option_search AND count($values)>0){
		$filter_check.= ' AND pf.products_options_values_id IN ('.implode(',',$values).') ';
	}
  }
  // #3431# Specials
  if (isset($_GET['y']) AND $_GET['y']=='specials'){
	$filter_check.= ' AND s.status = 1 ';
  }
  // $filter_check.= '  AND pf.products_options_id='.$filter_id.'';

  //build query
  $select_str = "SELECT COUNT(distinct p.products_id) AS summe, pf.products_options_values_id, pf.products_options_id ";

  $from_str  = "FROM ".TABLE_PRODUCTS." AS p LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." AS pd ON (p.products_id = pd.products_id) ";

  $from_str .= 'LEFT JOIN '.TABLE_PRODUCTS_FILTER.' pf ON (p.products_id=pf.products_id) ';
  $from_str .= $subcat_join;
  $from_str .= SEARCH_IN_ATTR == 'true' ? " LEFT OUTER JOIN ".TABLE_PRODUCTS_ATTRIBUTES." AS pa ON (p.products_id = pa.products_id) LEFT OUTER JOIN ".TABLE_PRODUCTS_OPTIONS_VALUES." AS pov ON (pa.options_values_id = pov.products_options_values_id) " : '';
  $from_str .= "LEFT OUTER JOIN ".TABLE_SPECIALS." AS s ON (p.products_id = s.products_id) AND s.status = '1'";

  if($NeedTax) {
    if (!isset ($_SESSION['customer_country_id'])) {
      $_SESSION['customer_country_id'] = STORE_COUNTRY;
      $_SESSION['customer_zone_id'] = STORE_ZONE;
    }
    $from_str .= " LEFT OUTER JOIN ".TABLE_TAX_RATES." tr ON (p.products_tax_class_id = tr.tax_class_id) LEFT OUTER JOIN ".TABLE_ZONES_TO_GEO_ZONES." gz ON (tr.tax_zone_id = gz.geo_zone_id) ";
    $tax_where = " AND (gz.zone_country_id IS NULL OR gz.zone_country_id = '0' OR gz.zone_country_id = '".(int) $_SESSION['customer_country_id']."') AND (gz.zone_id is null OR gz.zone_id = '0' OR gz.zone_id = '".(int) $_SESSION['customer_zone_id']."')";
  }
  //where-string
  $where_str = "
  WHERE p.products_status = 1
  AND pd.language_id = '".$_SESSION['languages_id']."'"
  .$subcat_where
  .$fsk_lock
  .$manu_check
  .$group_check
  .$tax_where
  .$pfrom_check
  .$pto_check
  .$filter_check;

  $group = false;
  //go for keywords... this is the main search process
  if ($keywords) {
    if (xtc_parse_search_string($keywords, $search_keywords)) {
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
          $ent_keyword = encode_htmlentities($search_keywords[$i]); // umlauts
          $ent_keyword = $ent_keyword != $search_keywords[$i] ? addslashes($ent_keyword) : false;
          $keyword = addslashes($search_keywords[$i]);
          $where_str .= " ( ";
          $where_str .= "pd.products_keywords LIKE ('%".$keyword."%') ";
          $where_str .= $ent_keyword ? "OR pd.products_keywords LIKE ('%".$ent_keyword."%') " : '';
          if (SEARCH_IN_DESC == 'true') {
             $where_str .= "OR pd.products_description LIKE ('%".$keyword."%') ";
             $where_str .= $ent_keyword ? "OR pd.products_description LIKE ('%".$ent_keyword."%') " : '';
             $where_str .= "OR pd.products_short_description LIKE ('%".$keyword."%') ";
             $where_str .= $ent_keyword ? "OR pd.products_short_description LIKE ('%".$ent_keyword."%') " : '';
          }
          $where_str .= "OR pd.products_name LIKE ('%".$keyword."%') ";
          $where_str .= $ent_keyword ? "OR pd.products_name LIKE ('%".$ent_keyword."%') " : '';
          $where_str .= "OR p.products_model LIKE ('%".$keyword."%') ";
          $where_str .= $ent_keyword ? "OR p.products_model LIKE ('%".$ent_keyword."%') " : '';
          $where_str .= "OR p.products_ean LIKE ('%".$keyword."%') ";
          $where_str .= $ent_keyword ? "OR p.products_ean LIKE ('%".$ent_keyword."%') " : '';
          $where_str .= "OR p.products_manufacturers_model LIKE ('%".$keyword."%') ";
          $where_str .= $ent_keyword ? "OR p.products_manufacturers_model LIKE ('%".$ent_keyword."%') " : '';
          if (SEARCH_IN_ATTR == 'true') {
            $where_str .= "OR pa.attributes_model LIKE ('%".$keyword."%') ";
            $where_str .= ($ent_keyword) ? "OR pa.attributes_model LIKE ('%".$ent_keyword."%') " : '';
            $where_str .= "OR pa.attributes_ean LIKE ('%".$keyword."%') ";
            $where_str .= ($ent_keyword) ? "OR pa.attributes_ean LIKE ('%".$ent_keyword."%') " : '';
            $where_str .= "OR (pov.products_options_values_name LIKE ('%".$keyword."%') ";
            $where_str .= ($ent_keyword) ? "OR pov.products_options_values_name LIKE ('%".$ent_keyword."%') " : '';
            $where_str .= "AND pov.language_id = '".(int) $_SESSION['languages_id']."')";
          }
          $where_str .= " ) ";
          break;
        }
      }
      $where_str .= " ) ";
      // $where_str .= " ) GROUP BY p.products_id ORDER BY p.products_id ";
	  // $group = true;
    }
  }
  if(!$group){
	if($option_search){
		$where_str .= ' GROUP BY pf.products_options_values_id';
	}else{
		$where_str .= ' GROUP BY IF(pf.products_options_id='.$filter_id.',pf.products_options_values_id,0), pf.products_options_id';
	}
  }
  // glue together
  $listing_sql = $select_str.$from_str.$where_str;
/** ############################### **/
	return $listing_sql;
}
?>
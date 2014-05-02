<?php
/* -----------------------------------------------------------------------------------------
   $Id:$
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
require_once (DIR_FS_INC.'xtc_image_submit.inc.php');
require_once (DIR_FS_INC.'filter.inc.php');
require_once (DIR_FS_INC.'xtc_hide_session_id.inc.php');

// set cache ID
if (!CacheCheck()) {
	$cache_id=null;
	$cache=false;
	$box_smarty->caching = 0;
} else {
	$cache=true;
	$box_smarty->caching = 1;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
}


if (!$box_smarty->is_cached(CURRENT_TEMPLATE.'/boxes/box_filter.html', $cache_id) || !$cache) {
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
	$box_smarty->assign('language', $_SESSION['language']);

	$keywords = isset($_GET['keywords']) && !empty($_GET['keywords']) ? stripslashes(trim(urldecode($_GET['keywords']))) : false;
	$pfrom = isset($_GET['pfrom']) && !empty($_GET['pfrom']) ? stripslashes($_GET['pfrom']) : false;
	$pto = isset($_GET['pto']) && !empty($_GET['pto']) ? stripslashes($_GET['pto']) : false;
	$manufacturers_id  = isset($_GET['manufacturers_id']) && xtc_not_null($_GET['manufacturers_id']) ? (int)$_GET['manufacturers_id'] : false;
	$categories_id = isset($_GET['categories_id']) && xtc_not_null($_GET['categories_id']) ? (int)$_GET['categories_id'] : false;
	$inc_subcat = isset($_GET['inc_subcat']) && xtc_not_null($_GET['inc_subcat']) ? (int)$_GET['inc_subcat'] : null;

	$params = Array(
		'opt',
		'filter_id'
	);
	$group_check = '';
	if (GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_".(int)$_SESSION['customers_status']['customers_status_id']."_group%'";
	}

	$options_list = '';


	if(isset($_GET['opt'])){
	$test_array = sc_filter_split_link($_GET['opt']);
	}else{
	$test_array = Array();
	}

/*
* Optionswerte bestimmen:
*/
	$werte = array();
	$optionen = array();
	foreach($test_array as $option=>$values){
		if($option<1){continue;} // Leer? ueberspringen
		foreach($values as $value){
			if(empty($value) OR $value<1){continue;} // hmm, sonderfall? ueberspringen
			$werte[$value] = $value;
			$optionen[$value] = $option;
		}
	}

	$popup_parameters = sc_filter_create_link($test_array, 0, 0);
	$get_url_params = xtc_get_all_get_params($params);

/*
* Optionswerte auslesen:
*/
	$alle_optionen = implode(',', $werte);
	$values = Array();
	if(!empty($alle_optionen)){
		$SQL = "SELECT products_options_values_id,products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES."
									WHERE products_options_values_id IN (".$alle_optionen.")
									AND language_id = '".(int) $_SESSION['languages_id']."'";
		$RESULT = xtDBquery($SQL);
		$NUM_ROWS = xtc_db_num_rows($RESULT);
		for($i=0;$i<$NUM_ROWS;$i++){
			$A = xtc_db_fetch_array($RESULT);
			$values_id = $A['products_options_values_id'];
			$values_name = $A['products_options_values_name'];
			$link = '';
			$option = 0;
			if(isset($optionen[$values_id])){
				$option = $optionen[$values_id];
				$parameters = sc_filter_create_link($test_array, $option, $values_id);
				$link = xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, $get_url_params.'opt='.$parameters);
			}
			$values[$values_id] = Array(
				'SUM' => 0,
				'NAME' => $values_name,
				'LINK' => $link,
				'OPTIONS_ID' => $option
			);
		}
		$find_values_sql = sc_filter_search_sql(0,true);
		$RESULT = xtDBquery($find_values_sql);
		$NUM_ROWS = xtc_db_num_rows($RESULT);
		for($i=0;$i<$NUM_ROWS;$i++){
			$A = xtc_db_fetch_array($RESULT);
			$values_id = $A['products_options_values_id'];
			$values_summe = $A['summe'];
			$option = $A['products_options_id'];
			if(!isset($values[$values_id])){continue;}

			$values[$values_id]['SUM'] = $values_summe;
		}
	}
unset($werte);
unset($optionen);

/*
* Optionen auslesen:
*/
	$options = Array();
	$SQL = "SELECT products_options_name, products_options_id FROM ".TABLE_PRODUCTS_OPTIONS."
								WHERE language_id = '".(int) $_SESSION['languages_id']."'
								AND option_active=1
								AND box_filter>0
							ORDER BY box_filter, products_options_sortorder, products_options_name";
	$RESULT = xtDBquery($SQL);
	$NUM_ROWS = xtc_db_num_rows($RESULT);
	for($i=0;$i<$NUM_ROWS;$i++){
		$A = xtc_db_fetch_array($RESULT);
		$option_values = Array();
		$fo_id = $A['products_options_id'];

		if(array_key_exists($fo_id, $test_array) AND count($test_array[$fo_id])>0){
			foreach($test_array[$fo_id] AS $value){
				if(!isset($values[$value])){continue;}
				$option_values[] = $value;
			}
		}

		$A['products_options_name'] = trim(preg_replace('/\[(.*?)\]/','',$A['products_options_name']));
		$options[$fo_id] = Array(
			'NAME' => $A['products_options_name'],
			'VALUES' => $option_values,
			'LINK' => xtc_href_link(DIR_WS_AJAX.FILENAME_PRODUCT_FILTERS,$get_url_params."opt=".$popup_parameters."&filter_id=".$fo_id)
		);
	}

	$box_smarty->assign('VALUES', $values);
	$box_smarty->assign('OPTIONS', $options);

	$group_check = (GROUP_CHECK == 'true') ? " AND p.group_permission_" . (int)$_SESSION['customers_status']['customers_status_id'] . "=1 " : '';
	$fsk_lock = ($_SESSION['customers_status']['customers_fsk18_display'] == '0') ? " AND p.products_fsk18 != '1' " : "";
	$filter_products_query = "SELECT
	 					MIN( IF(s.specials_new_products_price IS NULL,p.products_price,s.specials_new_products_price) ) AS minpreis,
						MAX( p.products_price ) AS maxpreis,
						p.products_tax_class_id
	 					FROM ".TABLE_PRODUCTS." AS p LEFT OUTER JOIN ".TABLE_SPECIALS." AS s ON (s.products_id=p.products_id AND s.status=1)
	 					WHERE p.products_status=1 ".$group_check.$fsk_lock;

	$filter_products_query = xtDBquery($filter_products_query);
	$filter_products_data = xtc_db_fetch_array($filter_products_query);

	$minpreis = isset($filter_products_data['minpreis'])?(int)$xtPrice->xtcFormat($filter_products_data['minpreis'], false, $filter_products_data['products_tax_class_id']):0;
	$maxpreis = isset($filter_products_data['maxpreis'])?ceil($xtPrice->xtcFormat($filter_products_data['maxpreis'], false, $filter_products_data['products_tax_class_id'])):0;
	$box_smarty->assign('PREIS_MIN', ($minpreis>0?$minpreis:0));
	$box_smarty->assign('PREIS_MAX', ($maxpreis>0?$maxpreis:0));
	$box_smarty->assign('HIDDEN_INPUT_FIELDS',
					 xtc_draw_hidden_field('opt',$popup_parameters)
					.xtc_draw_hidden_field('keywords',$keywords)
					.xtc_draw_hidden_field('pfrom',$pfrom)
					.xtc_draw_hidden_field('pto',$pto)
					.xtc_draw_hidden_field('categories_id', $categories_id)
					.xtc_draw_hidden_field('inc_subcat', $inc_subcat)
					.xtc_draw_hidden_field('manufacturers_id', $manufacturers_id)
				);
	$box_smarty->assign('FORM_ACTION', xtc_draw_form('advanced_search_filter', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get', '').xtc_hide_session_id());
	$box_smarty->assign('FORM_END', '</form>');
	$box_smarty->assign('REMOVE_IMG', xtc_image('templates/'.CURRENT_TEMPLATE.'/img/hacken.png', 'remove'));
	$box_smarty->assign('RESET_LINK', xtc_href_link(FILENAME_DEFAULT));
	$params['opt'] = $popup_parameters;

}

if (!$cache) {
    $box_filter = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_filter.html');
} else {
    $box_filter = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_filter.html', $cache_id);
}
//EOF - DokuMan - 2010-02-28 - fix Smarty cache error on unlink

$smarty->assign('box_FILTER', $box_filter);
?>
<?php

// Info Tabs
$last_group = -1;
$buffer = Array();
$urlsuch = Array();
$urlreplace = Array();
$urlsuch[]="/([^]_a-z0-9-=\"'\/])((https?|ftp):\/\/|www\.)([^ \r\n\(\)\^\$!`\"'\|\[\]\{\}<>]*)/si";
$urlsuch[]="/^((https?|ftp):\/\/|www\.)([^ \r\n\(\)\^\$!`\"'\|\[\]\{\}<>]*)/si";

$urlreplace[]="\\1[URL]\\2\\4[/URL]";
$urlreplace[]="[URL]\\1\\3[/URL]";

$result = xtDBquery("SELECT
								po.products_options_id,
								po.products_options_name,
								po.option_location,
								po.options_sortgroup,
								pf.products_options_values_id,
								pf.products_options_id,
								pov.products_options_values_name,
								pov.value_source,
								pov.value_image,
								pov.content_group
				FROM 		".TABLE_PRODUCTS_FILTER." AS pf, ".TABLE_PRODUCTS_OPTIONS." AS po, ".TABLE_PRODUCTS_OPTIONS_VALUES." AS pov
				WHERE 	pf.products_id = '".(int)$product->data['products_id']."'
				AND 		pf.products_options_id = po.products_options_id
				AND 		pf.products_options_values_id = pov.products_options_values_id
				AND 		po.language_id = '".(int)$_SESSION['languages_id']."'
				AND 		po.language_id = pov.language_id
				AND			po.option_active = 1
				ORDER BY po.options_sortgroup, po.products_options_sortorder");
while($A = xtc_db_fetch_array($result)){
	$opt_id = $A['products_options_id'];
	$value_id = $A['products_options_values_id'];

	$content_group = $A['content_group'];
	$content_group_link = '';
	if($content_group>0){
		$content_group = $main->getContentData($content_group);
		if($content_group){
			$content_group_link = $main->getContentLink($A['content_group'],$A['products_options_values_name']);
			// $content_group_link = '<a href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.$A['content_group'], $request_type).'" title="'.htmlspecialchars($A['products_options_values_name'],ENT_QUOTES).'" class="c-link">'.$A['products_options_values_name'].'</a>';
		}
	}else{
		$content_group = false;
	}
	// Building:
	if(!isset($buffer[$opt_id])){
		$last_group_sort = 0;
		if($last_group==-1){
			$last_group = $A['options_sortgroup'];
		}elseif($last_group != $A['options_sortgroup']){
			$last_group = $A['options_sortgroup'];
			$last_group_sort = 1;
		}
		$A['products_options_name'] = trim(preg_replace('/\[(.*?)\]/','',$A['products_options_name']));
		// Aus Text Links erstellen #2013-04-25
		$buff_name = preg_replace($urlsuch, $urlreplace, $A['products_options_values_name']);
		$buff_name = preg_replace("/\[URL\]www.(.*?)\[\/URL\]/si", "<a target=\"_blank\" rel=\"nofollow\" class=\"link-extern\" href=\"http://www.\\1\">www.\\1</a>", $buff_name);
		$buff_name = preg_replace("/\[URL\](.*?)\[\/URL\]/si", "<a target=\"_blank\" rel=\"nofollow\" class=\"link-extern\" href=\"\\1\">\\1</a>", $buff_name);
		$A['products_options_values_name'] = $buff_name;

		$buffer[$opt_id] = Array(
			'options_name' 	=> $A['products_options_name'],
			'values_name' 	=> '',
			'location' 		=> $A['option_location'],
			'values' 		=> Array(),
			'sortgroup' 	=> $last_group_sort
		);
	}
	$buffer[$opt_id]['values'][$value_id] = Array(
		'values_name' 		=> $A['products_options_values_name'],
		'source' 			=> $A['value_source'],
		'image' 			=> $A['value_image'],
		'content' 			=> $content_group,
		'content_link' 		=> $content_group_link
	);
	if($buffer[$opt_id]['values_name'] != ''){
		$buffer[$opt_id]['values_name'].= ', ';
	}
	$buffer[$opt_id]['values_name'].= ($content_group_link ? $content_group_link : $A['products_options_values_name']);
}


$product_filter_tabs = array();
$product_filter_special = array();
$list_filters = array();

// Locations
foreach($buffer AS $opt_id => $options){
	$location = (string)$options['location'];
	if(strpos($location,'0')!==false){
			$list_filters[$opt_id] = $options;
			unset($list_filters[$opt_id]['values']);
	}
	if(strpos($location,'1')!==false){
			$product_filter_tabs[$opt_id] = $options;
	}
	if(strpos($location,'2')!==false){
				$product_filter_special[$opt_id] = Array(
					'values_name' => $options['values_name'],
					'options_name' => $options['options_name'],
					'options_id' => $opt_id,
					'values' => $options['values']
				);


			foreach($product_filter_special[$opt_id]['values'] AS $value_id => $value){
				unset($product_filter_special[$opt_id]['values'][$value_id]['content']);
			}

	}
}
if(!sc_check('ACTIVATE_PRODUCT_FILTER_TABS')){
	$product_filter_tabs = Array();
}
// Temp-Trick:
if(sc_check('ACTIVATE_PRODUCT_FILTER_SPECIAL_CONNECTION')){

	$connection_array = array();
	$connection_array = explode('=', sc_check('ACTIVATE_PRODUCT_FILTER_SPECIAL_CONNECTION_VAR'));

	$seperator = ', ';
	if(sc_check('ACTIVATE_PRODUCT_FILTER_SPECIAL_CONNECTION_SEPERATOR')){
		$seperator = sc_check('ACTIVATE_PRODUCT_FILTER_SPECIAL_CONNECTION_SEPERATOR');
	}

	$zaehler = 1;

	foreach($connection_array AS $key => $value){
		if($zaehler == 1){
			$abc = $product_filter_special[$value]['values_name'];
		}else{
			$abc = $abc.$seperator.$product_filter_special[$value]['values_name'];
		}

		unset($product_filter_special[$value]['values_name']);

		if($zaehler > 1){
			unset($product_filter_special[$value]);
		}
		$zaehler++;

	}
	$product_filter_special[$connection_array[0]]['values_name'] = $abc;

	// if(isset($product_filter_special[5]) AND isset($product_filter_special[6])){
		// $product_filter_special[5]['values_name'].=', '.$product_filter_special[6]['values_name'];
		// unset($product_filter_special[6]);
	// }
}

if(isset($product_filter_special[16]['values_name'])){
	$temp_name = strip_tags($product_filter_special[16]['values_name']);
	if(strlen($temp_name) >= 60){
		$product_filter_special[16]['values_name'] = substr($temp_name, 0, 60);
		$product_filter_special[16]['values_name'].= '<a href="javascript:nextTab(2,\'#tab_16\')"> ..'.MORE_INFO.'</a>';
	}
}

	$info_smarty->assign('MODULE_product_filter_tabs', $product_filter_tabs);
	$info_smarty->assign('MODULE_product_filter_special', $product_filter_special);
	$info_smarty->assign('MODULE_product_filters', $list_filters);
	// 2013-05-27 # Jahrgang und sowas automatisch erweitern. (listing)
	$product_filter_vars = Array(
		'PRODUCTS_ID' => $product->data['products_id']
	);
	$product->getFilterInfos($product_filter_vars,$product->data);
	unset($product_filter_vars['PRODUCTS_ID']);
	$info_smarty->assign('MODULE_product_filter_vars', $product_filter_vars);
// werden nichts mehr gebraucht
unset($buffer);
unset($list_filters);
unset($product_filter_tabs);
unset($product_filter_special);
?>
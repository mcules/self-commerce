<?php
$default_smarty = new smarty;
$default_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$default_smarty->assign('session', session_id());
$main_content = '';
// include needed functions
require_once (DIR_FS_INC.'xtc_customer_greeting.inc.php');
require_once (DIR_FS_INC.'xtc_get_path.inc.php');
require_once (DIR_FS_INC.'xtc_check_categories_status.inc.php');

if(xtc_check_categories_status($current_category_id) >= 1){
	$error = CATEGORIE_NOT_FOUND;
	include(DIR_WS_MODULES.FILENAME_ERROR_HANDLER);
}else{
	if($category_depth == 'categories'){
	 include(DIR_WS_MODULES.'default_categories.php');
	}elseif($category_depth == 'products' || $_GET['manufacturers_id']){
	 include(DIR_WS_MODULES.'default_products.php');
	}else{ // default page
	 include(DIR_WS_MODULES.'default_content.php');
	}
}
?>

<?php

/* -----------------------------------------------------------------------------------------
   $Id$   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(information.php,v 1.6 2003/02/10); www.oscommerce.com 
   (c) 2003	 nextcommerce (content.php,v 1.2 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
$content_string = '';

$box_smarty->assign('language', $_SESSION['language']);
// set cache ID
// if (!CacheCheck()) {
	$cache=false;
	$box_smarty->caching = 0;
// } else {
//	$cache=true;
//	$box_smarty->caching = 1;
//	$box_smarty->cache_lifetime = CACHE_LIFETIME;
//	$box_smarty->cache_modified_check = CACHE_CHECK;
//	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
//}


if (!$box_smarty->iscached(CURRENT_TEMPLATE.'/boxes/box_content.html', $cache_id) || !$cache) {

	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

// NEW LINES FOR CONTENT WITH CHILDREN
require_once (DIR_FS_INC . 'shop_content.php');
                 // shop_content(lang, box_flag, parent, css_id, $show_sub) 
$content_string .= shop_content($_SESSION['languages_id'], 2, 0, 'box_content', MODULE_CONTENT_MANAGER_CHILDREN_SHOW);

	if ($content_string != '')
		$box_smarty->assign('BOX_CONTENT', $content_string);

}

if (!$cache) {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_content.html');
} else {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_content.html', $cache_id);
}

$smarty->assign('box_CONTENT', $box_content);
?>

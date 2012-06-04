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
   (c) 2003	 nextcommerce (information.php,v 1.8 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
$content_string = '';

$box_smarty->assign('language', $_SESSION['language']);
// set cache ID
//if (!CacheCheck()) {
	$cache=false;
	$box_smarty->caching = 0;
//} else {
//	$cache=true;
//	$box_smarty->caching = 1;
//	$box_smarty->cache_lifetime = CACHE_LIFETIME;
//	$box_smarty->cache_modified_check = CACHE_CHECK;
//	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
//}

if (!$box_smarty->iscached(CURRENT_TEMPLATE.'/boxes/box_information.html', $cache_id) || !$cache) {
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

// NEW LINES FOR CONTENT WITH CHILDREN
require_once (DIR_FS_INC . 'shop_content.php');
                 /* shop_content(lang, box_flag, parent, css_id, $show_sub) 
                    $show_sub is optional 
                    when you will see sub content let it empty, 
                    write 0 when subchilds not visible
                 */
$content_string .= shop_content($_SESSION['languages_id'], 1, 0, 'box_information', MODULE_CONTENT_MANAGER_CHILDREN_SHOW);

	if ($content_string != '')
		$box_smarty->assign('BOX_CONTENT', $content_string);

}

$box_smarty->assign('NEWSLETTER', '<ul  class="box_information"><li> <a href="' . xtc_href_link(FILENAME_NEWSLETTER, '', 'NONSSL') . '">Newsletter</a></li></ul>');

if (!$cache) {
	$box_information = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_information.html');
} else {
	$box_information = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_information.html', $cache_id);
}

$smarty->assign('box_INFORMATION', $box_information);
?>

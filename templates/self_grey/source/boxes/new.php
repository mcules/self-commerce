<?php

/* -----------------------------------------------------------------------------------------
   $Id: search.php 1262 2005-09-30 10:00:32Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(search.php,v 1.22 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (search.php,v 1.9 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$box_content = '';


$box_smarty->assign('language', $_SESSION['language']);
// set cache ID
 if (!CacheCheck()) {
	$box_smarty->caching = 0;
	$box_search = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_new.html');
} else {
	$box_smarty->caching = 1;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'];
	$box_new = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_new.html', $cache_id);
}

$smarty->assign('box_NEW', $box_new);
?>
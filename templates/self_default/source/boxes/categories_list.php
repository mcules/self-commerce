<?php

/* -----------------------------------------------------------------------------------------
   $Id: categories_list.php 2006-02-01 20:00:00 jvp $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
// reset var

$box_smarty = new smarty;
$box_content = '';

$box_smarty->assign('language', $_SESSION['language']);
// set cache ID
if (!CacheCheck()) {
	$cache=false;
	$box_smarty->caching = 0;
} else {
	$cache=true;
	$box_smarty->caching = 1;
	$box_smarty->cache_lifetime = CACHE_LIFETIME;
	$box_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'].$cPath;
}

if(!$box_smarty->iscached(CURRENT_TEMPLATE.'/boxes/box_categories_list.html', $cache_id) || !$cache){

$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');



$categories_string = '';
if (GROUP_CHECK == 'true') {
	$group_check = "c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1  and";
}
	$categories_query = "Select
					 	c.categories_id,
					 	c.categories_image,
					 	cd.categories_name,
					 	cd.categories_description
					 	From
					 	".TABLE_CATEGORIES." AS c,
					 	".TABLE_CATEGORIES_DESCRIPTION." AS cd
					 	Where
					 	c.categories_id = cd.categories_id AND
					 	c.parent_id = '0' AND
					 	".$group_check." 
					 	c.categories_status = '1' AND
					 	cd.language_id = '" .(int) $_SESSION['languages_id']. "'
					 	Order By
					 	c.sort_order Asc";

	$categories_query = xtDBquery($categories_query);

		while($categories = xtc_db_fetch_array($categories_query, true)) {
			$category_link =xtc_category_link($categories['categories_id'],$categories['categories_name']);
			$box_content[] = array ('CATEGORY_NAME' => $categories['categories_name'],
									'CATEGORY_IMAGE_TRUE' => $categories['categories_image'], 		   
							  		   'CATEGORY_IMAGE' => DIR_WS_IMAGES .'categories/' . $categories['categories_image'], 
							           'CATEGORY_LINK' => xtc_href_link(FILENAME_DEFAULT,  xtc_get_all_get_params(array(array('cat','page','filter_id','manufacturers_id'))) . $category_link), 
							           'CATEGORY_DESCRIPTION' => $categories['categories_description']);
		}
	
	$box_smarty->assign('box_content', $box_content);

}
// set cache ID
if (!$cache) {
	$box_categories_list = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_categories_list.html');
} else {
	$box_categories_list = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_categories_list.html', $cache_id);
}
$smarty->assign('box_CATEGORIES_LIST', $box_categories_list);
?>
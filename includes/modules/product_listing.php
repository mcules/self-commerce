<?php
/* -----------------------------------------------------------------------------------------
   $Id: product_listing.php 1286 2005-10-07 10:10:18Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_listing.php,v 1.42 2003/05/27); www.oscommerce.com 
   (c) 2003	 nextcommerce (product_listing.php,v 1.19 2003/08/1); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$result = true;
// include needed functions
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
$listing_split = new splitPageResults($listing_sql, (int)$_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');
$module_content = array ();
if ($listing_split->number_of_rows > 0) {

	$navigation = '
		<table border="0" width="100%" cellspacing="0" cellpadding="2">
		  <tr>
		    <td class="smallText">'.$listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS).'</td>
		    <td class="smallText" align="right">'.TEXT_RESULT_PAGE.' '.$listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array ('page', 'info', 'x', 'y'))).'</td>
		  </tr>
		</table>';
	if (GROUP_CHECK == 'true') {
		$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}
	$category_query = xtDBquery("select
		                                    cd.categories_description,
		                                    cd.categories_name,
						    cd.categories_heading_title,
		                                    c.listing_template,
		                                    c.categories_image from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
		                                    where c.categories_id = '".$current_category_id."'
		                                    and cd.categories_id = '".$current_category_id."'
		                                    ".$group_check."
		                                    and cd.language_id = '".$_SESSION['languages_id']."'");

	$category = xtc_db_fetch_array($category_query,true);
//self-commerce.de cat<->manu
	
	$manufacturer_query = xtDBquery("select
                                    manufacturers_name,
                                    manufacturers_image
                                    from 
                                    ".TABLE_MANUFACTURERS."
                                    where
                                    manufacturers_id = '".(int) $_GET['manufacturers_id']."'
                                  ");
                                  
  $manufacturer = xtc_db_fetch_array($manufacturer_query,true);
                                    
	$image = '';
	if (isset ($_GET['manufacturers_id'])) {
	// Herstellerbild und Name ausgeben
	if ($manufacturer['manufacturers_image'] != '')
	$image = DIR_WS_IMAGES.$manufacturer['manufacturers_image'];
	$module_smarty->assign('CATEGORIES_IMAGE', $image);	
  $module_smarty->assign('CATEGORIES_NAME',$manufacturer['manufacturers_name']);
	}else{
	// Kategoriebild und Name ausgeben
	if ($category['categories_image'] != '')
	$image = DIR_WS_IMAGES.'categories/'.$category['categories_image'];
	$module_smarty->assign('CATEGORIES_IMAGE', $image);	
	$module_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
	}

	$module_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);
	$module_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);
//self-commerce.de cat<->manu

	$rows = 0;
	$listing_query = xtDBquery($listing_split->sql_query);
	while ($listing = xtc_db_fetch_array($listing_query, true)) {
		$rows ++;
		$module_content[] =  $product->buildDataArray($listing);		
			
	}
} else {

	// no product found
	$result = false;

}
// get default template
if ($category['listing_template'] == '' or $category['listing_template'] == 'default') {
	$files = array ();
	if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/')) {
		while (($file = readdir($dir)) !== false) {
			if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
				$files[] = array ('id' => $file, 'text' => $file);
			} //if
		} // while
		closedir($dir);
	}
	$category['listing_template'] = $files[0]['id'];
}

if ($result != false) {

	$module_smarty->assign('MANUFACTURER_DROPDOWN', $manufacturer_dropdown);
	$module_smarty->assign('SORTING_DROPDOWN', $sorting_dropdown);	
	$module_smarty->assign('language', $_SESSION['language']);
	$module_smarty->assign('module_content', $module_content);

	$module_smarty->assign('NAVIGATION', $navigation);
	// set cache ID
	if (!CacheCheck()) {
		$module_smarty->caching = 0;
		$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_listing/'.$category['listing_template']);
	} else {
		$module_smarty->caching = 1;
		$module_smarty->cache_lifetime = CACHE_LIFETIME;
		$module_smarty->cache_modified_check = CACHE_CHECK;
		// Sessionvariable f¸r Anzahl Artikel pro Seite setzen
		if (isset($_GET['artproseite'])) {
			$_SESSION['artproseite'] = intval($_GET['artproseite']);
		}
		if( $_SESSION['artproseite']==0 || $_SESSION['artproseite']=='') {
			$_SESSION['artproseite']=24; // hier wird der Standard definiert, wieviele Artikel auf einer Seite dargestellt werden sollen
		}
		// Sessionvariable f¸r Ansichtenwahl setzen auch in default.php
		if (isset($_GET['ansicht'])) {
			$_SESSION['ansicht'] = intval($_GET['ansicht']);
		}
		if( $_SESSION['ansicht']==0 || $_SESSION['ansicht']=='') {
			$_SESSION['ansicht']=3; // hier wird der Standard der Ansichtenwahl definiert, 1 = einspaltig 3 = 3-spaltig 6 = 6-spaltig
		}
		$get_params .= isset($_GET['sort']) && $_GET['sort'] > 0 && $_GET['sort'] < 5 ? '_'.(int)$_GET['sort'] : '';
		$get_params .= isset($_SESSION['artproseite']) && $_SESSION['artproseite'] > 0 && $_SESSION['artproseite'] < 49 ? '_'.(int)$_SESSION['artproseite'] : '';
		$get_params .= isset($_SESSION['ansicht']) && $_SESSION['ansicht'] > 0 && $_SESSION['ansicht'] < 7 ? '_'.(int)$_SESSION['ansicht'] : '';
		$cache_id = $current_category_id.'_'.$_SESSION['language'].'_'.$_SESSION['customers_status']['customers_status_name'].'_'.$_SESSION['currency'].'_'.$_GET['manufacturers_id'].'_'.$_GET['filter_id'].'_'.$_GET['page'].'_'.$_GET['keywords'].'_'.$_GET['categories_id'].'_'.$_GET['pfrom'].'_'.$_GET['pto'].'_'.$_GET['x'].'_'.$_GET['y'].'_'.$_GET['sorting_id'];
		$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_listing/'.$category['listing_template'], $cache_id);
	}
	$smarty->assign('main_content', $module);
} else {

	$error = TEXT_PRODUCT_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);
}
?>

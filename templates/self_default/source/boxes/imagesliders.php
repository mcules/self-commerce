<?php

/* -----------------------------------------------------------------------------------------
   $Id: imagesliders.php 001 2008-07-29 16:59:00Z Hetfield $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   Copyright (c) 2008 Hetfield - www.MerZ-IT-SerVice.de
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com
   (c) 2003	 nextcommerce; www.nextcommerce.org   

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
$imagesliders_string = '';
$imagesliders_string_1 = '';
$imagesliders_string_2 = '';
if (file_exists(DIR_FS_INC.'xtc_get_categories_name.inc.php')) require_once(DIR_FS_INC.'xtc_get_categories_name.inc.php');
require_once(DIR_FS_INC.'xtc_get_products_name.inc.php');

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
	$cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
}

if (!$box_smarty->iscached(CURRENT_TEMPLATE.'/boxes/box_imagesliders.html', $cache_id) || !$cache) {
	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

	$imagesliders_query = "SELECT DISTINCT
                         ii.imagesliders_title,
                         ii.imagesliders_url,
						 ii.imagesliders_url_target,
						 ii.imagesliders_url_typ,
                         ii.imagesliders_indicator_class,
                         ii.imagesliders_caption_class,
                         ii.imagesliders_description,
                         ii.imagesliders_image
 					  FROM ".TABLE_IMAGESLIDERS." i, ".TABLE_IMAGESLIDERS_INFO." ii
                      WHERE languages_id='".(int) $_SESSION['languages_id']."'
					  AND i.imagesliders_id = ii.imagesliders_id
					  AND ii.imagesliders_image != ''
					  AND i.status = '0'
                      ORDER BY i.sorting, i.imagesliders_id ASC";

	$imagesliders_query = xtDBquery($imagesliders_query);

  // zusammensetzen des ol-Tags
    $zaehler=0;
	while ($imagesliders_data = xtc_db_fetch_array($imagesliders_query, true)) {  
    if ($imagesliders_data['imagesliders_indicator_class'] == '1'){ $indicator_class = ' white';} else {$indicator_class = '';} 
    if ($zaehler==0) {
      $imagesliders_string_1 .= '<ol class="carousel-indicators">'."\n";
      $imagesliders_string_1 .= '<li class="active' . $indicator_class . '" data-target="#myCarousel" data-slide-to="'.$zaehler.'"></li>'."\n";
    } else {
		  $imagesliders_string_1 .= '<li class="' . $indicator_class . '" data-target="#myCarousel" data-slide-to="'.$zaehler.'"></li>'."\n";
    }
 
  // zusammensetzen der Bilder-Tags
		if ($imagesliders_data['imagesliders_url'] == '') {
			$url = xtc_href_link(FILENAME_DEFAULT);
		}  else {
			if ($imagesliders_data['imagesliders_url_target'] == '0') {
				$target = '';
			} elseif ($imagesliders_data['imagesliders_url_target'] == '1') {
				$target = ' target="_blank"';
			} elseif ($imagesliders_data['imagesliders_url_target'] == '2') {
				$target = ' target="_top"';
			} elseif ($imagesliders_data['imagesliders_url_target'] == '3') {
				$target = ' target="_self"';
			} elseif ($imagesliders_data['imagesliders_url_target'] == '4') {
				$target = ' target="_parent"';
			}
			if ($imagesliders_data['imagesliders_url_typ'] == '0') {
				$url = $imagesliders_data['imagesliders_url'];
			} elseif ($imagesliders_data['imagesliders_url_typ'] == '1') {
				$url = xtc_href_link($imagesliders_data['imagesliders_url']);
			} elseif ($imagesliders_data['imagesliders_url_typ'] == '2') {
				$url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link((int)$imagesliders_data['imagesliders_url'],xtc_get_products_name((int)$imagesliders_data['imagesliders_url'])));
			} elseif ($imagesliders_data['imagesliders_url_typ'] == '3') {
				$url = xtc_href_link(FILENAME_DEFAULT, xtc_category_link((int)$imagesliders_data['imagesliders_url'],xtc_get_categories_name((int)$imagesliders_data['imagesliders_url'])));
			} elseif ($imagesliders_data['imagesliders_url_typ'] == '4') {
				$content_querys = "SELECT content_title FROM ".TABLE_CONTENT_MANAGER." WHERE languages_id='".(int) $_SESSION['languages_id']."' and content_group = '".(int)$imagesliders_data['imagesliders_url']."' and content_status=1 order by sort_order";
				$content_querys = xtDBquery($content_querys);
				$content_title = xtc_db_fetch_array($content_querys,true);				
				$SEF = '';
				if (SEARCH_ENGINE_FRIENDLY_URLS == 'true'){
					$SEF = '&content='.xtc_cleanName($content_title['content_title']);
				}
				$url = xtc_href_link(FILENAME_CONTENT, 'coID='.(int)$imagesliders_data['imagesliders_url'].$SEF);
			}			
		}
      if ($imagesliders_data['imagesliders_caption_class'] == '1'){ $caption_class = ' relative';} else {$caption_class = '';} 
    if ($zaehler==0) {
      $imagesliders_string_2 .= '<div class="carousel-inner">'."\n";
      $imagesliders_string_2 .= '<div class="item active">'."\n";
      $imagesliders_string_2 .= '<a href="'.$url.'" title="'.$imagesliders_data['imagesliders_title'].'"'.$target.'>'.xtc_image(DIR_WS_IMAGES.$imagesliders_data['imagesliders_image'],$imagesliders_data['imagesliders_title']).'</a>'."\n";
      $imagesliders_string_2 .= '<div class="carousel-caption'. $caption_class .' text-center">'."\n";
      $imagesliders_string_2 .= '<h4>'.$imagesliders_data['imagesliders_title'].'</h4>'."\n";
      $imagesliders_string_2 .= $imagesliders_data['imagesliders_description']."\n";
      $imagesliders_string_2 .= '</div>'."\n";
      $imagesliders_string_2 .= '</div>'."\n";
    } else {
      $imagesliders_string_2 .= '<div class="item">'."\n";
      $imagesliders_string_2 .= '<a href="'.$url.'" title="'.$imagesliders_data['imagesliders_title'].'"'.$target.'>'.xtc_image(DIR_WS_IMAGES.$imagesliders_data['imagesliders_image'],$imagesliders_data['imagesliders_title']).'</a>'."\n";
      $imagesliders_string_2 .= '<div class="carousel-caption'. $caption_class .' text-center">'."\n";
      $imagesliders_string_2 .= '<h4>'.$imagesliders_data['imagesliders_title'].'</h4>'."\n";
      $imagesliders_string_2 .= $imagesliders_data['imagesliders_description']."\n";
      $imagesliders_string_2 .= '</div>'."\n";
      $imagesliders_string_2 .= '</div>'."\n";
    }
    $zaehler++;

	}
    if (($imagesliders_string_1 != '') && ($imagesliders_string_2 != '')) {
      $imagesliders_string_1 .= '</ol>'."\n";
      $imagesliders_string_2 .= '</div>'."\n";
    }
    
    // Strings zusammensetzen
		$imagesliders_string .= $imagesliders_string_1.$imagesliders_string_2;

	if ($imagesliders_string != '')
		$box_smarty->assign('BOX_IMAGESLIDER', $imagesliders_string);
		
	if ($slidermenu != '')
		$box_smarty->assign('BOX_SLIDERMENU', $slidermenu);

}

if (!$cache) {
	$box_imagesliders = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_imagesliders.html');
} else {
	$box_imagesliders = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_imagesliders.html', $cache_id);
}

$smarty->assign('box_IMAGESLIDER', $box_imagesliders);
?>
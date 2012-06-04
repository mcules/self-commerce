<?php

/* -----------------------------------------------------------------------------------------
   $Id: content.php 1302 2007-01-04 16:21:29Z mz $

   Zerosoftware GbR for
   YAML for XT:Commerce http://www.zs-ecommerce.com

   Copyright (c) 2007 Björn Teßmann for Zerosoftware GbR zerosoft.de
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(information.php,v 1.6 2003/02/10); www.oscommerce.com
   (c) 2003-2005 nextcommerce (information.php,v 1.8 2003/08/21); www.nextcommerce.org
   (c) 2005-2007 XT-Commerce  (content.php,v 1.8 2005/10/12); www.xt-commerce.com
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
$content_string = '';

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

if (!$box_smarty->iscached(CURRENT_TEMPLATE.'/boxes/box_content.html', $cache_id) || !$cache) {

	$box_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');

	if (GROUP_CHECK == 'true') {
		$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
	}

	$content_query = "SELECT
	 					content_id,
	 					categories_id,
	 					parent_id,
	 					content_title,
	 					content_group
	 					FROM ".TABLE_CONTENT_MANAGER."
	 					WHERE languages_id='".(int) $_SESSION['languages_id']."'
	 					and file_flag=1 ".$group_check." and content_status=1 order by sort_order";

	$content_query = xtDBquery($content_query);

	$content_string .= '<ul class="conandinfo">';
	while ($content_data = xtc_db_fetch_array($content_query, true)) {
		$SEF_parameter = '';
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			$SEF_parameter = '&content='.xtc_cleanName($content_data['content_title']);
		// Kennzeichnung des aktiven Inhaltselements
		if ($_GET['coID'] == $content_data['content_group'].$SEF_parameter) {
				$content_string .= '<li class="activeContent">' ;
			} else {
				$content_string .= '<li>' ;
			}

		$content_string .= '<a href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.$content_data['content_group'].$SEF_parameter).'">'.$content_data['content_title'].'</a></li>';
	}
	$content_string .= '</ul>';

	if ($content_string != '<ul class="conandinfo"></ul>')
		$box_smarty->assign('BOX_CONTENT', $content_string);

}

if (!$cache) {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_content.html');
} else {
	$box_content = $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_content.html', $cache_id);
}

$smarty->assign('box_CONTENT', $box_content);
?>
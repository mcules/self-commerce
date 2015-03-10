<?php
// default page
if (GROUP_CHECK == 'true') {
	$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
}
$shop_content_query = xtDBquery("SELECT
                     content_title,
                     content_heading,
                     content_text,
                     content_file
                     FROM ".TABLE_CONTENT_MANAGER."
                     WHERE content_group='5'
                     ".$group_check."
                     AND languages_id='".$_SESSION['languages_id']."'");
$shop_content_data = xtc_db_fetch_array($shop_content_query,true);

$default_smarty->assign('title', $shop_content_data['content_heading']);
include (DIR_WS_INCLUDES.FILENAME_CENTER_MODULES);

if ($shop_content_data['content_file'] != '') {
	ob_start();
	if (strpos($shop_content_data['content_file'], '.txt'))
		echo '<pre>';
	include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
	if (strpos($shop_content_data['content_file'], '.txt'))
		echo '</pre>';
	$shop_content_data['content_text'] = ob_get_contents();
	ob_end_clean();
}

$default_smarty->assign('text', str_replace('{$greeting}', xtc_customer_greeting(), $shop_content_data['content_text']));
$default_smarty->assign('language', $_SESSION['language']);

// Sessionvariable f¸r Anzahl Artikel pro Seite setzen
if (isset($_GET['artproseite'])) {
	$_SESSION['artproseite'] = intval($_GET['artproseite']);
}
if( $_SESSION['artproseite']==0 || $_SESSION['artproseite']=='') {
	$_SESSION['artproseite']=24; // hier wird der Standard definiert, wieviele Artikel auf einer Seite dargestellt werden sollen
}
// Sessionvariable f¸r Ansichtenwahl Kategorieansicht
if (isset($_GET['ansicht_cat'])) {
	$_SESSION['ansicht_cat'] = intval($_GET['ansicht_cat']);
}
if( $_SESSION['ansicht_cat']==0 || $_SESSION['ansicht_cat']=='') {
	$_SESSION['ansicht_cat']=2; // hier wird der Standard der Ansichtenwahl definiert, 1 = einspaltig 2 = 2-spaltig 3 = 3-spaltig
}
$get_params .= isset($_GET['sort']) && $_GET['sort'] > 0 && $_GET['sort'] < 5 ? '_'.(int)$_GET['sort'] : '';
$get_params .= isset($_SESSION['artproseite']) && $_SESSION['artproseite'] > 0 && $_SESSION['artproseite'] < 49 ? '_'.(int)$_SESSION['artproseite'] : '';
$get_params .= isset($_SESSION['ansicht']) && $_SESSION['ansicht'] > 0 && $_SESSION['ansicht'] < 7 ? '_'.(int)$_SESSION['ansicht'] : '';

// set cache ID
 if (!CacheCheck()) {
	$default_smarty->caching = 0;
	$main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html');
} else {
	$default_smarty->caching = 1;
	$default_smarty->cache_lifetime = CACHE_LIFETIME;
	$default_smarty->cache_modified_check = CACHE_CHECK;
	$cache_id = $_SESSION['language'].$_SESSION['currency'].$_SESSION['customer_id'];
	$cache_id = $cache_id.$get_params;
	$main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html', $cache_id);
}

$smarty->assign('main_content', $main_content);
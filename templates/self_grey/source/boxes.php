<?php
/* -----------------------------------------------------------------------------------------
$Id: boxes.php 1298 2005-10-09 13:14:44Z mz $   

XT-Commerce - community made shopping
http://www.xt-commerce.com

Copyright (c) 2003 XT-Commerce
-----------------------------------------------------------------------------------------
Released under the GNU General Public License 
---------------------------------------------------------------------------------------*/

define('DIR_WS_BOXES',DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/source/boxes/');

include(DIR_WS_BOXES . 'categories.php');
include(DIR_WS_BOXES . 'manufacturers.php');
if ($_SESSION['customers_status']['customers_status_show_price']!='0') {
	require(DIR_WS_BOXES . 'add_a_quickie.php');
}
require(DIR_WS_BOXES . 'last_viewed.php');
if (substr(basename($PHP_SELF), 0,8) != 'advanced') {require(DIR_WS_BOXES . 'whats_new.php'); }
require(DIR_WS_BOXES . 'search.php');
require(DIR_WS_BOXES . 'content.php');
require(DIR_WS_BOXES . 'information.php');
include(DIR_WS_BOXES . 'languages.php');
if ($_SESSION['customers_status']['customers_status_id'] == 0) include(DIR_WS_BOXES . 'admin.php');
require(DIR_WS_BOXES . 'infobox.php');
require(DIR_WS_BOXES . 'loginbox.php');
include(DIR_WS_BOXES . 'newsletter.php');
include(DIR_WS_BOXES . 'extra1.php');
include(DIR_WS_BOXES . 'extra2.php');
if ($_SESSION['customers_status']['customers_status_show_price'] == 1) include(DIR_WS_BOXES . 'shopping_cart.php');
if ($product->isProduct()) include(DIR_WS_BOXES . 'manufacturer_info.php');

if (isset($_SESSION['customer_id'])) {
	include(DIR_WS_BOXES . 'order_history.php');
	$smarty->assign("customer_id", $_SESSION['customer_id']);
}

if (!$product->isProduct()) {
	include(DIR_WS_BOXES . 'best_sellers.php');
}

if (!$product->isProduct()) {
	include(DIR_WS_BOXES . 'specials.php');
}

if ($_SESSION['customers_status']['customers_status_read_reviews'] == 1) require(DIR_WS_BOXES . 'reviews.php');

if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {
	include(DIR_WS_BOXES . 'currencies.php');
}
$cur_link = explode('/', $_SERVER['PHP_SELF']);
$cur_link = array_pop($cur_link);
if (strpos($cur_link, '?') === true ) {
	$pos = strpos($cur_link, '?', 1);
	if ($pos) {
		$cur_link = substr($cur_link, 0, $pos);
	}
}

if ($_SESSION['language'] == "german") {
	$fehler = array(404 => 'Fehler 404: Die gesuchte Seite wurde nicht gefunden!',
					401 => "Fehler 401: Authentifizierungsfehler.",
					400 => "Fehler 400: Die Anforderung war syntaktisch falsch.",
					403 => "Fehler 403: Der Server verweigert die AusfŸhrung.",
					500 => "Fehler 500: Beim Server gab es einen internen Fehler.");
}
else {
	$fehler = array(404 => 'Error 404: Not Found!',
					401 => "Error 401: Unauthorized.",
					400 => "Error 400: Bad Request.",
					403 => "Error 403: Forbidden.",
					500 => "Error 500: Internal Server Error.");
}

$smarty->assign("herror", $fehler[$_REQUEST['error']]);
$smarty->assign('cur_link', $cur_link);
$smarty->assign('cur_language', $_SESSION['language']);
$smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
?>

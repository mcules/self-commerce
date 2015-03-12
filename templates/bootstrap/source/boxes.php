<?php
/* -----------------------------------------------------------------------------------------
   $Id: boxes.php 3409 2012-08-10 12:47:17Z web28 $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 XT-Commerce
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
    // Karl 06.02.2014 include extra Definitionen
    define('DIR_FS_LANGXTRA',DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/langxtra/');
    define('LANGXTRA',DIR_FS_LANGXTRA  . $_SESSION['language'] . '.php');
    if(file_exists(LANGXTRA)) require(LANGXTRA);

// BOF - Tomcraft - 2009-10-27 - Prevent duplicate content, see: http://www.gunnart.de/tipps-und-tricks/doppelten-content-vermeiden-productredirect-fuer-xtcommerce/
  require_once (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/inc/gunnart_productRedirect.inc.php');
// EOF - Tomcraft - 2009-10-27 - Prevent duplicate content, see: http://www.gunnart.de/tipps-und-tricks/doppelten-content-vermeiden-productredirect-fuer-xtcommerce/



  define('DIR_WS_BOXES',DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/source/boxes/');

//BOC require boxes
// -----------------------------------------------------------------------------------------
//	Immer sichtbar
// -----------------------------------------------------------------------------------------
  require_once(DIR_WS_BOXES . 'categories.php');
  // Karl 18.02.2014 Imageslider (c) 2008 by Hetfield -www.MerZ-IT-SerVice.de – Begin
if (file_exists(DIR_FS_INC.'xtc_get_categories_name.inc.php')){
  if (strstr($PHP_SELF, FILENAME_DEFAULT)) {
  if (!isset ($_GET['manufacturers_id'])) {
  if (isset ($cPath) && xtc_not_null($cPath)) { } else { require(DIR_WS_BOXES . 'imagesliders.php'); }
  }}}
  // Karl 18.02.2014 Imageslider (c) 2008 by Hetfield – www.MerZ-IT-SerVice.de – End
  require_once(DIR_WS_BOXES . 'categories_list.php');
  require_once(DIR_WS_BOXES . 'categories_superfish.php');
  require_once(DIR_WS_BOXES . 'manufacturers.php');
  require_once(DIR_WS_BOXES . 'last_viewed.php');
  require_once(DIR_WS_BOXES . 'search.php');
  require_once(DIR_WS_BOXES . 'search_header.php');
  require_once(DIR_WS_BOXES . 'service.php');
  require_once(DIR_WS_BOXES . 'content.php');
  require_once(DIR_WS_BOXES . 'information.php');
  require_once(DIR_WS_BOXES . 'languages.php'); 
  require_once(DIR_WS_BOXES . 'infobox.php');
  require_once(DIR_WS_BOXES . 'loginbox.php');
  require_once(DIR_WS_BOXES . 'newsletter.php');
// -----------------------------------------------------------------------------------------
//	Nur, wenn Preise sichtbar
// -----------------------------------------------------------------------------------------
  if ($_SESSION['customers_status']['customers_status_show_price'] == 1) {
    require_once(DIR_WS_BOXES . 'add_a_quickie.php');
    require_once(DIR_WS_BOXES . 'shopping_cart.php');
    require_once(DIR_WS_BOXES . 'shopping_cart_header.php');
  }
// -----------------------------------------------------------------------------------------
//	In der Suche verborgen
// -----------------------------------------------------------------------------------------
  if (substr(basename($PHP_SELF), 0,8) != 'advanced') {
    require_once(DIR_WS_BOXES . 'whats_new.php'); 
  }
// -----------------------------------------------------------------------------------------
//	Nur fuer Admins
// -----------------------------------------------------------------------------------------
  if ($_SESSION['customers_status']['customers_status_id'] == 0) {
    require_once(DIR_WS_BOXES . 'admin.php');
    $smarty->assign('is_admin', true);
  }
// -----------------------------------------------------------------------------------------
//	Produkt-Detailseiten
// -----------------------------------------------------------------------------------------
  if ($product->isProduct()) {
    //Aktuelle Seite ist Produkt-Detailseite
    require_once(DIR_WS_BOXES . 'manufacturer_info.php');
  } else {
    //Aktuelle Seite ist keine  Produkt-Detailseite
    require_once(DIR_WS_BOXES . 'best_sellers.php');
    require_once(DIR_WS_BOXES . 'specials.php');
  }
// -----------------------------------------------------------------------------------------
//	Nur fuer eingeloggte Besucher
// -----------------------------------------------------------------------------------------
  if (isset($_SESSION['customer_id'])) {
    require_once(DIR_WS_BOXES . 'order_history.php');
  }
// -----------------------------------------------------------------------------------------
//	Nur, wenn Bewertungen erlaubt
// -----------------------------------------------------------------------------------------
  if ($_SESSION['customers_status']['customers_status_read_reviews'] == 1) {
    require_once(DIR_WS_BOXES . 'reviews.php');
  }
// -----------------------------------------------------------------------------------------
//	Waehrend des Kauf-Abschlusses verborgen 
// -----------------------------------------------------------------------------------------
  if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {
    require_once(DIR_WS_BOXES . 'currencies.php');
  }
// -----------------------------------------------------------------------------------------
//EOC require boxes

// -----------------------------------------------------------------------------------------
// Smarty Zuweisung Startseite
// -----------------------------------------------------------------------------------------
$smarty->assign('home', strpos($PHP_SELF, 'index')!==false && !isset($_GET['cPath']) && !isset($_GET['manufacturers_id']) ? 1 : 0);
// -----------------------------------------------------------------------------------------

$smarty->assign('tpl_path',DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/');
?>
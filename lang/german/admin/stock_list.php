<?php
/*------------------------------------------------------------------------------
  $Id: stock_list.php,v 1.00 2006/05/21 GERMANN.NEWMEDIA

  XTC-CC - Contribution for XT-Commerce http://www.xt-commerce.com
  -----------------------------------------------------------------------------

  Copyright (c) 2006 GNM
  http://www.internetauftritte.ch

  Released under the GNU General Public License
------------------------------------------------------------------------------*/


define('SHOW_PAGE_STANDARD', 20); // Standardwert
$pages_array = array();
$pages_array[] = array('id' => '20', 'text' => '20 Artikel pro Seite zeigen');
$pages_array[] = array('id' => '50', 'text' => '50 Artikel pro Seite zeigen');
$pages_array[] = array('id' => '100', 'text' => '100 Artikel pro Seite zeigen');
$pages_array[] = array('id' => '500', 'text' => '500 Artikel pro Seite zeigen');
$pages_array[] = array('id' => '1000', 'text' => '1000 Artikel pro Seite zeigen');
			 

define('HEADING_TITLE', 'Lagerliste');

define('TABLE_HEADING_STOCKLIST', 'Artikel-Lagerliste');
define('HEADING_TITLE_SEARCH', 'Suche');

define('TABLE_HEADING_STOCKLIST_ID', 'ID#');
define('TABLE_HEADING_STOCKLIST_STATUS', 'Status');
define('TABLE_HEADING_STOCKLIST_NUMBER', 'Artikelnummer');
define('TABLE_HEADING_STOCKLIST_DESCR', 'Bezeichnung');
define('TABLE_HEADING_STOCKLIST_OPTION', 'Optionsname');
define('TABLE_HEADING_STOCKLIST_OPTIONVALUE', 'Optionswert');
define('TABLE_HEADING_STOCKLIST_OPTIONSTOCK', 'Lagerbestand');
define('TABLE_HEADING_STOCKLIST_STOCK', 'Lagerbestand');


define('TEXT_SHOW_PRODUCTS', 'Alle Artikel mit dieser Bezeichnung zum Bearbeiten anzeigen');

?>

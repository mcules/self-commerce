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
$pages_array[] = array('id' => '20', 'text' => 'show 20 articles per side');
$pages_array[] = array('id' => '50', 'text' => 'show 50 articles per side');
$pages_array[] = array('id' => '100', 'text' => 'show 100 articles per side');
$pages_array[] = array('id' => '500', 'text' => 'show 500 articles per side');
$pages_array[] = array('id' => '1000', 'text' => 'show 1000 articles per side');
			 

define('HEADING_TITLE', 'Lagerliste');

define('TABLE_HEADING_STOCKLIST', 'Stock list');
define('HEADING_TITLE_SEARCH', 'Search');

define('TABLE_HEADING_STOCKLIST_ID', 'ID#');
define('TABLE_HEADING_STOCKLIST_STATUS', 'Status');
define('TABLE_HEADING_STOCKLIST_NUMBER', 'Article number');
define('TABLE_HEADING_STOCKLIST_DESCR', 'Designation');
define('TABLE_HEADING_STOCKLIST_OPTION', 'Optionsname');
define('TABLE_HEADING_STOCKLIST_OPTIONVALUE', 'Optionswert');
define('TABLE_HEADING_STOCKLIST_OPTIONSTOCK', 'Stock');
define('TABLE_HEADING_STOCKLIST_STOCK', 'Stock');


define('TEXT_SHOW_PRODUCTS', 'All articles with this designation for working on indicate');

?>

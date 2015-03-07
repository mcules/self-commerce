<?php
/* --------------------------------------------------------------
Amazon Advanced Payment APIs Modul  V2.00
   checkout_amazon.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

Released under the GNU General Public License
--------------------------------------------------------------
*/
?><?php
include('includes/application_top.php');
include_once('lang/' . $_SESSION["language"] . '/modules/payment/am_apa.php');
$breadcrumb->add(AMAZON_CHECKOUT, xtc_href_link('checkout_amazon_login_processing.php', '', 'SSL'));

// create smarty elements
$smarty              = new Smarty;
// include boxes
require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions

if($_GET["action"] == 'checkout'){
    $_SESSION["amazon_target"] = 'checkout';
}
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content    = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_amazon_login_processing.html');
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
    $smarty->load_filter('output', 'note');
require(DIR_WS_INCLUDES . 'header.php');    
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include('includes/application_bottom.php');
?>

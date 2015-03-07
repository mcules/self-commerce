<?php
/* --------------------------------------------------------------
	Amazon Advanced Payment APIs Modul  V2.00
   	checkout_amazon_wallet.php 2014-06-03

   	alkim media
   	http://www.alkim.de

   	patworx multimedia GmbH
   	http://www.patworx.de/

	Released under the GNU General Public License
--------------------------------------------------------------
*/
?><?php
include('includes/application_top.php');
include_once('AmazonAdvancedPayment/.config.inc.php');
include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
include_once('lang/' . $_SESSION["language"] . '/modules/payment/am_apa.php');

// Check if Amazon ID is set and Amazon is allowed
if ((isset($_SESSION['amazon_id'])) && ($_SESSION['amazon_id'] != '') && (MODULE_PAYMENT_AM_APA_STATUS == 'True')) {
    $amazon_id = $_SESSION['amazon_id'];
} else {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

$_SESSION["fromWallet"] = 1;
$breadcrumb->add(AMAZON_CHECKOUT, xtc_href_link('checkout_amazon.php', '', 'SSL'));
// create smarty elements
$smarty              = new Smarty;
// include boxes
require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions


$smarty->assign('BUTTON_CONTINUE', '<img title="' . IMAGE_BUTTON_CONFIRM_ORDER . '" alt="'.IMAGE_BUTTON_CONFIRM_ORDER.'" src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/button_confirm_order.gif">');
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content    = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_amazon_wallet.html');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
    $smarty->load_filter('output', 'note');

require(DIR_WS_INCLUDES . 'header.php');
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include('includes/application_bottom.php');
?>

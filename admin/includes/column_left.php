<?php
/* --------------------------------------------------------------
   $Id: column_left.php 58 2012-10-07 23:01:41Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(column_left.php,v 1.15 2002/01/11); www.oscommerce.com
   (c) 2003	 nextcommerce (column_left.php,v 1.25 2003/08/19); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  $admin_access_query = xtc_db_query("SELECT * FROM " . TABLE_ADMIN_ACCESS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "'");
  $admin_access = xtc_db_fetch_array($admin_access_query);
?>
<tr>
<td>
<script type="text/javascript" src="includes/javascript/collumn_left_slide.js"></script>
<div id="dhtmlgoodies_xpPane">
<?php
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['customers'] == '1')) $box_customers .= '<a href="' . xtc_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CUSTOMERS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['customers_status'] == '1')) $box_customers .= '<a href="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CUSTOMERS_STATUS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['orders'] == '1')) $box_customers .= '<a href="' . xtc_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ORDERS . '</a><br />';
if(!empty($box_customers))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_customers.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['categories'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CATEGORIES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['new_attributes'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_NEW_ATTRIBUTES, '', 'NONSSL') . '" class="menuBoxContentLink"> -'.BOX_ATTRIBUTES_MANAGER.'</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['products_attributes'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_PRODUCTS_ATTRIBUTES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['manufacturers'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_MANUFACTURERS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['suppliers'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_SUPPLIERS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_SUPPLIERS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['reviews'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_REVIEWS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['specials'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_SPECIALS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_SPECIALS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['products_expected'] == '1')) $box_products .= '<a href="' . xtc_href_link(FILENAME_PRODUCTS_EXPECTED, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_PRODUCTS_EXPECTED . '</a><br />';
if(!empty($box_products))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_products.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['modules'] == '1')) $box_modules .= '<a href="' . xtc_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_PAYMENT . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['modules'] == '1')) $box_modules .= '<a href="' . xtc_href_link(FILENAME_MODULES, 'set=shipping', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_SHIPPING . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['modules'] == '1')) $box_modules .= '<a href="' . xtc_href_link(FILENAME_MODULES, 'set=ordertotal', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ORDER_TOTAL . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['module_export'] == '1')) $box_modules .= '<a href="' . xtc_href_link(FILENAME_MODULE_EXPORT) . '" class="menuBoxContentLink"> -' . BOX_MODULE_EXPORT . '</a><br />';
if(!empty($box_modules))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_modules.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['stats_products_viewed'] == '1')) $box_statistics .= '<a href="' . xtc_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_PRODUCTS_VIEWED . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['stats_products_purchased'] == '1')) $box_statistics .= '<a href="' . xtc_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_PRODUCTS_PURCHASED . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['stats_customers'] == '1')) $box_statistics .= '<a href="' . xtc_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_STATS_CUSTOMERS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['stats_sales_report'] == '1')) $box_statistics .= '<a href="' . xtc_href_link(FILENAME_SALES_REPORT, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_SALES_REPORT . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['stats_campaigns'] == '1')) $box_statistics .= '<a href="' . xtc_href_link(FILENAME_CAMPAIGNS_REPORT, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CAMPAIGNS_REPORT . '</a><br />';
if(!empty($box_statistics))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_statistics.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['module_newsletter'] == '1'))	$box_tools .= '<a href="' . xtc_href_link(FILENAME_MODULE_NEWSLETTER) . '" class="menuBoxContentLink"> -' . BOX_MODULE_NEWSLETTER . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['content_manager'] == '1'))	$box_tools .= '<a href="' . xtc_href_link(FILENAME_CONTENT_MANAGER) . '" class="menuBoxContentLink"> -' . BOX_CONTENT . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['blacklist'] == '1'))			$box_tools .= '<a href="' . xtc_href_link(FILENAME_BLACKLIST, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_TOOLS_BLACKLIST . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['backup'] == '1'))			$box_tools .= '<a href="' . xtc_href_link(FILENAME_BACKUP) . '" class="menuBoxContentLink"> -' . BOX_BACKUP . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['banner_manager'] == '1'))	$box_tools .= '<a href="' . xtc_href_link(FILENAME_BANNER_MANAGER) . '" class="menuBoxContentLink"> -' . BOX_BANNER_MANAGER . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['server_info'] == '1'))		$box_tools .= '<a href="' . xtc_href_link(FILENAME_SERVER_INFO) . '" class="menuBoxContentLink"> -' . BOX_SERVER_INFO . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['whos_online'] == '1'))		$box_tools .= '<a href="' . xtc_href_link(FILENAME_WHOS_ONLINE) . '" class="menuBoxContentLink"> -' . BOX_WHOS_ONLINE . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['csv_backend'] == '1'))		$box_tools .= '<a href="' . xtc_href_link('csv_backend.php') . '" class="menuBoxContentLink"> -' . BOX_IMPORT . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['file_chk'] == '1'))			$box_tools .= '<a href="' . xtc_href_link(FILENAME_FILE_CHK) . '" class="menuBoxContentLink"> -' . BOX_FILE_CHK . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['paypal'] == '1'))			$box_tools .= '<a href="' . xtc_href_link('paypal.php') . '" class="menuBoxContentLink"> -' . BOX_PAYPAL . '</a><br>';
if(!empty($box_tools))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_tools.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['coupon_admin'] == '1')) $box_gift_system .= '<a href="' . xtc_href_link(FILENAME_COUPON_ADMIN, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_COUPON_ADMIN . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['gv_queue'] == '1')) $box_gift_system .= '<a href="' . xtc_href_link(FILENAME_GV_QUEUE, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_GV_ADMIN_QUEUE . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['gv_mail'] == '1')) $box_gift_system .= '<a href="' . xtc_href_link(FILENAME_GV_MAIL, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_GV_ADMIN_MAIL . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['gv_sent'] == '1')) $box_gift_system .= '<a href="' . xtc_href_link(FILENAME_GV_SENT, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_GV_ADMIN_SENT . '</a><br />';
if(!empty($box_gift_system) && ACTIVATE_GIFT_SYSTEM=='true')
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_gift_system.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['languages'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_LANGUAGES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_LANGUAGES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['countries'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_COUNTRIES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_COUNTRIES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['currencies'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_CURRENCIES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CURRENCIES. '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['zones'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_ZONES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ZONES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['geo_zones'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_GEO_ZONES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_GEO_ZONES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['tax_classes'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_TAX_CLASSES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_TAX_CLASSES . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['tax_rates'] == '1')) $box_zone .= '<a href="' . xtc_href_link(FILENAME_TAX_RATES, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_TAX_RATES . '</a><br />';
if(!empty($box_zone))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_zone.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=1', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_1 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=2', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_2 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=3', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_3 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=4', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_4 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=5', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_5 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=7', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_7 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=8', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_8 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=9', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_9 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=10', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_10 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=11', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_11 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=12', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_12 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=13', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_13 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=14', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_14 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=15', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_15 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=16', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_16 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=17', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_17 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=18', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_18 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=19', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_19 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=22', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_22 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['orders_status'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ORDERS_STATUS . '</a><br />';
if (ACTIVATE_SHIPPING_STATUS=='true') {
    if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['shipping_status'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_SHIPPING_STATUS . '</a><br />';
}
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['products_vpe'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_PRODUCTS_VPE . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['campaigns'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CAMPAIGNS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CAMPAIGNS . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['cross_sell_groups'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_XSELL_GROUPS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ORDERS_XSELL_GROUP . '</a><br />';
// PDFBill NEXT Start
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=99', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_99 . '</a><br />';
// PDFBill NEXT End
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<li><a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=333', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_333 . '</a></li>';
  if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_configuration .= '<li><a href="' . xtc_href_link('cox_sort.php', '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_COX_SORT . '</a></li>';
if(!empty($box_configuration))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_configuration.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=358', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_358 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=359', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_359 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=360', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_360 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=361', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_361 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=Piwik Analytics', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_PIWIK . '</a><br />';
if(!empty($box_extra_modules)) $box_extra_modules .= '<hr>';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['admin_sql'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_ADMIN_SQL, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ADMIN_SQL . '</a><br />';
if(!empty($box_extra_modules)) $box_extra_modules .= '<hr>';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['stock_list'] == '1')) $box_extra_modules .= '<a href="' . xtc_href_link(FILENAME_STOCK_LIST, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_STOCK_LIST . '</a><br />';
/* Haendlerbund AGB- Schnittstelle Beginn */
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['haendlerbund'] == '1')) $box_extra_modules .=  '<a href="' . xtc_href_link("haendlerbund.php", '') . '" class="menuBoxContentLink"> -' . 'AGB Service' . '</a>';
/* Haendlerbund AGB- Schnittstelle Ende */
if(!empty($box_extra_modules))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_extra_modules.'</div>
    </div>';

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['token_admin'] == '1')) $box_security .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=365', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_TOKEN_ADMIN . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['token_admin'] == '1')) $box_security .= '<a href="' . xtc_href_link(FILENAME_TOKEN_ADMIN, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_TOKEN_USER . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_security .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=362', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_362 . '</a><br />';
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) $box_security .= '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=363', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_363 . '</a><br />';
if(!empty($box_security))
    echo '<div class="dhtmlgoodies_panel">
        <div>'.$box_security.'</div>
    </div>';
?>
</div>
<?php
	$panelHead = '';
	$panelHead .= (!empty($box_customers)) ? '\''. BOX_HEADING_CUSTOMERS .'\',':'';
	$panelHead .= (!empty($box_products)) ? '\''. BOX_HEADING_PRODUCTS .'\',':'';
	$panelHead .= (!empty($box_modules)) ? '\''. BOX_HEADING_MODULES .'\',':'';
	$panelHead .= (!empty($box_statistics)) ? '\''. BOX_HEADING_STATISTICS .'\',':'';
	$panelHead .= (!empty($box_tools)) ? '\''. BOX_HEADING_TOOLS .'\',':'';
	$panelHead .= (!empty($box_gift_system) && ACTIVATE_GIFT_SYSTEM=='true') ? '\''.BOX_HEADING_GV_ADMIN.'\',':'';
	$panelHead .= (!empty($box_zone)) ? '\''.(BOX_HEADING_ZONE).'\',':'';
	$panelHead .= (!empty($box_configuration)) ? '\''. BOX_HEADING_CONFIGURATION .'\',':'';
	$panelHead .= (!empty($box_extra_modules)) ? '\''. BOX_HEADING_EXTRA_MODULES .'\',':'';
	$panelHead .= (!empty($box_security)) ? '\''. BOX_HEADING_SECURITY .'\',':'';
	$panelHead = substr($panelHead,0,-1);
?>
<!-- START OF PANE CODE -->
<script type="text/javascript">
initDhtmlgoodies_xpPane(Array(<?php echo $panelHead ?>),
				Array(false,false,false,false,false,<?php echo (!empty($box_gift_system) && ACTIVATE_GIFT_SYSTEM=='true') ? 'false,':''; ?>false,false,false,false),
				Array('pane1','pane2','pane3','pane4','pane5',<?php echo (!empty($box_gift_system) && ACTIVATE_GIFT_SYSTEM=='true') ? '\'pane6\',':''; ?>'pane7','pane8','pane9','panel10')
			);
</script>
<!-- END OF PANE CONTENT -->
</td>
</tr>

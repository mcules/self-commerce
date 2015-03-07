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
require_once (DIR_FS_INC.'xtc_validate_password.inc.php');
require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');
include_once('lang/' . $_SESSION["language"] . '/modules/payment/am_apa.php');
$breadcrumb->add(AMAZON_CHECKOUT, xtc_href_link('checkout_amazon_connect_accounts.php', '', 'SSL'));
$error = false;

switch($_POST["action"]){


    case 'tryConnect':
        if($_SESSION["amzConnectEmail"] != $_POST["email"]){
            $error = true;
            break;
        }
        $check_customer_query = xtc_db_query("select customers_id, customers_vat_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, customers_default_address_id from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($_POST["email"])."' and account_type = '0'");
	    if (!xtc_db_num_rows($check_customer_query)) {
		    $error = true;
            break;
	    } else {
		    $check_customer = xtc_db_fetch_array($check_customer_query);
		    $password = xtc_db_prepare_input($_POST['password']);
		    if (!xtc_validate_password($password, $check_customer['customers_password'])) {
		        $error = true;
                break;
            }else{
                if (SESSION_RECREATE == 'True') {
				    xtc_session_recreate();
			    }
			    
			    $q = "UPDATE customers SET amazon_customer_id = '".xtc_db_input($_SESSION["amzConnectCustomerId"])."' WHERE customers_id = ".$check_customer['customers_id'];
			    xtc_db_query($q);

			    $check_country_query = xtc_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
			    $check_country = xtc_db_fetch_array($check_country_query);
                $_SESSION['customer_gender'] = $check_customer['customers_gender'];
			    $_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
			    $_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
			    $_SESSION['customer_id'] = $check_customer['customers_id'];
			    $_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
			    $_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
			    $_SESSION['customer_country_id'] = $check_country['entry_country_id'];
			    $_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];
                xtc_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
			    xtc_write_user_info((int) $_SESSION['customer_id']);
			    // restore cart contents
			    $_SESSION['cart']->restore_contents();
			    if ($_SESSION['cart']->count_contents() > 0) {
				    if(!empty($_SESSION["amazon_target"]) && $_SESSION["amazon_target"] == 'checkout' && !empty($_SESSION["amz_access_token"])){
	                    $goto = xtc_href_link('checkout_amazon.php', 'fromRedirect=1', 'SSL');
	                }else{
	                    $goto = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
	                }
	                xtc_redirect($goto);
			    } else {
				    xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
			    }
            
            }
       }     

        break;
        

}


// create smarty elements
$smarty              = new Smarty;
// include boxes
require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
// include needed functions

require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
require_once(DIR_FS_INC . 'xtc_count_shipping_modules.inc.php');
require_once(DIR_FS_INC . 'xtc_create_password.inc.php');

$smarty->assign('ERROR', $error);
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content    = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_amazon_connect_accounts.html');
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
    $smarty->load_filter('output', 'note');
require(DIR_WS_INCLUDES . 'header.php');    
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include('includes/application_bottom.php');
?>

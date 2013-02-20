<?php
/* -----------------------------------------------------------------------------------------
   $Id: login.php 59 2012-10-08 16:05:24Z deisold $

   	Self-Commerce
   	http://www.self-commerce.de

   	Copyright (c) 2012 Self-Commerce
   	-----------------------------------------------------------------------------------------
   	(c) 2012	 Self-Commerce www.self-commerce.de
   	based on:
   	(c) 2000-2001	The Exchange Project  (earlier name of osCommerce)
   	(c) 2002-2003	osCommerce	 (login.php,v 1.79 2003/05/19)	www.oscommerce.com
   	(c) 2005	 	xt-commerce;	 							www.xt-commerce.com
   	(c) 2003      	nextcommerce (login.php,v 1.13 2003/08/17)	www.nextcommerce.org
   	(c) 2012	 	Self-Commerce	 							www.self-commerce.de

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/

include 'includes/application_top.php';
require_once DIR_WS_CLASSES . 'radius.class.php';

if (isset ($_SESSION['customer_id']))
{
	xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}
// create smarty elements
$smarty = new Smarty;
// include boxes
require DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php';

// include needed functions
require_once DIR_FS_INC.'xtc_validate_password.inc.php';
require_once DIR_FS_INC.'xtc_array_to_string.inc.php';
require_once DIR_FS_INC.'xtc_write_user_info.inc.php';
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
if ($session_started == false)
{
	xtc_redirect(xtc_href_link(FILENAME_COOKIE_USAGE));
}

if (isset ($_GET['action']) && ($_GET['action'] == 'process'))
{
	$email_address	= xtc_db_prepare_input($_POST['email_address']);
	$password		= xtc_db_prepare_input($_POST['password']);
	$customers_id	= false;

	if
	(TOKEN_SECURE_ADMIN)
	{
		$Sql = "SELECT *
				FROM ".TABLE_TOKEN_USER."
				WHERE username='$email_address'
				LIMIT 1;";
		$check_admin_token = xtc_db_query($Sql);
		if (xtc_db_num_rows($check_admin_token))
		{
			$token_temp = xtc_db_fetch_array($check_admin_token);
			$class_radius = new Radius($ip_radius_server = TOKEN_SERVER, $shared_secret = TOKEN_SECRET, $radius_suffix = '', $udp_timeout = 5, $authentication_port = TOKEN_SERVER_PORT_AUTH, $accounting_port = TOKEN_SERVER_PORT_ACCOUNTING);
			$class_radius->SetNasIpAddress(TOKEN_SERVER_NAS_IP);
			if
			(TOKEN_DEBUG)
			{
				$class_radius->SetDebugMode(true);
			}
			if
			($class_radius->AccessRequest($email_address, $password))
			{
				$customers_id = (int)$token_temp['customers_id'];
			}
		}
	}
	if
	(!$customers_id)
	{
		// Check if email exists
		$query = "	SELECT customers_id, customers_vat_id, customers_firstname, customers_lastname, customers_gender, customers_password, customers_email_address, login_tries, login_time, customers_default_address_id
					FROM ".TABLE_CUSTOMERS."
					WHERE customers_email_address = '".xtc_db_input($email_address)."'
					OR customers_cid = '".xtc_db_input($email_address)."'";
	}
	else
	{
		$query = "	SELECT customers_id, customers_vat_id, customers_firstname, customers_lastname, customers_gender, customers_email_address, login_tries, login_time, customers_default_address_id
					FROM ".TABLE_CUSTOMERS."
					WHERE customers_id = '$customers_id';";
	}
	$check_customer_query = xtc_db_query($query);

	if (!xtc_db_num_rows($check_customer_query))
	{
		$_GET['login'] = 'fail';
		$info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
	}
	else
	{
		$check_customer = xtc_db_fetch_array($check_customer_query);

		// Check the login is blocked while login_tries is more than 5 and blocktime is not over
		$blocktime = LOGIN_TIME;        // time to block the login in seconds
		$time = time();           // time now as a timestamp
		$logintime = strtotime($check_customer['login_time']);  // conversion from the ISO date format to a timestamp
		$difference = $time - $logintime;      // The difference time in seconds between the last login and now

		if ($check_customer['login_tries'] >= LOGIN_NUM and $difference < $blocktime)
		{
			// Action for b�se ?
			require_once DIR_FS_INC.'xtc_random_charcode.inc.php';
			$vlcode = xtc_random_charcode(32);
			$smarty->assign('VVIMG', '<img src="'.FILENAME_DISPLAY_VVCODES.'" alt="" />');
			$smarty->assign('INPUT_CODE', xtc_draw_input_field('vvcode', '', 'size="6"', 'text', false));
			if ($_POST['vvcode'] == $_SESSION['vvcode'])
			{
				// code ok
				// Check that password is good
				if
				(!$customers_id)
				{
					if (!xtc_validate_password($password, $check_customer['customers_password']))
					{
						$_GET['login'] = 'fail';
						// Login tries + 1
						xtc_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".xtc_db_input($email_address)."'");
						$info_message = TEXT_LOGIN_ERROR;
					}
				} else
				{
					if (SESSION_RECREATE == 'True')
					{
						xtc_session_recreate();
					}
					// Login tries = 0                        $date_now = date('Ymd');
					xtc_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE customers_email_address = '".xtc_db_input($email_address)."'");

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

					if ($_SESSION['cart']->count_contents() > 0)
					{
						xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
					} else
					{
						xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
					}

				}
			}else
			{
				// code falsch
				$info_message = TEXT_WRONG_CODE;
				// Login tries + 1
				xtc_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".xtc_db_input($email_address)."'");
			}
		} else
		{
			// Check that password is good
			if
			(!$customers_id && !xtc_validate_password($password, $check_customer['customers_password']))
			{
				$_GET['login'] = 'fail';
				// Login tries + 1
				xtc_db_query("update ".TABLE_CUSTOMERS." SET login_tries = login_tries+1, login_time = now() WHERE customers_email_address = '".xtc_db_input($email_address)."'");
				$info_message = TEXT_LOGIN_ERROR;
			} else
			{
				if (SESSION_RECREATE == 'True')
				{
					xtc_session_recreate();
				}
				// Login tries = 0                        $date_now = date('Ymd');
				xtc_db_query("update ".TABLE_CUSTOMERS." SET login_tries = 0, login_time = now() WHERE customers_email_address = '".xtc_db_input($email_address)."'");

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

				if ($_SESSION['cart']->count_contents() > 0)
				{
					xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
				} else
				{
					xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
				}
			}
		}
	}
}

$breadcrumb->add(NAVBAR_TITLE_LOGIN, xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
require DIR_WS_INCLUDES.'header.php';

//if ($_GET['info_message']) $info_message = $_GET['info_message'];
$smarty->assign('info_message', $info_message);
$smarty->assign('account_option', ACCOUNT_OPTIONS);
$smarty->assign('BUTTON_NEW_ACCOUNT', '<a href="'.xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL').'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');
$smarty->assign('BUTTON_LOGIN', xtc_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN));
$smarty->assign('BUTTON_GUEST', '<a href="'.xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL').'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>');
$smarty->assign('FORM_ACTION', xtc_draw_form('login', xtc_href_link(FILENAME_LOGIN, 'action=process', 'SSL')));
$smarty->assign('INPUT_MAIL', xtc_draw_input_field('email_address'));
$smarty->assign('INPUT_PASSWORD', xtc_draw_password_field('password'));
$smarty->assign('LINK_LOST_PASSWORD', xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/login.html');
$smarty->assign('main_content', $main_content);

$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
if (!defined(RM))
{
	$smarty->loadfilter('output', 'note');
}
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include 'includes/application_bottom.php';
?>
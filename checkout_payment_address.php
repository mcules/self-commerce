<?php

/* -----------------------------------------------------------------------------------------
   $Id: checkout_payment_address.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_payment_address.php,v 1.13 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (checkout_payment_address.php,v 1.14 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC.'xtc_count_customer_address_book_entries.inc.php');
require_once (DIR_FS_INC.'xtc_address_label.inc.php');

// Paypal Express Modul Start
if (is_array($_SESSION['nvpReqArray'])) {
	$link_checkout_payment = FILENAME_PAYPAL_CHECKOUT;
} else {
	$link_checkout_payment = FILENAME_CHECKOUT_PAYMENT;
}
// Paypal Express Modul Ende

// if the customer is not logged on, redirect them to the login page
if (!isset ($_SESSION['customer_id']))
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() < 1)
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));

$error = false;
$process = false;
if (isset ($_POST['action']) && ($_POST['action'] == 'submit')) {
	// process a new billing address
	if (xtc_not_null($_POST['firstname']) && xtc_not_null($_POST['lastname']) && xtc_not_null($_POST['street_address'])) {
		$process = true;

		if (ACCOUNT_GENDER == 'true')
			$gender = xtc_db_prepare_input($_POST['gender']);
		if (ACCOUNT_COMPANY == 'true')
			$company = xtc_db_prepare_input($_POST['company']);
		$firstname = xtc_db_prepare_input($_POST['firstname']);
		$lastname = xtc_db_prepare_input($_POST['lastname']);
		$street_address = xtc_db_prepare_input($_POST['street_address']);
		if (ACCOUNT_SUBURB == 'true')
			$suburb = xtc_db_prepare_input($_POST['suburb']);
		$postcode = xtc_db_prepare_input($_POST['postcode']);
		$city = xtc_db_prepare_input($_POST['city']);
		$country = xtc_db_prepare_input($_POST['country']);
		if (ACCOUNT_STATE == 'true') {
			$zone_id = xtc_db_prepare_input($_POST['zone_id']);
			$state = xtc_db_prepare_input($_POST['state']);
		}

		if (ACCOUNT_GENDER == 'true') {
			if (($gender != 'm') && ($gender != 'f')) {
				$error = true;

				$messageStack->add('checkout_address', ENTRY_GENDER_ERROR);
			}
		}

		if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_FIRST_NAME_ERROR);
		}

		if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_LAST_NAME_ERROR);
		}

		if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_STREET_ADDRESS_ERROR);
		}

		if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_POST_CODE_ERROR);
		}

		if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_CITY_ERROR);
		}

		if (ACCOUNT_STATE == 'true') {
			$zone_id = 0;
			$check_query = xtc_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".(int) $country."'");
			$check = xtc_db_fetch_array($check_query);
			$entry_state_has_zones = ($check['total'] > 0);
			if ($entry_state_has_zones == true) {
				$zone_query = xtc_db_query("select distinct zone_id from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' and (zone_name like '".xtc_db_input($state)."%' or zone_code like '%".xtc_db_input($state)."%')");
				if (xtc_db_num_rows($zone_query) > 1) {
					$zone_query = xtc_db_query("select distinct zone_id from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' and zone_name = '".xtc_db_input($state)."'");
				}
				if (xtc_db_num_rows($zone_query) >= 1) {
					$zone = xtc_db_fetch_array($zone_query);
					$zone_id = $zone['zone_id'];
				} else {
					$error = true;

					$messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
				}
			} else {
				if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
					$error = true;

					$messageStack->add('checkout_address', ENTRY_STATE_ERROR);
				}
			}
		}

		if ((is_numeric($country) == false) || ($country < 1)) {
			$error = true;

			$messageStack->add('checkout_address', ENTRY_COUNTRY_ERROR);
		}

		if ($error == false) {
			$sql_data_array = array ('customers_id' => $_SESSION['customer_id'], 'entry_firstname' => $firstname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => $country);

			if (ACCOUNT_GENDER == 'true')
				$sql_data_array['entry_gender'] = $gender;
			if (ACCOUNT_COMPANY == 'true')
				$sql_data_array['entry_company'] = $company;
			if (ACCOUNT_SUBURB == 'true')
				$sql_data_array['entry_suburb'] = $suburb;
			if (ACCOUNT_STATE == 'true') {
				if ($zone_id > 0) {
					$sql_data_array['entry_zone_id'] = $zone_id;
					$sql_data_array['entry_state'] = '';
				} else {
					$sql_data_array['entry_zone_id'] = '0';
					$sql_data_array['entry_state'] = $state;
				}
			}

			xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

			$_SESSION['billto'] = xtc_db_insert_id();

			if (isset ($_SESSION['payment']))
				unset ($_SESSION['payment']);

			// Paypal Express Modul Änderung:
			xtc_redirect(xtc_href_link($link_checkout_payment, '', 'SSL'));
		}
		// process the selected billing destination
	}
	elseif (isset ($_POST['address'])) {
		$reset_payment = false;
		if (isset ($_SESSION['billto'])) {
			if ($billto != $_POST['address']) {
				if (isset ($_SESSION['payment'])) {
					$reset_payment = true;
				}
			}
		}

		$_SESSION['billto'] = xtc_db_prepare_input($_POST['address']);

		$check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_SESSION['customer_id']."' and address_book_id = '".$_SESSION['billto']."'");
		$check_address = xtc_db_fetch_array($check_address_query);

		if ($check_address['total'] == '1') {
			if ($reset_payment == true)
				unset ($_SESSION['payment']);
			// Paypal Express Modul Änderung:
			xtc_redirect(xtc_href_link($link_checkout_payment, '', 'SSL'));
		} else {
			unset ($_SESSION['billto']);
		}
		// no addresses to select from - customer decided to keep the current assigned address
	} else {
		$_SESSION['billto'] = $_SESSION['customer_default_address_id'];

		// Paypal Express Modul Änderung:
		xtc_redirect(xtc_href_link($link_checkout_payment, '', 'SSL'));
	}
}

// if no billing destination address was selected, use their own address as default
if (!isset ($_SESSION['billto'])) {
	$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
}

// Paypal Express Modul Änderung:
$breadcrumb->add(NAVBAR_TITLE_1_PAYMENT_ADDRESS, xtc_href_link($link_checkout_payment, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2_PAYMENT_ADDRESS, xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

$addresses_count = xtc_count_customer_address_book_entries();
require (DIR_WS_INCLUDES.'header.php');

$smarty->assign('FORM_ACTION', xtc_draw_form('checkout_address', xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 'post', 'onsubmit="return check_form_optional(checkout_address);"'));

if ($messageStack->size('checkout_address') > 0) {
	$smarty->assign('error', $messageStack->output('checkout_address'));

}

if ($process == false) {
	$smarty->assign('ADDRESS_LABEL', xtc_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />'));

	if ($addresses_count > 1) {

		$address_content = '<table width="100%" cellspacing="0" cellpadding="5" border="0">';
		$radio_buttons = 0;

		$addresses_query = xtc_db_query("select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".$_SESSION['customer_id']."'");
		while ($addresses = xtc_db_fetch_array($addresses_query)) {
			$format_id = xtc_get_address_format_id($address['country_id']);
			$address_content .= '';
			if ($addresses['address_book_id'] == $_SESSION['billto']) {
				$address_content .= '<tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, '.$radio_buttons.')">'."\n";
			} else {
				$address_content .= '<tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, '.$radio_buttons.')">'."\n";
			}
			$address_content .= '<td width="10%">'.xtc_draw_radio_field('address', $addresses['address_book_id'], ($addresses['address_book_id'] == $_SESSION['billto'])).'</td>
			                     <td><strong>'.$addresses['firstname'].' '.$addresses['lastname'].'</strong></td>
			                  </tr>
			                  <tr>
			                    <td colspan="2">'.xtc_address_format($format_id, $addresses, true, ' ', ', ').'</td>
			                  </tr>
			                  <tr>
                          <td colspan="2" height="5">&nbsp;</td>
                        </tr>';

			$radio_buttons ++;
		}
		$address_content .= '</table>';
		$smarty->assign('BLOCK_ADDRESS', $address_content);

	}
}

if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {

	require (DIR_WS_MODULES.'checkout_new_address.php');
}
$smarty->assign('BUTTON_CONTINUE', xtc_draw_hidden_field('action', 'submit').xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE));

if ($process == true) {
	$smarty->assign('BUTTON_BACK', '<a href="'.xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL').'">'.xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK).'</a>');

}
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);

$smarty->caching = 0;
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_payment_address.html');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM))
	$smarty->loadfilter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
?>

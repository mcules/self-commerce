<?php
/* -----------------------------------------------------------------------------------------
   $Id: create_guest_account.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   (c) 2012	 Self-Commerce www.self-commerce.de
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account.php,v 1.63 2003/05/28); www.oscommerce.com
   (c) 2003  nextcommerce (create_account.php,v 1.27 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   
   Guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

if (ACCOUNT_OPTIONS == 'account') { xtc_redirect(FILENAME_DEFAULT); }
if (isset ($_SESSION['customer_id'])) { xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL')); }

// create smarty elements
$smarty = new Smarty;
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');
// include needed functions
require_once (DIR_FS_INC.'xtc_draw_radio_field.inc.php');
require_once (DIR_FS_INC.'xtc_get_country_list.inc.php');
require_once (DIR_FS_INC.'xtc_get_countries.inc.php');
require_once (DIR_FS_INC.'xtc_draw_checkbox_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_password_field.inc.php');
require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC.'xtc_create_password.inc.php');
require_once (DIR_FS_INC.'xtc_draw_hidden_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_pull_down_menu.inc.php');
require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');

// needs to be included earlier to set the success message in the messageStack
//  require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . FILENAME_CREATE_ACCOUNT);

$process = false;
if (isset ($_POST['action']) && ($_POST['action'] == 'process')) {
	$process = true;

	if (ACCOUNT_GENDER == 'true')
		$gender = xtc_db_prepare_input($_POST['gender']);
	$firstname = xtc_db_prepare_input($_POST['firstname']);
	$lastname = xtc_db_prepare_input($_POST['lastname']);
	if (ACCOUNT_DOB == 'true')
		$dob = xtc_db_prepare_input($_POST['dob']);
	$email_address = xtc_db_prepare_input($_POST['email_address']);
	if (ACCOUNT_COMPANY == 'true')
		$company = xtc_db_prepare_input($_POST['company']);
	if (ACCOUNT_COMPANY_VAT_CHECK == 'true')
		$vat = xtc_db_prepare_input($_POST['vat']);
	$street_address = xtc_db_prepare_input($_POST['street_address']);
	if (ACCOUNT_SUBURB == 'true')
		$suburb = xtc_db_prepare_input($_POST['suburb']);
	$postcode = xtc_db_prepare_input($_POST['postcode']);
	$city = xtc_db_prepare_input($_POST['city']);
	$zone_id = xtc_db_prepare_input($_POST['zone_id']);
	if (ACCOUNT_STATE == 'true')
		$state = xtc_db_prepare_input($_POST['state']);
	$country = xtc_db_prepare_input($_POST['country']);
	$telephone = xtc_db_prepare_input($_POST['telephone']);
	$fax = xtc_db_prepare_input($_POST['fax']);
	//    $newsletter = xtc_db_prepare_input($_POST['newsletter']);
	$newsletter = '0';
	$password = xtc_db_prepare_input($_POST['password']);
	$confirmation = xtc_db_prepare_input($_POST['confirmation']);
  $datensg = xtc_db_prepare_input($_POST['datensg']);
	$error = false;

	if (ACCOUNT_GENDER == 'true') {
		if (($gender != 'm') && ($gender != 'f')) {
			$error = true;

			$messageStack->add('create_account', ENTRY_GENDER_ERROR);
		}
	}

	if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
	}

	if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
	}

	if (ACCOUNT_DOB == 'true') {
		if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
			$error = true;

			$messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
		}
	}

// New VAT Check
	require_once(DIR_WS_CLASSES.'vat_validation.php');
	$vatID = new vat_validation($vat, '', '', $country);
	
	$customers_status = $vatID->vat_info['status'];
	$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
	$error_vat = $vatID->vat_info['error'];
	
	if($error_vat==1){
	$messageStack->add('create_account', ENTRY_VAT_ERROR);
	$error = true;
  }

// New VAT CHECK END
	if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
	}
	if (xtc_validate_email($email_address) == false) {
		$error = true;

		$messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
	}else {
        $check_email_query = xtc_db_query("select count(*) as total from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($email_address)."' and account_type = '0'");
        $check_email = xtc_db_fetch_array($check_email_query);
        if ($check_email['total'] > 0) {
            $error = true;

            $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
        }
    }

	if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
	}

	if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
	}

	if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_CITY_ERROR);
	}

	if (is_numeric($country) == false) {
		$error = true;

		$messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
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

				$messageStack->add('create_account', ENTRY_STATE_ERROR);
			}
		}
	}

	if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;

		$messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
	}
	
	  if(!isset($datensg) || empty($datensg)) {
    $error = true;
    
    $messageStack->add('create_account', ERROR_DATENSG_NOT_ACCEPTED);
  }

	if ($customers_status == 0 || !$customers_status)
		$customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
	$password = xtc_create_password(8);

	if (!$newsletter)
		$newsletter = 0;
	if ($error == false) {
		$sql_data_array = array ('customers_vat_id' => $vat, 'customers_vat_id_status' => $customers_vat_id_status, 'customers_status' => $customers_status, 'customers_firstname' => $firstname, 'customers_lastname' => $lastname, 'customers_email_address' => $email_address, 'customers_telephone' => $telephone, 'customers_fax' => $fax, 'customers_newsletter' => $newsletter, 'account_type' => '1', 'customers_password' => xtc_encrypt_password($password), 'datensg' => 'now()');

		$_SESSION['account_type'] = '1';

		if (ACCOUNT_GENDER == 'true')
			$sql_data_array['customers_gender'] = $gender;
		if (ACCOUNT_DOB == 'true')
			$sql_data_array['customers_dob'] = xtc_date_raw($dob);

// Modifikation Automatisch Kundennummer tag monat jahr- nr: 
function new_customer_id($space='-'){ 
    $new_cid=''; 
    $day = date("d"); 
    $mon = date("m"); 
    $year = date("y"); 
    
    $cid_query = xtc_db_query("SELECT customers_id FROM ".TABLE_CUSTOMERS." ORDER BY customers_id DESC LIMIT 1"); 
    $last_cid = xtc_db_fetch_array($cid_query); 
    $new_cid = $day . $mon . $year . $space . ($last_cid['customers_id']+1000); 

return $new_cid; 
} 
    $sql_data_array['customers_cid'] = new_customer_id(); 
// Modifikation Kundennummer tag monat jahr- nr Ende 
 
		xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

		$_SESSION['customer_id'] = xtc_db_insert_id();

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

		$address_id = xtc_db_insert_id();

		xtc_db_query("update ".TABLE_CUSTOMERS." set customers_default_address_id = '".$address_id."' where customers_id = '".(int) $_SESSION['customer_id']."'");

		xtc_db_query("insert into ".TABLE_CUSTOMERS_INFO." (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('".(int) $_SESSION['customer_id']."', '0', now())");

		if (SESSION_RECREATE == 'True') {
			xtc_session_recreate();
		}

		$_SESSION['customer_first_name'] = $firstname;
		$_SESSION['customer_last_name'] = $lastname;
		$_SESSION['customer_default_address_id'] = $address_id;
		$_SESSION['customer_country_id'] = $country;
		$_SESSION['customer_zone_id'] = $zone_id;
		$_SESSION['customer_vat_id'] = $vat;

		// restore cart contents
		$_SESSION['cart']->restore_contents();

if (isset ($_SESSION[tracking]['refID'])){
      $campaign_check_query_raw = "SELECT *
			                            FROM ".TABLE_CAMPAIGNS." 
			                            WHERE campaigns_refID = '".$_SESSION[tracking][refID]."'";
			$campaign_check_query = xtc_db_query($campaign_check_query_raw);
		if (xtc_db_num_rows($campaign_check_query) > 0) {
			$campaign = xtc_db_fetch_array($campaign_check_query);
			$refID = $campaign['campaigns_id'];
			} else {
			$refID = 0;
		            }
			
			 xtc_db_query("update " . TABLE_CUSTOMERS . " set
                                 refferers_id = '".$refID."'
                                 where customers_id = '".(int) $_SESSION['customer_id']."'");
			
			$leads = $campaign['campaigns_leads'] + 1 ;
		     xtc_db_query("update " . TABLE_CAMPAIGNS . " set
		                         campaigns_leads = '".$leads."'
                                 where campaigns_id = '".$refID."'");		
		}
		#---- AJAX CHECKOUT PROCESS START
		if (CHECKOUT_AJAX_STAT == 'true' && $_POST['js_enabled'] == 1) {
			xtc_redirect(xtc_href_link('checkout.php', '', 'SSL'));
		} else {
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
		}
		#---- AJAX CHECKOUT PROCESS END
	}
}

$breadcrumb->add(NAVBAR_TITLE_CREATE_GUEST_ACCOUNT, xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));

require (DIR_WS_INCLUDES.'header.php');

if ($messageStack->size('create_account') > 0) {
	$smarty->assign('error', $messageStack->output('create_account'));

}
$smarty->assign('FORM_ACTION', xtc_draw_form('create_account', xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'), 'post', 'onsubmit="return check_form(create_account);"').xtc_draw_hidden_field('action', 'process'));

if (ACCOUNT_GENDER == 'true') {
	$smarty->assign('gender', '1');

	$smarty->assign('INPUT_MALE', xtc_draw_radio_field(array ('name' => 'gender', 'suffix' => MALE), 'm'));
	$smarty->assign('INPUT_FEMALE', xtc_draw_radio_field(array ('name' => 'gender', 'suffix' => FEMALE, 'text' => (xtc_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">'.ENTRY_GENDER_TEXT.'</span>' : '')), 'f'));

} else {
	$smarty->assign('gender', '0');
}

$smarty->assign('INPUT_FIRSTNAME', xtc_draw_input_fieldNote(array ('name' => 'firstname', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">'.ENTRY_FIRST_NAME_TEXT.'</span>' : ''))));
$smarty->assign('INPUT_LASTNAME', xtc_draw_input_fieldNote(array ('name' => 'lastname', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">'.ENTRY_LAST_NAME_TEXT.'</span>' : ''))));

if (ACCOUNT_DOB == 'true') {
	$smarty->assign('birthdate', '1');

	$smarty->assign('INPUT_DOB', xtc_draw_input_fieldNote(array ('name' => 'dob', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">'.ENTRY_DATE_OF_BIRTH_TEXT.'</span>' : ''))));

} else {
	$smarty->assign('birthdate', '0');
}

$smarty->assign('INPUT_EMAIL', xtc_draw_input_fieldNote(array ('name' => 'email_address', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">'.ENTRY_EMAIL_ADDRESS_TEXT.'</span>' : ''))));

if (ACCOUNT_COMPANY == 'true') {
	$smarty->assign('company', '1');
	$smarty->assign('INPUT_COMPANY', xtc_draw_input_fieldNote(array ('name' => 'company', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">'.ENTRY_COMPANY_TEXT.'</span>' : ''))));
} else {
	$smarty->assign('company', '0');
}

if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
	$smarty->assign('vat', '1');
	$smarty->assign('INPUT_VAT', xtc_draw_input_fieldNote(array ('name' => 'vat', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_VAT_TEXT) ? '<span class="inputRequirement">'.ENTRY_VAT_TEXT.'</span>' : ''))));
} else {
	$smarty->assign('vat', '0');
}

$smarty->assign('INPUT_STREET', xtc_draw_input_fieldNote(array ('name' => 'street_address', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">'.ENTRY_STREET_ADDRESS_TEXT.'</span>' : ''))));

if (ACCOUNT_SUBURB == 'true') {
	$smarty->assign('suburb', '1');
	$smarty->assign('INPUT_SUBURB', xtc_draw_input_fieldNote(array ('name' => 'suburb', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">'.ENTRY_SUBURB_TEXT.'</span>' : ''))));

} else {
	$smarty->assign('suburb', '0');
}

$smarty->assign('INPUT_CODE', xtc_draw_input_fieldNote(array ('name' => 'postcode', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">'.ENTRY_POST_CODE_TEXT.'</span>' : ''))));
$smarty->assign('INPUT_CITY', xtc_draw_input_fieldNote(array ('name' => 'city', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">'.ENTRY_CITY_TEXT.'</span>' : ''))));

if (ACCOUNT_STATE == 'true') {
	$smarty->assign('state', '1');

	if ($process == true) {
		if ($entry_state_has_zones == true) {
			$zones_array = array ();
			$zones_query = xtc_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".(int) $country."' order by zone_name");
			while ($zones_values = xtc_db_fetch_array($zones_query)) {
				$zones_array[] = array ('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
			}
			$state_input = xtc_draw_pull_down_menuNote(array ('name' => 'state', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">'.ENTRY_STATE_TEXT.'</span>' : '')), $zones_array);
		} else {
			$state_input = xtc_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">'.ENTRY_STATE_TEXT.'</span>' : '')));
		}
	} else {
		$state_input = xtc_draw_input_fieldNote(array ('name' => 'state', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_STATE_TEXT) ? '<span class="inputRequirement">'.ENTRY_STATE_TEXT.'</span>' : '')));
	}

	$smarty->assign('INPUT_STATE', $state_input);
} else {
	$smarty->assign('state', '0');
}

if ($_POST['country']) {
	$selected = $_POST['country'];
} else {
	$selected = STORE_COUNTRY;
}

$smarty->assign('SELECT_COUNTRY', xtc_get_country_list(array ('name' => 'country', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">'.ENTRY_COUNTRY_TEXT.'</span>' : '')), $selected));
$smarty->assign('INPUT_TEL', xtc_draw_input_fieldNote(array ('name' => 'telephone', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="inputRequirement">'.ENTRY_TELEPHONE_NUMBER_TEXT.'</span>' : ''))));
$smarty->assign('INPUT_FAX', xtc_draw_input_fieldNote(array ('name' => 'fax', 'text' => '&nbsp;'. (xtc_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="inputRequirement">'.ENTRY_FAX_NUMBER_TEXT.'</span>' : ''))));
$shop_content_query = xtc_db_query("SELECT content_title,
                                          content_heading,
                                          content_text,
                                          content_file
                                          FROM ".TABLE_CONTENT_MANAGER."
                                          WHERE content_group='2'
                                          AND languages_id='".$_SESSION['languages_id']."'");
$shop_content_data = xtc_db_fetch_array($shop_content_query);
  if ($shop_content_data['content_file'] != '') {
    $datensg = '<iframe SRC="'.DIR_WS_CATALOG.'media/content/'.$shop_content_data['content_file'].'" width="100%" height="300">';
    $datensg .= '</iframe>';
  } else {
    $datensg = '<textarea name="blubblub" cols="60" rows="10" readonly="readonly">'.strip_tags(str_replace('<br />', "\n", $shop_content_data['content_text'])).'</textarea>';
  }
$smarty->assign('DSG', $datensg);
$smarty->assign('DATENSG_CHECKBOX', '<input type="checkbox" value="datensg" name="datensg" /> *');

//$smarty->assign('CHECKBOX_NEWSLETTER',xtc_draw_checkbox_field('newsletter', '1') . '&nbsp;' . (xtc_not_null(ENTRY_NEWSLETTER_TEXT) ? '<span class="inputRequirement">' . ENTRY_NEWSLETTER_TEXT . '</span>': ''));
$smarty->assign('FORM_END', '</form>');
$smarty->assign('language', $_SESSION['language']);
$smarty->caching = 0;
#---- AJAX CHECKOUT PROCESS START
$post_js_stat = '<script type="text/javascript">';
$post_js_stat .= 'document.write(\''.xtc_draw_hidden_field('js_enabled', 1).'\');'; 
$post_js_stat .= '</script>';
$smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE).$post_js_stat);
#---- AJAX CHECKOUT PROCESS END
$main_content = $smarty->fetch(CURRENT_TEMPLATE.'/module/create_account_guest.html');

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->caching = 0;
if (!defined(RM)) { $smarty->loadfilter('output', 'note'); }
$smarty->display(CURRENT_TEMPLATE.'/index.html');
include ('includes/application_bottom.php');
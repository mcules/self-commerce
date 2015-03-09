<?php
header('Content-Type: application/json; charset=utf-8');

include('ajax_checkout_encoding.inc.php');
if (!function_exists('apache_response_headers')) {
  include('apache_response_headers.inc.php');
}

$action = strtolower(trim(auto_decode($_REQUEST['ajax_action'])));
$json_response = array();
$allowed_for_logged_out = array('login', 'register', 'modal_message', 'get_states');
if (empty($_SESSION['customer_id']) && !in_array($action, $allowed_for_logged_out)) {
  die(json_encode(array('redirect' => xtc_href_link(FILENAME_LOGIN, '', 'SSL'))));
}

function prevent_redirect() {
  $response_headers = apache_response_headers();
  $location = $response_headers['Location'] ? $response_headers['Location'] : '';
  
  if (empty($location)) {
    return;
  }
  
  $url_parts = parse_url($location);
  $query = $url_parts['query'] ? $url_parts['query'] : '';
  parse_str($query, $query_params);
  unset($_SESSION['checkout_payment_form_data']);
  unset($_SESSION['payment']);
  if ($query && !empty($query_params['payment_error'])) {
    header('Location:');
    header('X-Ajax-Checkout: '.json_encode($query_params));
    header("HTTP/1.0 200 OK");
  } else {
    header('X-Ajax-Checkout: '.json_encode(array('redirect' => $location)));
  }
}
register_shutdown_function('prevent_redirect');




if ($action == 'login') {
  
  
  require_once (DIR_FS_INC.'xtc_validate_password.inc.php');
  require_once (DIR_FS_INC.'xtc_array_to_string.inc.php');
  require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');

  $email_address = xtc_db_prepare_input($_POST['email_address']);
  $password = xtc_db_prepare_input($_POST['password']);
  $valid = false;
  
  $check_customer_query = xtc_db_query("select customers_id, customers_vat_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, customers_default_address_id from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($email_address)."' and account_type = '0'");
  
  if (!xtc_db_num_rows($check_customer_query)) {
    $info_message = TEXT_NO_EMAIL_ADDRESS_FOUND;
  } else {
    $check_customer = xtc_db_fetch_array($check_customer_query);
    // Check that password is valid
    if (!xtc_validate_password($password, $check_customer['customers_password'])) {
      $valid = false;
      $info_message = TEXT_LOGIN_ERROR;
    } else {
      if (SESSION_RECREATE == 'True') {
        xtc_session_recreate();
      }
      $valid = true;

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
      
      unset($_SESSION['checkout_payment_form_data']);
      unset($_SESSION['payment']);
      unset($_SESSION['shipping']);

      xtc_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
      xtc_write_user_info((int) $_SESSION['customer_id']);
      
      // restore cart contents
      $_SESSION['cart']->restore_contents();
      
      if (is_object($econda)) $econda->_loginUser();
    }
  }
  
  $json_response = array('error' => $valid ? null : auto_encode($info_message));
  
  
} else if ($action == "register") {
  
  
  require_once (DIR_FS_INC.'xtc_create_password.inc.php');
  require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
  require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
  require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');
  
  $account_type = ($_POST['account_type'] == 'guest' || $_POST['account_type'] == 'account') ? $_POST['account_type'] : 'guest';
  
  if (ACCOUNT_GENDER == 'true') {
    $gender = xtc_db_prepare_input($_POST['gender']);
  }
  $firstname = xtc_db_prepare_input($_POST['firstname']);
  $lastname = xtc_db_prepare_input($_POST['lastname']);
  if (ACCOUNT_DOB == 'true') {
    $dob = xtc_db_prepare_input($_POST['dob']);
  }
  $email_address = xtc_db_prepare_input($_POST['email_address']);
  if (ACCOUNT_COMPANY == 'true') {
    $company = xtc_db_prepare_input($_POST['company']);
  }
  if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {
    $vat = xtc_db_prepare_input($_POST['vat']);
  }
  $street_address = xtc_db_prepare_input($_POST['street_address']);
  if (ACCOUNT_SUBURB == 'true') {
    $suburb = xtc_db_prepare_input($_POST['suburb']);
  }
  $postcode = xtc_db_prepare_input($_POST['postcode']);
  $city = xtc_db_prepare_input($_POST['city']);
  if (ACCOUNT_STATE == 'true') {
    $state = xtc_db_prepare_input($_POST['state']);
  }
  $country = xtc_db_prepare_input($_POST['country']);
  $telephone = xtc_db_prepare_input($_POST['telephone']);
  $fax = xtc_db_prepare_input($_POST['fax']);
  if ($account_type) {
    $password = xtc_db_prepare_input($_POST['password']);
    $confirmation = xtc_db_prepare_input($_POST['confirmation']);
  }
  
  $error = false;
  $info_message = array();
  
  if (ACCOUNT_GENDER == 'true') {
    if (($gender != 'm') && ($gender != 'f')) {
      $error = true;
      $info_message[] = ENTRY_GENDER_ERROR;
    }
  }
  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $info_message[] = ENTRY_FIRST_NAME_ERROR;
  }
  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $info_message[] = ENTRY_LAST_NAME_ERROR;
  }
  if (ACCOUNT_DOB == 'true') {
    if (checkdate(substr(xtc_date_raw($dob), 4, 2), substr(xtc_date_raw($dob), 6, 2), substr(xtc_date_raw($dob), 0, 4)) == false) {
      $error = true;
      $info_message[] =  ENTRY_DATE_OF_BIRTH_ERROR;
    }
  }
  
  // New VAT Check
  require_once(DIR_WS_CLASSES.'vat_validation.php');
  $vatID = new vat_validation($vat, '', '', $country, ($account_type == 'guest' ? true : false));
  
  $customers_status = $vatID->vat_info['status'];
  $customers_vat_id_status = $vatID->vat_info['vat_id_status'];
  $error = $vatID->vat_info['error'];
  
  if ($error == 1){
    $info_message[] = ENTRY_VAT_ERROR;
    $error = true;
  }
  
  if ($account_type == 'guest') {
    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;
      $info_message[] = ENTRY_EMAIL_ADDRESS_ERROR;
    } elseif (xtc_validate_email($email_address) == false) {
      $error = true;
      $info_message[] = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
    }
  } else {
    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;
      $info_message[] =  ENTRY_EMAIL_ADDRESS_ERROR;
    } elseif (xtc_validate_email($email_address) == false) {
      $error = true;
      $info_message[] = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
    } else {
      $check_email_query = xtc_db_query("select count(*) as total from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($email_address)."' and account_type = '0'");
      $check_email = xtc_db_fetch_array($check_email_query);
      if ($check_email['total'] > 0) {
        $error = true;
        $info_message[] =  ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
      }
    }
  }
  if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    $error = true;
    $info_message[] = ENTRY_STREET_ADDRESS_ERROR;
  }
  if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    $error = true;
    $info_message[] = ENTRY_POST_CODE_ERROR;
  }
  if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
    $error = true;
    $info_message[] = ENTRY_CITY_ERROR;
  }
  if (is_numeric($country) == false) {
    $error = true;
    $info_message[] = ENTRY_COUNTRY_ERROR;
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
          $info_message[] = ENTRY_STATE_ERROR_SELECT;
      }
    }
  }
  if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
    $error = true;
    $info_message[] = ENTRY_TELEPHONE_NUMBER_ERROR;
  }
  if ($account_type != 'guest') {
    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;
      $info_message[] = ENTRY_PASSWORD_ERROR;
    } elseif ($password != $confirmation) {
      $error = true;
      $info_message[] = ENTRY_PASSWORD_ERROR_NOT_MATCHING;
    }
  }
  
  //--- XTC:Modified start
  if (PROJECT_VERSION == 'xtcModified') {
    $confirm_email_address = isset($_POST['confirm_email_address']) ? xtc_db_prepare_input($_POST['confirm_email_address']) : 0;
    $privacy = xtc_db_prepare_input($_POST['privacy']);
    if ($privacy != 'privacy') {
      $error = true;
      $info_message[] = ENTRY_PRIVACY_ERROR;
    }
    if ($email_address != $confirm_email_address) {
      $error = true;    
      $info_message[] = ENTRY_EMAIL_ERROR_NOT_MATCHING;
    }
  }
  // XTC:Modified end ---//
  
  //don't know why, but this happens sometimes and new user becomes admin
  if ($customers_status == 0 || !$customers_status) {
    if ($account_type == 'guest') {
      $customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
    } else {
      $customers_status = DEFAULT_CUSTOMERS_STATUS_ID;
    }
  }
  if ($account_type == 'guest')
    $password = xtc_create_password(8);
  if (!$newsletter)
    $newsletter = 0;
  if ($error == false && count($info_message) < 1) {
    $sql_data_array = array ('customers_vat_id' => $vat, 'customers_vat_id_status' => $customers_vat_id_status, 'customers_status' => $customers_status, 'customers_firstname' => $firstname, 'customers_lastname' => $lastname, 'customers_email_address' => $email_address, 'customers_telephone' => $telephone, 'customers_fax' => $fax, 'customers_newsletter' => $newsletter, 'customers_password' => xtc_encrypt_password($password));
    if ($account_type == 'guest') {
      $sql_data_array['account_type'] = 1;
      $_SESSION['account_type'] = '1';
    } else {
      $sql_data_array['customers_date_added'] = 'now()';
      $sql_data_array['customers_last_modified'] = 'now()';
    }

    if (ACCOUNT_GENDER == 'true')
      $sql_data_array['customers_gender'] = $gender;
    if (ACCOUNT_DOB == 'true')
      $sql_data_array['customers_dob'] = xtc_date_raw($dob);
    xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);
    $_SESSION['customer_id'] = xtc_db_insert_id();
    $user_id = xtc_db_insert_id();
    xtc_write_user_info($user_id);
    $sql_data_array = array ('customers_id' => $_SESSION['customer_id'], 'entry_firstname' => $firstname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => $country);
    if ($account_type != 'guest') {
        $sql_data_array['address_date_added'] = 'now()';
        $sql_data_array['address_last_modified'] = 'now()';
    }
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
    if ($account_type != 'guest') {
      // build the message content
      $name = $firstname.' '.$lastname;
  
        // load data into array
      $module_content = array ();
      $module_content = array ('MAIL_NAME' => $name, 'MAIL_REPLY_ADDRESS' => EMAIL_SUPPORT_REPLY_ADDRESS, 'MAIL_GENDER' => $gender);
  
      // assign data to smarty
      $smarty = new Smarty;
      $smarty->assign('language', $_SESSION['language']);
      $smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
      $smarty->assign('content', $module_content);
      $smarty->caching = false;
    }
    
    if (isset ($_SESSION['tracking']['refID'])) {
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
    if (ACTIVATE_GIFT_SYSTEM == 'true' && $account_type != 'guest') {
      // GV Code Start
      // ICW - CREDIT CLASS CODE BLOCK ADDED  ******************************************************* BEGIN
      if (NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0) {
        $coupon_code = create_coupon_code();
        $insert_query = xtc_db_query("insert into ".TABLE_COUPONS." (coupon_code, coupon_type, coupon_amount, date_created) values ('".$coupon_code."', 'G', '".NEW_SIGNUP_GIFT_VOUCHER_AMOUNT."', now())");
        $insert_id = xtc_db_insert_id($insert_query);
        $insert_query = xtc_db_query("insert into ".TABLE_COUPON_EMAIL_TRACK." (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('".$insert_id."', '0', 'Admin', '".$email_address."', now() )");

        $smarty->assign('SEND_GIFT', 'true');
        $smarty->assign('GIFT_AMMOUNT', $xtPrice->xtcFormat(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT, true));
        $smarty->assign('GIFT_CODE', $coupon_code);
        $smarty->assign('GIFT_LINK', xtc_href_link(FILENAME_GV_REDEEM, 'gv_no='.$coupon_code, 'NONSSL', false));
      }
      if (NEW_SIGNUP_DISCOUNT_COUPON != '') {
        $coupon_code = NEW_SIGNUP_DISCOUNT_COUPON;
        $coupon_query = xtc_db_query("select * from ".TABLE_COUPONS." where coupon_code = '".$coupon_code."'");
        $coupon = xtc_db_fetch_array($coupon_query);
        $coupon_id = $coupon['coupon_id'];
        $coupon_desc_query = xtc_db_query("select * from ".TABLE_COUPONS_DESCRIPTION." where coupon_id = '".$coupon_id."' and language_id = '".(int) $_SESSION['languages_id']."'");
        $coupon_desc = xtc_db_fetch_array($coupon_desc_query);
        $insert_query = xtc_db_query("insert into ".TABLE_COUPON_EMAIL_TRACK." (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('".$coupon_id."', '0', 'Admin', '".$email_address."', now() )");

        $smarty->assign('SEND_COUPON', 'true');
        $smarty->assign('COUPON_DESC', $coupon_desc['coupon_description']);
        $smarty->assign('COUPON_CODE', $coupon['coupon_code']);
      }
      // ICW - CREDIT CLASS CODE BLOCK ADDED  ******************************************************* END
      // GV Code End       // create templates
    }        
    if ($account_type != 'guest') {
      $smarty->caching = 0;
      $html_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/create_account_mail.html');
      $smarty->caching = 0;
      $txt_mail = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$_SESSION['language'].'/create_account_mail.txt');

      xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $email_address, $name, EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);

      if (isset($mail_error)) {
        $error = true;
        $info_message[] = $mail_error;
      }
    }
  }
  if ($error || count($info_message) > 0) {
    $info_message = implode("<br />", $info_message);
    $json_response = array('error' => auto_encode($info_message));
  } else {
    $json_response = array('error' => null);
  }
  
  
} else if ($action == "after_login") {
  
  
  $order = new order;
  if ($order->delivery['country']['iso_code_2'] != '') {
    $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
  }
  $products_block = $checkout->getProducts();
  $shipping_block = $checkout->getShippingBlock();
  $virtual = $checkout->isVirtual();
  $order_total_modules = new order_total;
  $order_total_modules->process();
  $payment_modules = new payment();
  $payment_modules->update_status();
  
  $payment_compatible = true;
  
  $json_response = array_merge($json_response, array(
    'payment_options'   => auto_encode($checkout->getPaymentBlock()),
    'shipping_options'  => auto_encode($shipping_block),
    'is_virtual'        => $virtual,
    'is_free_shipping'  => $checkout->isFreeShipping()
  ));
  
  if ($order->info['total'] <= 0) {
    if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
      $_SESSION['old_payment'] = $_SESSION['payment'];
      $_SESSION['payment'] = 'no_payment';
    }
    $order = new order;
    $order_total_modules = new order_total;
    $order_total_modules->process();
    
    $json_response['gvcover'] = true;
  } else {
    if (!empty($_SESSION['old_payment'])) {
      $_SESSION['payment'] = $_SESSION['old_payment'];
      unset($_SESSION['old_payment']);
      $order = new order;
      $order_total_modules = new order_total;
      $order_total_modules->process();
    }
    $json_response['gvcover'] = false;
  }
  
  if (ACTIVATE_GIFT_SYSTEM == 'true') {
    $gvcover_html = $order_total_modules->credit_selection();
  }
  
  $json_response = array_merge($json_response, array(
    'form_url'                  => auto_encode($checkout->getFormUrl()),
    'hidden_inputs'             => auto_encode($checkout->getProcessButton()),
    'products'                  => auto_encode($products_block),
    'products_count'            => $_SESSION['cart']->count_contents(),
    'shipping_address'          => auto_encode(xtc_address_label($_SESSION['customer_id'], $_SESSION['sendto'], true, ' ', '<br />')),
    'payment_address'           => auto_encode(xtc_address_label($_SESSION['customer_id'], $_SESSION['billto'], true, ' ', '<br />')),
    'order_total'               => auto_encode($checkout->getTotalBlock()),
    'gvcover_html'              => $gvcover_html ? auto_encode($gvcover_html) : ''
  ));
  
  
} else if ($action == 'update_payment') {
  
  
  $shipping_modules = new shipping;
  
  $_SESSION['payment'] = xtc_db_prepare_input($_POST['payment']);
  if (isset($_SESSION['credit_covers'])) {
    $_SESSION['payment'] = 'no_payment';
  }
  $order = new order();
  $order_total_modules = new order_total();
  $order_total_modules->process();
  $payment_modules = new payment($_SESSION['payment']);
  $payment_modules->update_status();
  $payment_modules->pre_confirmation_check();
  if ($_SESSION['payment'] == 'cc') {
    ob_start();
    @$payment_modules->confirmation();
    ob_end_clean();
  }
  if ($order->info['total'] <= 0) {
    if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
        $_SESSION['old_payment'] = $_SESSION['payment'];
        $_SESSION['payment'] = 'no_payment';
    }
    
    $order = new order;
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $json_response['gvcover'] = true;
  }
  
  $_SESSION['checkout_payment_form_data'] = $_POST;
  $json_response = array_merge($json_response, array(
    'form_url'      => auto_encode($checkout->getFormUrl()),
    'hidden_inputs' => auto_encode($payment_modules->process_button()),
    'order_total'   => auto_encode($checkout->getTotalBlock())
  ));
  
  
} else if ($action == 'update_shipping') {
  
  
  $order = new order;
  $order_total_modules = new order_total;
  $order_total_modules->process();

  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();
  $shipping_modules = new shipping;
  $_SESSION['shipping'] = xtc_db_prepare_input($_POST['shipping']);
  list($module, $method) = explode('_', $_SESSION['shipping']);
  global $$module;
  $is_free_shipping = $checkout->isFreeShipping();
  
  if (is_object($$module) || ($_SESSION['shipping'] == 'free_free' || $is_free_shipping)) {
    if ($_SESSION['shipping'] == 'free_free' || $is_free_shipping) {
      $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
      $quote[0]['methods'][0]['cost'] = '0';
    } else {
      $quote = $shipping_modules->quote($method, $module);
    }
    if (isset($quote['error'])) {
      unset($_SESSION['shipping']);
    } else {
      if ((isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost']))) {
        $_SESSION['shipping'] = array ('id' => $_SESSION['shipping'], 'title' => ($is_free_shipping ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);
      }
    }
  } else {
    unset($_SESSION['shipping']);
  }
  
  $order = new order;
  $order_total_modules = new order_total;
  $order_total_modules->process();
  $payment_modules = new payment();
  $payment_modules->update_status();
  
  global $payment_compatible;
  $payment_compatible = true;
  
  if (!$payment_compatible) {
    $json_response['_payment_error'] = auto_encode(CHECKOUT_PAYMENT_NOT_COMPATIBLE);
  }
  
  if ($order->info['total'] <= 0) {
    if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
      $_SESSION['old_payment'] = $_SESSION['payment'];
      $_SESSION['payment'] = 'no_payment';
    }
    $order = new order;
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $json_response['gvcover'] = true;
  }
  
  $json_response = array_merge($json_response, array(
    'form_url'        => auto_encode($checkout->getFormUrl()),
    'hidden_inputs'   => auto_encode($checkout->getProcessButton()),
    'order_total'     => auto_encode($checkout->getTotalBlock()),
    'payment_options' => auto_encode($checkout->getPaymentBlock())
  ));
  
  
} else if ($action == 'use_gv') {
  
  
  $use_gv = $_POST['cot_gv'];
  if ($use_gv == 1) {
    $_SESSION['cot_gv'] = true;
    if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
      $_SESSION['old_payment'] = $_SESSION['payment'];
    }
  } else {
    $_SESSION['cot_gv'] = false;
    if (!empty($_SESSION['old_payment'])) {
      $_SESSION['payment'] = $_SESSION['old_payment'];
      unset($_SESSION['old_payment']);
    }
  }
  $order = new order();
  $order_total_modules = new order_total;
  $order_total_modules->process();
  if ($order->info['total'] <= 0) {
    if (!empty($_SESSION['old_payment'])) {
      unset($_SESSION['payment']);
    }
  }
  $order = new order;
  $order_total_modules = new order_total;
  $order_total_modules->process();
  if ($order->info['total'] <= 0) {
    if (!empty($_SESSION['old_payment'])) {
      $_SESSION['payment'] = $_SESSION['old_payment'];
    }
    $_SESSION['payment'] = 'no_payment';
  }
  
  $virtual = $checkout->isVirtual();
  $payment_modules = new payment();
  $payment_modules->update_status();
  
  if ($order->info['total'] <= 0) {
    $json_response['gvcover'] = true;
  } else {
    $json_response['gvcover'] = false;
  }
  
  if ($virtual) {
    $json_response['virtual'] = true;
  } else {
    if ($checkout->isFreeShipping()) {
      $json_response['is_free_shipping'] = true;
    }  else {
      $json_response['is_free_shipping'] = false;
    }
  }
  
  $json_response = array_merge($json_response, array(
    'form_url'        => auto_encode($checkout->getFormUrl()),
    'hidden_inputs'   => auto_encode($checkout->getProcessButton()),
    'order_total'     => auto_encode($checkout->getTotalBlock())
  ));
  
  
} else if ($action == 'update_product') {
  
  
  unset($_SESSION['actual_content']);
  
  $id = $_GET['products_id'];
  $type = $_GET['type'];
  $qty_now = (int) $_SESSION['cart']->get_quantity($id);
  
  if ($type == 'increase') {
    if ($qty_now >= MAX_PRODUCTS_QTY) {
      $qty_new = MAX_PRODUCTS_QTY; 
    } else {
      $qty_new = $qty_now + 1;
    }
  } elseif ($type == 'decrease') {
    if ($qty_now <= 1) {
      $qty_new = $qty_now;
    } else {
      $qty_new = $qty_now - 1;
    }
  }
  
  if (STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT != 'true') {
    $stock_check = true;
  }
  if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT != 'true') {
    $attribute_stock_check = true;
  }
  
  $count_error_attributes = 0;
  
  $real_product_id = substr($id, 0, strpos($id, '{'));
  $attributes_string = substr($id, strpos($id, '{'));
  $attributes_sub_arr = explode('{', $attributes_string);
  $options = array();
  $values = array();
  for ($a=0; $a<count($attributes_sub_arr); $a++) {
    if (strlen($attributes_sub_arr[$a]) > 2) {
      $attributes_main_arr = explode('}', $attributes_sub_arr[$a]);
      array_push($options, $attributes_main_arr[0]);
      $attribute_value_query = xtc_db_query("SELECT products_options_value_id FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WHERE customers_id = '".$_SESSION['customer_id']."' AND products_id = '".$id."' AND products_options_id = '".$attributes_main_arr[0]."'");
      $attribute_value_arr = xtc_db_fetch_array($attribute_value_query);
      array_push($values, $attribute_value_arr['products_options_value_id']);
    }
  }
  
  for ($b=0; $b<count($options); $b++) {
    $select_attributes_id_query = xtc_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = '".$real_product_id."' AND options_id = '".$options[$b]."' AND options_values_id = '".$values[$b]."'");
    $select_attributes_id_arr = xtc_db_fetch_array($select_attributes_id_query);
    if ($select_attributes_id_arr && xtc_check_stock_attributes($select_attributes_id_arr['products_attributes_id'], $qty_new) != '') {
      $count_error_attributes++;
    }
  }
  
  if (($stock_check ? (xtc_check_stock($id, $qty_new) != '') : false) || ($attribute_stock_check ? ($count_error_attributes > 0) : false)) {
    $json_response['alert'] = auto_encode(html_entity_decode(CHECKOUT_OUT_OF_STOCK));
  } else {
    $_SESSION['cart']->update_quantity($id, $qty_new, $_SESSION['cart']->contents[$id]['attributes']);
    if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] && !empty($_SESSION['customers_status']['customers_status_min_order'])) {
      if ($type == 'increase') {
        $qty_new--;
      } elseif ($type == 'decrease') {
        $qty_new++;
      }
      $_SESSION['cart']->update_quantity($id, $qty_new, $_SESSION['cart']->contents[$id]['attributes']);
      die(json_encode(array('alert' => auto_encode(html_entity_decode(CHECKOUT_OUT_OF_STOCK)))));
    }
    if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] && !empty($_SESSION['customers_status']['customers_status_max_order'])) {
      if ($type == 'increase') {
        $qty_new--;
      } elseif ($type == 'decrease') {
        $qty_new++;
      }
      $_SESSION['cart']->update_quantity($id, $qty_new, $_SESSION['cart']->contents[$id]['attributes']);
      die(json_encode(array('alert' => auto_encode(html_entity_decode(CHECKOUT_MAX_ERROR)))));
    }
    $order = new order;
    $price_new = 0;
    $tax_class_id = $checkout->getTaxID($id);

    $price_new = $xtPrice->xtcGetPrice($id, $format=false, $qty_new, $tax_class_id, '')+$xtPrice->xtcFormat($_SESSION['cart']->attributes_price($id), false);
    $price_new *= $qty_new;
    $total_weight = $_SESSION['cart']->show_weight();
    $total_count = $_SESSION['cart']->count_contents();
    
    if (!empty($_SESSION['shipping']['id'])) {
      list ($module, $method) = explode('_', $_SESSION['shipping']['id']);
      if (!is_object($shipping_modules)) {
        $shipping_modules = new shipping;
      }
      $quote = $shipping_modules->quote($method, $module);
      $_SESSION['shipping']['cost'] = $quote[0]['methods'][0]['cost'];
    }
    $order = new order;
    $shipping_block = $checkout->getShippingBlock();
    $order = new order;
    $virtual = $checkout->isVirtual();
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $payment_modules = new payment();
    $payment_modules->update_status();
    
    global $payment_compatible;
    $payment_compatible = true;
    $payment_block = $checkout->getPaymentBlock();
    
    if ($virtual) {
      $json_response['is_virtual'] = true;
    } else {
      $json_response['is_free_shipping'] = $checkout->isFreeShipping();
    }
    
    if ($order->info['total'] <= 0) {
      if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
        $_SESSION['old_payment'] = $_SESSION['payment'];
        $_SESSION['payment'] = 'no_payment';
      }
      $order = new order;
      $order_total_modules = new order_total;
      $order_total_modules->process();
      
      $json_response['gvcover'] = true;
    } else {
      if (!empty($_SESSION['old_payment'])) {
        $_SESSION['payment'] = $_SESSION['old_payment'];
        unset($_SESSION['old_payment']);
        $order = new order;
        $order_total_modules = new order_total;
        $order_total_modules->process();
      }
      $json_response['gvcover'] = false;
    }
    
    $json_response = array_merge($json_response, array(
      'form_url'          => auto_encode($checkout->getFormUrl()),
      'hidden_inputs'     => auto_encode($checkout->getProcessButton()),
      'order_total'       => auto_encode($checkout->getTotalBlock()),
      'payment_options'   => auto_encode($payment_block),
      'shipping_options'  => auto_encode($shipping_block),
      'products_qty'      => $qty_new,
      'products_price'    => auto_encode($xtPrice->xtcFormat($price_new, true)),
      'products_count'    => $_SESSION['cart']->count_contents()
    ));
  }
  
  
} else if ($action == 'remove_product') {
  
  
  unset($_SESSION['actual_content']);
  $id = $_GET['products_id'];
  $tax_class_id = $checkout->getTaxID($id);
  $qty_p = $_SESSION['cart']->get_quantity($id);
  $products_query = xtc_db_query("select products_price from ".TABLE_PRODUCTS." where products_id='".$id."'");
  $products = xtc_db_fetch_array($products_query);
  $price_p = $xtPrice->xtcGetPrice($id, false, $qty_p, $tax_class_id, $products['products_price']);
  $diff = $_SESSION['cart']->show_total()-($price_p*$_SESSION['cart']->get_quantity($id));
  
  if ($diff < $_SESSION['customers_status']['customers_status_min_order'] && !empty($_SESSION['customers_status']['customers_status_min_order'])) {
    die(json_encode(array('alert' => auto_encode(html_entity_decode(CHECKOUT_MIN_ERROR)))));
  }
  
  if ($diff > $_SESSION['customers_status']['customers_status_max_order'] && !empty($_SESSION['customers_status']['customers_status_max_order'])) {
    die(json_encode(array('alert' => auto_encode(html_entity_decode(CHECKOUT_MAX_ERROR)))));
  }
  
  $_SESSION['cart']->remove($id);
  $_SESSION['cart']->restore_contents();
  
  if ($_SESSION['cart']->count_contents() > 0) {
    $order = new order;
  
    $total_weight = $_SESSION['cart']->show_weight();
    $total_count = $_SESSION['cart']->count_contents();
    
    if (!empty($_SESSION['shipping']['id'])) {
      list ($module, $method) = explode('_', $_SESSION['shipping']['id']);
      if (!is_object($shipping_modules)) {
        $shipping_modules = new shipping;
      }
      $quote = $shipping_modules->quote($method, $module);
      $_SESSION['shipping']['cost'] = $quote[0]['methods'][0]['cost'];
    }
    $order = new order;
    $shipping_block = $checkout->getShippingBlock();
    $order = new order;
    $virtual = $checkout->isVirtual();
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $payment_modules = new payment();
    $payment_modules->update_status();

    global $payment_compatible;
    $payment_compatible = true;
    $payment_block = $checkout->getPaymentBlock();

    if ($order->info['total'] <= 0) {
      if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
        $_SESSION['old_payment'] = $_SESSION['payment'];
        $_SESSION['payment'] = 'no_payment';
      }
      $order = new order;
      $order_total_modules = new order_total;
      $order_total_modules->process();
      
      $json_response['gvcover'] = true;
    } else {
      if (!empty($_SESSION['old_payment'])) {
        $_SESSION['payment'] = $_SESSION['old_payment'];
        unset($_SESSION['old_payment']);      
        $order = new order;
        $order_total_modules = new order_total;
        $order_total_modules->process();
      }
      $json_response['gvcover'] = false;
    }
    
    $json_response['success'] = true;
    
    if ($virtual) {
      $json_response['is_virtual'] = true;
    } else {
      $json_response['is_free_shipping'] = $checkout->isFreeShipping();
    }
  } else {
    $order = new order;
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $shipping_block = CHECKOUT_EMPTY_CART;
    $json_response['redirect'] = FILENAME_SHOPPING_CART;
  }
  
  $json_response = array_merge($json_response, array(
    'form_url'          => auto_encode($checkout->getFormUrl()),
    'hidden_inputs'     => auto_encode($checkout->getProcessButton()),
    'order_total'       => auto_encode($checkout->getTotalBlock()),
    'payment_options'   => auto_encode($payment_block),
    'shipping_options'  => auto_encode($shipping_block),
    'products_count'    => $_SESSION['cart']->count_contents()
  ));
  
  
} else if ($action == 'update_attribute') {
  
  
  $att_string = $_GET['attribute'];
  unset($_SESSION['actual_content']);
  
  $att_array = explode('|', $att_string);
  $old_att_query = xtc_db_query("SELECT products_options_value_id FROM ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WHERE products_options_id = '".$att_array[1]."' AND products_id = '".$att_array[0]."'");
  $old_att_array = xtc_db_fetch_array($old_att_query);
  $old_att = $old_att_array['products_options_value_id'];
  
  xtc_db_query("UPDATE ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." SET products_options_value_id = '".$att_array[2]."' WHERE products_options_id = '".$att_array[1]."' AND products_id = '".$att_array[0]."'");
  
  $_SESSION['cart']->restore_contents();
  
  if (STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT != 'true') {
    $stock_check = true;
  }
  
  if (ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT != 'true') {
    $attribute_stock_check = true;
  }
  
  $attribute_id_query = xtc_db_query("SELECT products_attributes_id FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE options_values_id = '".$att_array[2]."'");
  $attribute_id_arr = xtc_db_fetch_array($attribute_id_query);
  $attribute_id = $attribute_id_arr['products_attributes_id'];
  
  
  if (($stock_check ? (xtc_check_stock($att_array[0], $_SESSION['cart']->contents[$att_array[0]]['qty']) != '') : false) || ($attribute_stock_check ? (xtc_check_stock_attributes($attribute_id, $_SESSION['cart']->contents[$att_array[0]]['qty']) != '') : false)) {
    xtc_db_query("UPDATE ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." SET products_options_value_id = '".$old_att."' WHERE products_options_id = '".$att_array[1]."' AND products_id = '".$att_array[0]."'");
    $_SESSION['cart']->restore_contents();
    $new_value = $att_array[0].'|'.$att_array[1].'|'.$old_att;
    $json_response['alert'] = auto_encode(html_entity_decode(CHECKOUT_OUT_OF_STOCK));
    $json_response['restore_attribute'] = $new_value;
  } else {
    if ($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'] && !empty($_SESSION['customers_status']['customers_status_min_order'])) {
      xtc_db_query("UPDATE ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." SET products_options_value_id = '".$old_att."' WHERE products_options_id = '".$att_array[1]."' AND products_id = '".$att_array[0]."'");
      $_SESSION['cart']->restore_contents();
      $new_value = $att_array[0].'|'.$att_array[1].'|'.$old_att;
      die(json_encode(array('alert' => auto_encode(html_entity_decode(CHECKOUT_MIN_ERROR)), 'restore_attribute' => $new_value)));
    }
    if ($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'] && !empty($_SESSION['customers_status']['customers_status_max_order'])) {
      xtc_db_query("UPDATE ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." SET products_options_value_id = '".$old_att."' WHERE products_options_id = '".$att_array[1]."' AND products_id = '".$att_array[0]."'");
      $_SESSION['cart']->restore_contents();
      $new_value = $att_array[0].'|'.$att_array[1].'|'.$old_att;
      die(json_encode(array('alert' => auto_encode(html_entity_decode(CHECKOUT_MAX_ERROR)), 'restore_attribute' => $new_value)));
    }
    $price_new = 0;
    $tax_class_id = $checkout->getTaxID($att_array[0]);
    
    $price_new = $xtPrice->xtcGetPrice($att_array[0], false, $_SESSION['cart']->contents[$att_array[0]]['qty'], $tax_class_id,'')+$xtPrice->xtcFormat($_SESSION['cart']->attributes_price($att_array[0]), false);
    $price_new *= $_SESSION['cart']->contents[$att_array[0]]['qty'];
    $query = xtc_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE language_id = '".$_SESSION['languages_id']."' AND products_options_values_id = '".$att_array[2]."'");
    $row = xtc_db_fetch_array($query);  
    
    $order = new order;
    
    $total_weight = $_SESSION['cart']->show_weight();
    $total_count = $_SESSION['cart']->count_contents();
    
    if (!empty($_SESSION['shipping']['id'])) {
      list ($module, $method) = explode('_', $_SESSION['shipping']['id']);
      if (!is_object($shipping_modules)) {
        $shipping_modules = new shipping;
      }
      $quote = $shipping_modules->quote($method, $module);
      $_SESSION['shipping']['cost'] = $quote[0]['methods'][0]['cost'];
    }
    
    $virtual = $checkout->isVirtual();
    $order = new order;
    $shipping_block = $checkout->getShippingBlock();
    $order = new order;
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $payment_modules = new payment();
    $payment_modules->update_status();
    
    global $payment_compatible;
    $payment_compatible = true;
    $payment_block = $checkout->getPaymentBlock();
    
    if ($virtual) {
      $json_response['is_virtual'] = true;
    } else {
      $json_response['is_free_shipping'] = $checkout->isFreeShipping();
    }
    
    if ($order->info['total'] <= 0) {
      if (!empty($_SESSION['payment']) && $_SESSION['payment'] != 'no_payment') {
        $_SESSION['old_payment'] = $_SESSION['payment'];
        $_SESSION['payment'] = 'no_payment';
      }
      $order = new order;
      $order_total_modules = new order_total;
      $order_total_modules->process();
      
      $json_response['gvcover'] = true;
    } else {
      if (!empty($_SESSION['old_payment'])) {
        $_SESSION['payment'] = $_SESSION['old_payment'];
        unset($_SESSION['old_payment']);
        $order = new order;
        $order_total_modules = new order_total;
        $order_total_modules->process();
      }
      $json_response['gvcover'] = false;
    }
    
    $json_response = array_merge($json_response, array(
      'products_price'    => auto_encode($xtPrice->xtcFormat($price_new, true)),
      'form_url'          => auto_encode($checkout->getFormUrl()),
      'hidden_inputs'     => auto_encode($checkout->getProcessButton()),
      'order_total'       => auto_encode($checkout->getTotalBlock()),
      'payment_options'   => auto_encode($payment_block),
      'shipping_options'  => auto_encode($shipping_block)
    ));
  }
  
  
} else if ($action == 'checkout_check') {
  
  
  $_SESSION['comments'] = strip_tags($_POST['comments']);
  
  $order = new order();
  $shipping_modules = new shipping;
  
  $error = array();
  if (empty($_SESSION['shipping']) && !$checkout->isVirtual() && !$checkout->isFreeShipping() && (xtc_not_null(MODULE_SHIPPING_INSTALLED))) {
    $error[] = '- '.CHECKOUT_NO_SHIPPING_MODULE_SELECTED;
  }
  
  if (empty($_SESSION['payment']) && (xtc_not_null(MODULE_PAYMENT_INSTALLED))) {
    $error[] = '- '.CHECKOUT_NO_PAYMENT_MODULE_SELECTED;
  }
  
  if (count($error) > 0) {
    $json_response['checkout_error'] = auto_encode(implode('<br />', $error));
  } else{
    $json_response['success'] = true;
  }
  
  
} else if ($action == 'get_states') {
  
  
  $zones_array = array();
  $zones_query = xtc_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".xtc_db_input($_GET['country'])."' order by zone_name");
  while ($zones_values = xtc_db_fetch_array($zones_query)) {
    $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
  }
  
  if (count($zones_array) > 0) {
    $json_response['state_select'] = auto_encode(xtc_draw_pull_down_menuNote(array('name' => 'state'), $zones_array));
  }
  
  
} else if ($action == 'update_address') {
  
  
  $type = $_POST['type'];
  if (!$checkout->isNewAddressPossible()) {
    die(json_encode(array('error' => '- '.CHECKOUT_NOMORE_ADDRESSES)));
  }
  if ($type == 'shipping') {
    $sess = 'sendto';
  } elseif ($type == 'payment') {
    $sess = 'billto';
  }
  
  require_once (DIR_FS_INC.'xtc_get_country_name.inc.php');
  require_once (DIR_FS_INC.'xtc_get_zone_code.inc.php');
  
  $error = false;
  $error_text = '';
  if (ACCOUNT_GENDER == 'true') {
    $gender = xtc_db_prepare_input($_POST['gender']);
  }
  if (ACCOUNT_COMPANY == 'true') {
    $company = xtc_db_prepare_input($_POST['company']);
  }
  $firstname = xtc_db_prepare_input($_POST['firstname']);
  $lastname = xtc_db_prepare_input($_POST['lastname']);
  $street_address = xtc_db_prepare_input($_POST['street_address']);
  if (ACCOUNT_SUBURB == 'true') {
    $suburb = xtc_db_prepare_input($_POST['suburb']);
  }
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
      $error_text .= '- '.ENTRY_GENDER_ERROR.'<br />';
    }
  }

  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $error_text .= '- '.ENTRY_FIRST_NAME_ERROR.'<br />';
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $error_text .= '- '.ENTRY_LAST_NAME_ERROR.'<br />';
  }

  if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
    $error = true;
    $error_text .= '- '.ENTRY_STREET_ADDRESS_ERROR.'<br />';
  }

  if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
    $error = true;
    $error_text .= '- '.ENTRY_POST_CODE_ERROR.'<br />';
  }

  if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
    $error = true;
    $error_text .= '- '.ENTRY_CITY_ERROR.'<br />';
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
        $error_text .= '- '.ENTRY_STATE_ERROR_SELECT.'<br />';
      }
    } else {
      if (strlen($state) < ENTRY_STATE_MIN_LENGTH && $zone_id > 0) {
        $error = true;
        $error_text .= '- '.ENTRY_STATE_ERROR.'<br />';
      }
    }
  }

  if ((is_numeric($country) == false) || ($country < 1)) {
    $error = true;
    $error_text .= '- '.ENTRY_COUNTRY_ERROR.'<br />';
  }

  if ($error == false) {
    $sql_data_array = array ('customers_id' => $_SESSION['customer_id'], 'entry_firstname' => $firstname, 'entry_lastname' => $lastname, 'entry_street_address' => $street_address, 'entry_postcode' => $postcode, 'entry_city' => $city, 'entry_country_id' => $country);

    if (ACCOUNT_GENDER == 'true') {
      $sql_data_array['entry_gender'] = $gender;
    }
    if (ACCOUNT_COMPANY == 'true') {
      $sql_data_array['entry_company'] = $company;
    }
    if (ACCOUNT_SUBURB == 'true') {
      $sql_data_array['entry_suburb'] = $suburb;
    }
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

    $_SESSION[$sess] = xtc_db_insert_id();
    
    if ($type == 'shipping') {
      $xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
      $order = new order();
      
      $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
      
      $json_response['is_free_shipping'] = $checkout->isFreeShipping();
      
      $json_response['product_prices'] = array();
      $product_ids = explode(', ',$_SESSION['cart']->get_product_id_list());
      for ($b=0; $b<count($product_ids); $b++) {
        $tax_class_id = $checkout->getTaxID($product_ids[$b]);
        $product_qty = $_SESSION['cart']->contents[$product_ids[$b]]['qty'];
        $product_price = $xtPrice->xtcGetPrice($product_ids[$b], $format=false, $product_qty, $tax_class_id, '')+$xtPrice->xtcFormat($_SESSION['cart']->attributes_price($product_ids[$b]), false);
        $product_price *= $product_qty;
        $json_response['product_prices'][$product_ids[$b]] = auto_encode($xtPrice->xtcFormat($product_price, true));
      }
    }
    
    if ($type == 'shipping' && isset ($_SESSION['credit_covers'])) {
      $_SESSION['payment'] = 'no_payment';
    }
    if ($type == 'shipping') {
      $total_weight = $_SESSION['cart']->show_weight();
      $total_count = $_SESSION['cart']->count_contents();
      $order = new order;
      if (!empty($_SESSION['shipping']['id'])) {
        list ($module, $method) = explode('_', $_SESSION['shipping']['id']);
        if (!is_object($shipping_modules)) {
          $shipping_modules = new shipping;
        }
        $quote = $shipping_modules->quote($method, $module);
        if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
          $_SESSION['shipping'] = array ('id' => $_SESSION['shipping']['id'], 'title' => ($checkout->isFreeShipping() ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);
        }
      }
      $shipping_block = $checkout->getShippingBlock();
      $order = new order;
      $order_total_modules = new order_total;
      $order_total_modules->process();
      $payment_modules = new payment();
      $payment_modules->update_status();
      
      $json_response['shipping_options'] = auto_encode($shipping_block);
      $json_response['payment_options'] = auto_encode($checkout->getPaymentBlock());
      $json_response['hidden_inputs'] = auto_encode($checkout->getProcessButton());
    }
    
    if ($type == 'shipping') {
      $json_response['order_total'] = auto_encode($checkout->getTotalBlock());
    }
    
    $json_response[$type . '_address'] = auto_encode(xtc_address_label($_SESSION['customer_id'], $_SESSION[$sess], true, ' ', '<br />'));
    $json_response[$type . '_address_dropdown'] = auto_encode($checkout->getAddresses($type));
    $json_response['form_url'] = auto_encode($checkout->getFormUrl());
  } else {
    $json_response['error'] = auto_encode($error_text);
  }
  
  
} else if ($action == "update_address_by_dropdown") {
  
  
  $address_id = $_GET['address_id'];
  $type = $_GET['type'];
  if ($type == 'shipping') {
    $sess = 'sendto';
  } elseif ($type == 'payment') {
    $sess = 'billto';
  }
  $_SESSION[$sess] = (int) $address_id;
  if ($type == 'shipping') {
    $xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
    $order = new order();
    $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
    
    $json_response['is_free_shipping'] = $checkout->isFreeShipping();
    $json_response['product_prices'] = array();
    $product_ids = explode(', ',$_SESSION['cart']->get_product_id_list());
    for ($b=0; $b<count($product_ids); $b++) {
      $tax_class_id = $checkout->getTaxID($product_ids[$b]);
      $product_qty = $_SESSION['cart']->contents[$product_ids[$b]]['qty'];
      $product_price = $xtPrice->xtcGetPrice($product_ids[$b], $format=false, $product_qty, $tax_class_id, '')+$xtPrice->xtcFormat($_SESSION['cart']->attributes_price($product_ids[$b]), false);
      $product_price *= $product_qty;
      $json_response['product_prices'][$product_ids[$b]] = auto_encode($xtPrice->xtcFormat($product_price, true));
    }
  }
  
  $json_response[$type . '_address'] = auto_encode(xtc_address_label($_SESSION['customer_id'], $_SESSION[$sess], true, ' ', '<br />'));
  if ($type == 'shipping' && isset ($_SESSION['credit_covers'])) {
    $_SESSION['payment'] = 'no_payment';
  }
  
  if ($type == 'shipping') {
    $total_weight = $_SESSION['cart']->show_weight();
    $total_count = $_SESSION['cart']->count_contents();
    $order = new order;
    if (!empty($_SESSION['shipping']['id'])) {
      list ($module, $method) = explode('_', $_SESSION['shipping']['id']);
      if (!is_object($shipping_modules)) {
        $shipping_modules = new shipping;
      }
      $quote = $shipping_modules->quote($method, $module);
      if ((isset ($quote[0]['methods'][0]['title'])) && (isset ($quote[0]['methods'][0]['cost']))) {
        $_SESSION['shipping'] = array ('id' => $_SESSION['shipping']['id'], 'title' => ($checkout->isFreeShipping() ? $quote[0]['methods'][0]['title'] : $quote[0]['module'].' ('.$quote[0]['methods'][0]['title'].')'), 'cost' => $quote[0]['methods'][0]['cost']);
      }
    }
    $shipping_block = $checkout->getShippingBlock();
    $order = new order;
    $order_total_modules = new order_total;
    $order_total_modules->process();
    $payment_modules = new payment();
    $payment_modules->update_status();
    
    $json_response['shipping_options'] = auto_encode($shipping_block);
    $json_response['payment_options'] = auto_encode($checkout->getPaymentBlock());
    $json_response['hidden_inputs'] = auto_encode($checkout->getProcessButton());
  }
  
  if ($type == 'shipping') {
    $json_response['order_total'] = auto_encode($checkout->getTotalBlock());
  }

  $json_response[$type . '_address'] = auto_encode(xtc_address_label($_SESSION['customer_id'], $_SESSION[$sess], true, ' ', '<br />'));
  $json_response['form_url'] = auto_encode($checkout->getFormUrl());
  
  
} else if ($action == 'get_address_dropdown') {
  
  
  $type = $_GET['type'];
  $json_response[$type . '_address_dropdown'] = auto_encode($checkout->getAddresses($type));
  
  
}

echo json_encode($json_response);
?>
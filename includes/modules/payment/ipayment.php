<?php

/* -----------------------------------------------------------------------------------------
   $Id$   

   Phoenix Medien GmbH & Co. KG
   http://www.phoenix-medien.de

   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2003 XT-Commerce (Rev.998 2005/07/07); www.xt-commerce.com 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ipayment.php,v 1.32 2003/01/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (ipayment.php,v 1.9 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class ipayment {
	var $code, $title, $description, $enabled, $version = '1.0.2';

	function ipayment() {
		global $order;

		$this->code = 'ipayment';
		$this->title = MODULE_PAYMENT_IPAYMENT_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_IPAYMENT_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_IPAYMENT_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_IPAYMENT_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_IPAYMENT_TEXT_INFO;
		if ((int) MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://ipayment.de/merchant/'.MODULE_PAYMENT_IPAYMENT_ID.'/processor.php';
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_IPAYMENT_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_IPAYMENT_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	function javascript_validation() {
		return '';
	}

	function selection() {
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return array();
	}

	function process_button() {
		global $order, $xtPrice, $customers_ip, $insert_id;
		
			// render form
		for ($i = 1; $i < 13; $i ++) {
			$expires_month[] = array ('id' => sprintf('%02d', $i), 'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)));
		}

		$today = getdate();
		for ($i = $today['year']; $i < $today['year'] + 10; $i ++) {
			$expires_year[] = array ('id' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)));
		}
					
		$process_button_string = '
			<div class="cc_form">
				<label for="cc_form_addr_name">'.MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER.'</label>
					'.xtc_draw_input_field('addr_name', $order->billing['firstname'].' '.$order->billing['lastname'], 'id="cc_form_addr_name"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<label for="cc_form_cc_number">'.MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER.'</label>
					'.xtc_draw_input_field('cc_number', '', 'id="cc_form_cc_number"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<label for="cc_form_cc_expdate_month">'.MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES.'</label>
					'.xtc_draw_pull_down_menu('cc_expdate_month', $expires_month, '', 'id="cc_form_cc_expdate_month"').'&nbsp;'.xtc_draw_pull_down_menu('cc_expdate_year', $expires_year).'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<label for="cc_form_cc_checkcode">'.MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER.'</label>
					'.xtc_draw_input_field('cc_checkcode', '', 'size="4" maxlength="4" id="cc_form_cc_checkcode"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				'.MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION.'
			</div>
		';

		switch (MODULE_PAYMENT_IPAYMENT_CURRENCY) {
			case 'Always EUR' :
				$trx_currency = 'EUR';
				break;
			case 'Always USD' :
				$trx_currency = 'USD';
				break;
			case 'Either EUR or USD, else EUR' :
				if (($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD')) {
					$trx_currency = $_SESSION['currency'];
				} else {
					$trx_currency = 'EUR';
				}
				break;
			case 'Either EUR or USD, else USD' :
				if (($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD')) {
					$trx_currency = $_SESSION['currency'];
				} else {
					$trx_currency = 'USD';
				}
				break;
		}
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$total = $order->info['total'] + $order->info['tax'];
		} else {
			$total = $order->info['total'];
		}
		if ($_SESSION['currency'] == $trx_currency) {
			$amount = round($total, $xtPrice->get_decimal_places($trx_currency));
		} else {
			$amount = round($xtPrice->xtcCalculateCurrEx($total, $trx_currency), $xtPrice->get_decimal_places($trx_currency));
		}
		
			// save amount in session
		$_SESSION[$this->code.'_amount'] = round($amount * 100, 0);

		$process_button_string .=	xtc_draw_hidden_field('silent', '1').
									xtc_draw_hidden_field('trx_paymenttyp', 'cc').
									xtc_draw_hidden_field('trxuser_id', MODULE_PAYMENT_IPAYMENT_USER_ID).
									xtc_draw_hidden_field('trxpassword', MODULE_PAYMENT_IPAYMENT_PASSWORD).
									xtc_draw_hidden_field('item_name', STORE_NAME).
									xtc_draw_hidden_field('client_name', PROJECT_VERSION).
									xtc_draw_hidden_field('client_version', $this->code.' '.$this->version).
									xtc_draw_hidden_field('trx_currency', $trx_currency).
									xtc_draw_hidden_field('trx_amount', round($amount * 100, 0)).
									xtc_draw_hidden_field('payment_error', $this->code).
									xtc_draw_hidden_field('conditions', 'conditions').
									xtc_draw_hidden_field('addr_email', $order->customer['email_address']).
									xtc_draw_hidden_field('redirect_url', xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true)).
									xtc_draw_hidden_field('silent_error_url', xtc_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL', true));

		if (defined('MODULE_PAYMENT_IPAYMENT_SECURITY_KEY') && MODULE_PAYMENT_IPAYMENT_SECURITY_KEY != '')
			$process_button_string .= xtc_draw_hidden_field('trx_securityhash', md5(MODULE_PAYMENT_IPAYMENT_USER_ID . round($amount * 100, 0) . $trx_currency . MODULE_PAYMENT_IPAYMENT_PASSWORD . MODULE_PAYMENT_IPAYMENT_SECURITY_KEY));

		return $process_button_string;
	}

	function before_process() {
		$errorMsg = '';
	
		switch (MODULE_PAYMENT_IPAYMENT_CURRENCY) {
			case 'Always EUR' :
				$trx_currency = 'EUR';
				break;
			case 'Always USD' :
				$trx_currency = 'USD';
				break;
			case 'Either EUR or USD, else EUR' :
				if (($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD')) {
					$trx_currency = $_SESSION['currency'];
				} else {
					$trx_currency = 'EUR';
				}
				break;
			case 'Either EUR or USD, else USD' :
				if (($_SESSION['currency'] == 'EUR') || ($_SESSION['currency'] == 'USD')) {
					$trx_currency = $_SESSION['currency'];
				} else {
					$trx_currency = 'USD';
				}
				break;
		}
	
			// check for required fields
		$requiredFields = array('trxuser_id', 'trx_amount', 'trx_currency', 'ret_booknr', 'ret_status');
		foreach ($requiredFields as $key) {
			if (empty($_GET[$key]))
				$errorMsg = 'Transaction "'.$key.'" key not found.';
		}
		
			// check return status
		if ($_GET['ret_status'] != 'SUCCESS')
			$errorMsg = 'Return status not verified.';

			// check amount
		if (empty($_SESSION[$this->code.'_amount']) || (int)$_GET['trx_amount'] != $_SESSION[$this->code.'_amount'])
			$errorMsg = 'Amount doesn\'t match.';
		
			// check security hash
		if (defined('MODULE_PAYMENT_IPAYMENT_SECURITY_KEY') && MODULE_PAYMENT_IPAYMENT_SECURITY_KEY != '') {
			if (empty($_GET['ret_param_checksum']))
				$errorMsg = 'Checksum not submitted.';
				
			$hash = md5(MODULE_PAYMENT_IPAYMENT_USER_ID . $_SESSION[$this->code.'_amount'] . $trx_currency . $_GET['ret_authcode'] . $_GET['ret_booknr'] . MODULE_PAYMENT_IPAYMENT_SECURITY_KEY);
			if ($hash !== $_GET['ret_param_checksum'])
				$errorMsg = 'Security hash does not match ('.$_GET['ret_param_checksum'].')';
		}
		
			// check for duplicated booking no.
		if (!empty($_GET['ret_booknr'])) {
			$check_query = xtc_db_query('SELECT ip_BOOKNR FROM payment_ipayment WHERE ip_BOOKNR = '.xtc_db_input($_GET['ret_booknr']));
			if (xtc_db_num_rows($check_query) > 0)
				$errorMsg = 'Booking no. already exists.';
		} else {
			$errorMsg = 'Booking no. already exists.';
		}

		if (!empty($errorMsg)) {
			unset($_SESSION[$this->code.'_amount']);
			
				// log error
			xtc_db_query("INSERT INTO payment_ipayment_log (ip_LOG_MESSAGE, ip_LOG_INFO, ip_LOG_DATE) VALUES ('".xtc_db_input($errorMsg)."', '".xtc_db_input(serialize($_GET))."', now())");
			
			$payment_error_return = 'payment_error='.$this->code.'&ret_errormsg='.urlencode(IPAYMENT_ERROR_HEADING);
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		
		unset($_SESSION[$this->code.'_amount']);
		return false;
	}

	function after_process() {
		global $insert_id;
		if ($this->order_status) {
				// save transaction data
			$transData = array();
			foreach($_GET as $key => $val) {
				if (substr($key, 0, 4) == 'ret_')
					$transData[$key] = xtc_db_prepare_input($val);
			}
			
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
			xtc_db_query("INSERT INTO payment_ipayment (ip_BOOKNR, ip_INFO, ip_ORDERID, ip_IS_CAPTURED) VALUES ('".xtc_db_input($transData['ret_booknr'])."', '".xtc_db_input(serialize($transData))."', ".$insert_id.", 0)");
		}
	}

	function get_error() {
			// log error
		xtc_db_query("INSERT INTO payment_ipayment_log (ip_LOG_MESSAGE, ip_LOG_INFO, ip_LOG_DATE) VALUES ('".((isset ($_GET['ret_errormsg'])) ? xtc_db_input(urldecode($_GET['ret_errormsg'])) : IPAYMENT_ERROR_MESSAGE)."', '".xtc_db_input(serialize($_GET))."', now())");

		$error = array ('title' => IPAYMENT_ERROR_HEADING, 'error' => ((isset ($_GET['ret_errormsg'])) ? stripslashes(urldecode($_GET['ret_errormsg'])) : IPAYMENT_ERROR_MESSAGE));

		return $error;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_IPAYMENT_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_ALLOWED', '', '6', '0', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_ID', '99999', '6', '2', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_USER_ID', '99998', '6', '3', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_PASSWORD', '0', '6', '4', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_SECURITY_KEY', 'testtest', '6', '5', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_CURRENCY', 'Either EUR or USD, else EUR','6', '5', 'xtc_cfg_select_option(array(\'Always EUR\', \'Always USD\', \'Either EUR or USD, else EUR\', \'Either EUR or USD, else USD\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENT_SORT_ORDER', '0', '6', '0', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID', '0','6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
			// create table for saving transaction data and logging
		xtc_db_query("CREATE TABLE IF NOT EXISTS payment_ipayment (ip_BOOKNR VARCHAR( 255 ) NOT NULL, ip_INFO TEXT NOT NULL, ip_ORDERID INT(11) NOT NULL, ip_IS_CAPTURED TINYINT( 1 ) NOT NULL DEFAULT '0', PRIMARY KEY (ip_BOOKNR), INDEX (ip_ORDERID))");
		xtc_db_query("CREATE TABLE IF NOT EXISTS payment_ipayment_log (ip_LOG_ID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, ip_LOG_MESSAGE VARCHAR( 255 ) NOT NULL, ip_LOG_INFO TEXT NOT NULL, ip_LOG_DATE DATETIME NOT NULL)");
	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		return array ('MODULE_PAYMENT_IPAYMENT_STATUS', 'MODULE_PAYMENT_IPAYMENT_ALLOWED', 'MODULE_PAYMENT_IPAYMENT_ID', 'MODULE_PAYMENT_IPAYMENT_USER_ID', 'MODULE_PAYMENT_IPAYMENT_PASSWORD', 'MODULE_PAYMENT_IPAYMENT_SECURITY_KEY', 'MODULE_PAYMENT_IPAYMENT_CURRENCY', 'MODULE_PAYMENT_IPAYMENT_ZONE', 'MODULE_PAYMENT_IPAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_IPAYMENT_SORT_ORDER');
	}
}
?>
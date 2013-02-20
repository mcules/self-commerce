<?php

/* -----------------------------------------------------------------------------------------
   $Id: ipaymentelv.php 17 2012-06-04 20:33:29Z deisold $   

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

class ipaymentelv {
	var $code, $title, $description, $enabled, $version = '1.0.2';

	function ipaymentelv() {
		global $order;

		$this->code = 'ipaymentelv';
		$this->title = MODULE_PAYMENT_IPAYMENTELV_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_IPAYMENTELV_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_IPAYMENTELV_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_IPAYMENTELV_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_IPAYMENTELV_TEXT_INFO;
		if ((int) MODULE_PAYMENT_IPAYMENTELV_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_IPAYMENTELV_ORDER_STATUS_ID;
		}

		if (is_object($order))
			$this->update_status();

		$this->form_action_url = 'https://ipayment.de/merchant/'.MODULE_PAYMENT_IPAYMENTELV_ID.'/processor.php';
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_IPAYMENTELV_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_IPAYMENTELV_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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
		
		$process_button_string = '
			<div class="cc_form">
				<label for="elv_form_addr_name">'.MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_OWNER.'</label>
					'.xtc_draw_input_field('addr_name', $order->billing['firstname'].' '.$order->billing['lastname'], 'id="elv_form_addr_name"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<label for="elv_form_bank">'.MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_NAME.'</label>
					'.xtc_draw_input_field('bank_name', '', 'id="elv_form_bank"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<label for="elv_form_blz">'.MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_BLZ.'</label>
					'.xtc_draw_input_field('bank_code', '', 'id="elv_form_blz"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<label for="elv_form_account">'.MODULE_PAYMENT_IPAYMENTELV_TEXT_BANK_NUMBER.'</label>
					'.xtc_draw_input_field('bank_accountnumber', '', 'id="elv_form_account"').'&nbsp;<span class="inputRequirement">*</span><br class="clearHere" />
				<p style="padding-top:15px;"><em>'.MODULE_PAYMENT_IPAYMENTELV_TEXT_NOTE.'<br />'.MODULE_PAYMENT_IPAYMENTELV_TEXT_NOTE2.'</em></p>
			</div>
		';

		switch (MODULE_PAYMENT_IPAYMENTELV_CURRENCY) {
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
			$amount = $total;
		} else {
			$amount = $xtPrice->xtcCalculateCurrEx($total, $trx_currency);
		}
		
			// save amount in session
		$_SESSION[$this->code.'_amount'] = round($amount * 100, 0);

		$process_button_string .=	xtc_draw_hidden_field('silent', '1').
									xtc_draw_hidden_field('trx_paymenttyp', 'elv').
									xtc_draw_hidden_field('trxuser_id', MODULE_PAYMENT_IPAYMENTELV_USER_ID).
									xtc_draw_hidden_field('trxpassword', MODULE_PAYMENT_IPAYMENTELV_PASSWORD).
									xtc_draw_hidden_field('item_name', STORE_NAME).
									xtc_draw_hidden_field('client_name', PROJECT_VERSION).
									xtc_draw_hidden_field('client_version', $this->code.' '.$this->version).
									xtc_draw_hidden_field('trx_currency', $trx_currency).
									xtc_draw_hidden_field('trx_amount', round($amount * 100, 0)).
									xtc_draw_hidden_field('payment_error', $this->code).
									xtc_draw_hidden_field('conditions', 'conditions').
									xtc_draw_hidden_field('addr_street', $order->customer['street_address']).
									xtc_draw_hidden_field('addr_street2', '').
									xtc_draw_hidden_field('addr_zip', $order->customer['postcode']).
									xtc_draw_hidden_field('addr_city', $order->customer['city']).
									xtc_draw_hidden_field('addr_country', $order->customer['country']['iso_code_2']).
									xtc_draw_hidden_field('addr_email', $order->customer['email_address']).
									xtc_draw_hidden_field('addr_telefon', $order->customer['telephone']).
									xtc_draw_hidden_field('addr_telefax', '').
									xtc_draw_hidden_field('addr_state', $order->customer['state']).
									xtc_draw_hidden_field('redirect_url', xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true)).
									xtc_draw_hidden_field('silent_error_url', xtc_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL', true));

		if (defined('MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY') && MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY != '')
			$process_button_string .= xtc_draw_hidden_field('trx_securityhash', md5(MODULE_PAYMENT_IPAYMENTELV_USER_ID . round($amount * 100, 0) . $trx_currency . MODULE_PAYMENT_IPAYMENTELV_PASSWORD . MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY));

		return $process_button_string;
	}

	function before_process() {
		$errorMsg = '';

		switch (MODULE_PAYMENT_IPAYMENTELV_CURRENCY) {
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
		if (defined('MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY') && MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY != '') {
			if (empty($_GET['ret_param_checksum']))
				$errorMsg = 'Checksum not submitted.';
				
			$hash = md5(MODULE_PAYMENT_IPAYMENTELV_USER_ID . $_SESSION[$this->code.'_amount'] . $trx_currency . $_GET['ret_authcode'] . $_GET['ret_booknr'] . MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY);
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
			
			$payment_error_return = 'payment_error='.$this->code.'&ret_errormsg='.urlencode(IPAYMENTELV_ERROR_HEADING);
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

		$error = array ('title' => IPAYMENTELV_ERROR_HEADING, 'error' => ((isset ($_GET['ret_errormsg'])) ? stripslashes(urldecode($_GET['ret_errormsg'])) : IPAYMENTELV_ERROR_MESSAGE));

		return $error;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_IPAYMENTELV_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_STATUS', 'True', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_ALLOWED', '', '6', '0', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_ID', '99999', '6', '2', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_USER_ID', '99998', '6', '3', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_PASSWORD', '0', '6', '4', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY', 'testtest', '6', '5', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_CURRENCY', 'Either EUR or USD, else EUR','6', '5', 'xtc_cfg_select_option(array(\'Always EUR\', \'Always USD\', \'Either EUR or USD, else EUR\', \'Either EUR or USD, else USD\'), ', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_SORT_ORDER', '0', '6', '0', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_IPAYMENTELV_ORDER_STATUS_ID', '0','6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
			// create table for saving transaction data and logging
		xtc_db_query("CREATE TABLE IF NOT EXISTS payment_ipayment (ip_BOOKNR VARCHAR( 255 ) NOT NULL, ip_INFO TEXT NOT NULL, ip_ORDERID INT(11) NOT NULL, ip_IS_CAPTURED TINYINT( 1 ) NOT NULL DEFAULT '0', PRIMARY KEY (ip_BOOKNR), INDEX (ip_ORDERID))");
		xtc_db_query("CREATE TABLE IF NOT EXISTS payment_ipayment_log (ip_LOG_ID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, ip_LOG_MESSAGE VARCHAR( 255 ) NOT NULL, ip_LOG_INFO TEXT NOT NULL, ip_LOG_DATE DATETIME NOT NULL)");
	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		return array ('MODULE_PAYMENT_IPAYMENTELV_STATUS', 'MODULE_PAYMENT_IPAYMENTELV_ALLOWED', 'MODULE_PAYMENT_IPAYMENTELV_ID', 'MODULE_PAYMENT_IPAYMENTELV_USER_ID', 'MODULE_PAYMENT_IPAYMENTELV_PASSWORD', 'MODULE_PAYMENT_IPAYMENTELV_SECURITY_KEY', 'MODULE_PAYMENT_IPAYMENTELV_CURRENCY', 'MODULE_PAYMENT_IPAYMENTELV_ZONE', 'MODULE_PAYMENT_IPAYMENTELV_ORDER_STATUS_ID', 'MODULE_PAYMENT_IPAYMENTELV_SORT_ORDER');
	}
}
?>
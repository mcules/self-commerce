<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   am_apa.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
?><?php

define('MODULE_PAYMENT_AM_APA_TEXT_DESCRIPTION','Amazon Advanced Payment APIs by <a href="http://www.alkim.de" target="_blank">>>alkim media<<</a>
<div id="amapaVersion"></div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="//www.alkim.de/amazon_version_control.js.php"></script>
<script type="text/javascript">
$(document).ready(function(){
    if("'.AMZ_VERSION.'" != "AMZ_VERSION" && "'.AMZ_VERSION.'" != "" && typeof(currentAlkimApaVersion) != \'undefined\'){
            var add = "";
            currentVersionArray = currentAlkimApaVersion.split(".");
            thisVersionArray = "'.AMZ_VERSION.'".split(".");
            if(
                    currentVersionArray[0] > thisVersionArray[0] 
                        || 
                    (
                        parseInt(currentVersionArray[0]) == parseInt(thisVersionArray[0])
                            &&
                        parseInt(currentVersionArray[1]) > parseInt(thisVersionArray[1])
                    )
                        || 
                    (
                        parseInt(currentVersionArray[0]) == parseInt(thisVersionArray[0])
                            &&
                        parseInt(currentVersionArray[1]) == parseInt(thisVersionArray[1])
                            &&
                        parseInt(currentVersionArray[2]) > parseInt(thisVersionArray[2])
                    )
                        
            ){
                add += "<div style=\'font-weight:bold; color:#cc0000;\'>'.AMZ_NEW_VERSION_AVAILABLE.' ("+currentAlkimApaVersion+")</div>";
            } else {
            	add += "<div style=\'font-weight:bold; color:green;\'>'.AMZ_VERSION_IS_GOOD.'</div>";
            }
            $("#amapaVersion").html(" Version '.AMZ_VERSION.'"+add);
     }
});
</script>

');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/.config.inc.php');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonTransactions.class.php');
class am_apa {
	var $code, $title, $description, $enabled;

	function am_apa() {
		global $order;
		$this->group_id = 777;
		$this->code = 'am_apa';
		$this->title = '<img src="../AmazonAdvancedPayment/logo.gif" border="0" alt="CBA Logo" />Amazon Advanced Payment APIs';

		$this->sort_order = 0;
		$this->enabled = ((MODULE_PAYMENT_AM_APA_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_AM_APA_TEXT_DESCRIPTION;

       $check = xtc_db_num_rows(xtc_db_query("SELECT configuration_value FROM ". TABLE_CONFIGURATION." WHERE configuration_group_id = '". $this->group_id ."' LIMIT 1"));
		if ($check == 0) {
			$this->description = MODULE_PAYMENT_AM_APA_TEXT_DESCRIPTION;
		} else {
		    if(defined('AMZ_VERSION') && AMZ_VERSION != AMZ_FILE_VERSION){
			        $this->description.= '
			                              <center>
			                                <div style="font-weight:bold; color:#cc0000;">
			                                    '.AMZ_DB_UPDATE_WARNING.'
			                                </div>  
			                                <br />
			                                <a href="'.xtc_href_link('amz_configuration.php', 'action=dbUpdate').'" class="button">
			                                    '.AMZ_DB_UPDATE_BUTTON_TEXT.'
			                                </a>
			                           <br/>';
			}else{
		
			    $this->description = '<center>'.MODULE_PAYMENT_AM_APA_TEXT_DESCRIPTION . '<br/><br/>
			                    <a href="'.xtc_href_link('amz_configuration.php').'" class="button">'.AMZ_CONFIGURATION.'</a>
			                    <br /><br />
			                    <a href="'.xtc_href_link('amz_configuration.php', 'action=transactionHistory').'" class="button">'.AMZ_PROTOCOL.'</a>
			                    
			                    
			                    </center>
			                    ';
			}
			
			
		}



	}

	function update_status() {
		global $order;

		return $this->enabled;

	}

	function javascript_validation() {
		return false;
	}

	function selection() {
		return false;
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		return false;
	}

	function before_process() {
		if(AMZ_AUTHORIZATION_MODE == 'fast_auth'){

		    if(isset($_SESSION["fromWallet"]) && $_SESSION["fromWallet"] == 1){
		            $config                     = AlkimAmazonHandler::getConfig();
                    $service                    = new OffAmazonPaymentsService_Client($config);
                    $confirmOrderRefReqModel = new OffAmazonPaymentsService_Model_ConfirmOrderReferenceRequest;
                    $confirmOrderRefReqModel->setAmazonOrderReferenceId($_SESSION['amazon_id']);
                    $confirmOrderRefReqModel->setSellerId(AWS_MERCHANT_ID);
                    try {
                        $confirmOrderRefResp = $service->confirmOrderReference($confirmOrderRefReqModel);
                    }
                    catch (OffAmazonPaymentsService_Exception $e) {

                    }
                    unset($_SESSION["fromWallet"]);
		    }

		    $response = AlkimAmazonTransactions::fastAuth($_SESSION["amzOrderReference"], $_SESSION["amzOrderTotal"]);
		    if(is_object($response)){
		        $details = $response->getAuthorizeResult()->getAuthorizationDetails();
		        $status = $details->getAuthorizationStatus()->getState();
		        if($status == "Declined"){
		            $reason = $details->getAuthorizationStatus()->getReasonCode();

                    if($reason == 'InvalidPaymentMethod'){

		                xtc_redirect(xtc_href_link('checkout_amazon_wallet.php','', 'SSL'));
		                die;
                    }else{
		                AlkimAmazonTransactions::cancelOrder($_SESSION["amzOrderReference"]);
		                xtc_redirect(xtc_href_link('shopping_cart.php', 'info_message='.urlencode(AMZ_FASTAUTH_HARD_DECLINED), 'SSL'));
		                die;
		            }

		        }else{
		                $address = $details->getAuthorizationBillingAddress();
		                $addressArr = AlkimAmazonHandler::getAddressArrayFromAmzAddress($address);
                   
                   
                   

                        $q = "SELECT * FROM ".TABLE_ADDRESS_BOOK." WHERE
                            customers_id = ".(int)$_SESSION['customer_id']."
                                AND
                            entry_firstname = '".xtc_db_input($addressArr["firstname"])."'
                                AND
                            entry_lastname = '".xtc_db_input($addressArr["lastname"])."'
                                AND
                            entry_street_address = '".xtc_db_input($addressArr["street_address"])."'
                                AND
                            entry_postcode = '".xtc_db_input($addressArr["postcode"])."'
                                AND
                            entry_city = '".xtc_db_input($addressArr["city"])."'
                                AND
                            entry_country_id = '".xtc_db_input($addressArr["country"]["id"])."'";
                            $rs = xtc_db_query($q);
                            
                            $address_book_sql_array = array(
                                    'customers_id' => (int)$_SESSION['customer_id'],
                                    'entry_firstname' => $addressArr["firstname"],
                                    'entry_lastname' => $addressArr["lastname"],
                                    'entry_company' => $addressArr["company"],
                                    'entry_street_address' =>$addressArr["street_address"],
                                    'entry_postcode' => $addressArr["postcode"],
                                    'entry_city' => $addressArr["city"],
                                    'entry_country_id' => $addressArr["country"]["id"]
                            );
                       
                       
                       
                       
                       
                       
                       
                        if(xtc_db_num_rows($rs) > 0){
                            $r = xtc_db_fetch_array($rs);
                            $_SESSION["billto"] = $r["address_book_id"];
                        }else{


                            xtc_db_perform(TABLE_ADDRESS_BOOK, $address_book_sql_array);
                            $customer_address_id = xtc_db_insert_id();
                            $_SESSION["billto"] = $customer_address_id;

                        }

                        $q = "SELECT * FROM customers WHERE customers_id = ".(int)$_SESSION['customer_id'];
                        $rs = xtc_db_query($q);
                        $r = xtc_db_fetch_array($rs);

                        if($r["customers_firstname"] == '' && $r["customers_lastname"] == ''){
                           $q = "UPDATE customers
                                        SET
                                            customers_firstname = '".xtc_db_input($addressArr["firstname"])."',
                                            customers_lastname = '".xtc_db_input($addressArr["lastname"])."'
                                        WHERE
                                            customers_id = ".(int)$_SESSION['customer_id'];
                            xtc_db_query($q);
                        }
                        $q = "SELECT * FROM address_book WHERE customers_id = ".(int)$_SESSION['customer_id']." AND address_book_id = ".(int)$r["customers_default_address_id"];
                        $rs = xtc_db_query($q);
                        $r = xtc_db_fetch_array($rs);
                        if($r["entry_firstname"] == '' && $r["entry_lastname"] == ''){
                           xtc_db_perform(TABLE_ADDRESS_BOOK, $address_book_sql_array, 'update', " address_book_id = ".(int)$r["address_book_id"]);
                        }

                        global $order;
                        $order = new order;


		            }

		    }else{

		        xtc_redirect(xtc_href_link('shopping_cart.php', 'info_message='.urlencode(AMZ_UNKNOWN_ERROR), 'SSL'));
		        die;
		    }


		    //TODO:redirect on error
		    if(AMZ_CAPTURE_MODE == 'after_auth'){
                AlkimAmazonTransactions::capture($response->getAuthorizeResult()->getAuthorizationDetails()->getAmazonAuthorizationId(), $_SESSION["amzOrderTotal"]);
		    }
		}


	}

	function after_process() {
        global $insert_id;
        if(isset($_SESSION["amzOrderReference"])){
            $q = "UPDATE ".TABLE_ORDERS." SET amazon_order_id = '".xtc_db_input($_SESSION["amzOrderReference"])."' WHERE orders_id = ".(int)$insert_id;
            xtc_db_query($q);

        }
        if(AMZ_AUTHORIZATION_MODE == 'after_checkout'){
		    AlkimAmazonTransactions::authorize($_SESSION["amzOrderReference"], $_SESSION["amzOrderTotal"],10);
		}
		if(is_array($_SESSION["amzSetStatusAuthorized"])){
		    foreach($_SESSION["amzSetStatusAuthorized"] AS $orderRef){
		        AlkimAmazonTransactions::setOrderStatusAuthorized($orderRef);
		    }
		}
		if(is_array($_SESSION["amzSetStatusCaptured"])){
		    foreach($_SESSION["amzSetStatusCaptured"] AS $orderRef){
		        AlkimAmazonTransactions::setOrderStatusCaptured($orderRef);
		    }
		}





        unset($_SESSION["amzOrderReference"]);
        //unset($_SESSION["amz_access_token"]);
        unset($_SESSION["amazon_id"]);
	}

	function get_error() {
		return false;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_AM_APA_STATUS'");
			$this->_check = min(1, xtc_db_num_rows($check_query));
		}
		return $this->_check;
	}

	function install() {

        xtc_db_query("
CREATE TABLE IF NOT EXISTS `amz_transactions` (
  `amz_tx_id` int(11) NOT NULL AUTO_INCREMENT,
  `amz_tx_order_reference` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `amz_tx_type` varchar(16) NOT NULL,
  `amz_tx_time` int(11) NOT NULL,
  `amz_tx_expiration` int(11) NOT NULL,
  `amz_tx_amount` float NOT NULL,
  `amz_tx_amount_refunded` float NOT NULL,
  `amz_tx_status` varchar(32) NOT NULL,
  `amz_tx_reference` varchar(64) NOT NULL,
  `amz_tx_amz_id` varchar(255) NOT NULL,
  `amz_tx_last_change` int(11) NOT NULL,
  `amz_tx_last_update` int(11) NOT NULL,
  `amz_tx_order` int(11) NOT NULL,
  `amz_tx_customer_informed` tinyint(1) NOT NULL,
  PRIMARY KEY (`amz_tx_id`),
  UNIQUE KEY `amz_tx_amz_id` (`amz_tx_amz_id`),
  KEY `amz_tx_order_reference` (`amz_tx_order_reference`),
  KEY `amz_tx_type` (`amz_tx_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");
xtc_db_query("
CREATE TABLE IF NOT EXISTS `amz_products_excluded` (
  `amz_products_excluded_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL,
  PRIMARY KEY (`amz_products_excluded_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");
		$result = xtc_db_query("show columns from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." like 'products_attributes_id'");
		if (xtc_db_num_rows($result) == 0) {
			xtc_db_query("ALTER TABLE `orders_products_attributes` ADD `products_attributes_id` INT( 11 ) NOT NULL ;");
		}
		$result = xtc_db_query("show columns from ".TABLE_ORDERS_PRODUCTS." like 'AmazonOrderItemCode'");
		if (xtc_db_num_rows($result) == 0) {
			xtc_db_query("ALTER TABLE `orders_products` ADD `AmazonOrderItemCode` VARCHAR( 255 ) NOT NULL ;");
		}
		$result = xtc_db_query("show columns from ".TABLE_ORDERS_PRODUCTS." like 'AmazonShippingCosts'");
		if (xtc_db_num_rows($result) == 0) {
			xtc_db_query("ALTER TABLE `orders_products` ADD `AmazonShippingCosts` DECIMAL( 10, 2 ) NOT NULL ;");
		}
		$result = xtc_db_query("show columns from ".TABLE_ORDERS_PRODUCTS." like 'AmazonCoupon'");
		if (xtc_db_num_rows($result) == 0) {
			xtc_db_query("ALTER TABLE `orders_products` ADD `AmazonCoupon` DECIMAL( 10, 2 ) NOT NULL ;");
		}
		xtc_db_query("ALTER TABLE `". TABLE_ORDERS_STATUS."` CHANGE `orders_status_name` `orders_status_name` VARCHAR( 64 )");


		if (xtc_db_num_rows(xtc_db_query("SELECT orders_status_id FROM ". TABLE_ORDERS_STATUS ." WHERE orders_status_id = '78' LIMIT 1")) == 0) {
			xtc_db_query("INSERT INTO ". TABLE_ORDERS_STATUS." (orders_status_id, language_id, orders_status_name) VALUES ('78','1','Amazon Payments - authorization successful')");
			xtc_db_query("INSERT INTO ". TABLE_ORDERS_STATUS." (orders_status_id, language_id, orders_status_name) VALUES ('78','2','Amazon Payments - autorisiert')");
		}
		
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AM_APA_STATUS', 'False',  '".  $this->group_id ."', '1', '', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ALLOWED', 'DE',  '".  $this->group_id ."', '2', now())");

		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_MERCHANTID', '',  '".  $this->group_id ."', '3', now())");

		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ACCESKEY', '',  '".  $this->group_id ."', '4', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_SECRETKEY', '',  '".  $this->group_id ."', '5', now())");

		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_MODE', 'sandbox',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_AUTHORIZATION_MODE', 'after_checkout',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_CAPTURE_MODE', 'after_shipping',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_SHIPPED_STATUS', '3',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_AGB_ID', '3',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_REVOCATION_ID', '9',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_BUTTON_SIZE', 'medium',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_BUTTON_COLOR', 'Orange',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_IPN_STATUS', 'True',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_CRON_STATUS', 'True',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_AGB_DISPLAY_MODE', 'Short',  '".  $this->group_id ."', '7', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_CRON_PW', '".$this->randomString()."',  '".  $this->group_id ."', '7', now())");

xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_PAYMENT_NAME', '',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_PAYMENT_EMAIL', 'test@testshop.de',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_SOFT_DECLINE_SUBJECT_GERMAN', 'Ihre Zahlung wurde von Amazon abgelehnt',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_SOFT_DECLINE_SUBJECT_ENGLISH', 'Your Payment has been declined by Amazon',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_HARD_DECLINE_SUBJECT_GERMAN', 'Ihre Zahlung wurde von Amazon abgelehnt - bitte kontaktieren Sie uns',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_HARD_DECLINE_SUBJECT_ENGLISH', 'Your Payment has been declined by Amazon - please contact us',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_SEND_MAILS_ON_DECLINE', 'True',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_SHOW_ON_CHECKOUT_PAYMENT', 'True',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_DEBUG_MODE', 'True',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_EXCLUDED_SHIPPING', '',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_VERSION', '2.00',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_TEMPLATE', '1',  '".  $this->group_id ."', '7', now())");
xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('AMZ_DOWNLOAD_ONLY', 'False',  '".  $this->group_id ."', '7', now())");

		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ORDER_STATUS_NOTIFIED', '77',  '".  $this->group_id ."', '20', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK', '78',  '".  $this->group_id ."', '21', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ORDER_STATUS_PAYED', '79',  '".  $this->group_id ."', '22', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ORDER_STATUS_SHIPPED', '80',  '".  $this->group_id ."', '23', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_ORDER_STATUS_STORNO', '81',  '".  $this->group_id ."', '24', now())");

		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_SENDMAILS', 'False',  '".  $this->group_id ."', '10', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_AM_APA_MAIL', '',  '".  $this->group_id ."', '11', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AM_APA_SENDUSERMAIL', 'False',  '".  $this->group_id ."', '40', '', now())");
        xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AM_APA_ALLOW_GUESTS', 'True',  '".  $this->group_id ."', '41', '', now())");
		xtc_db_query("insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_AM_APA_SHOW_ORDERID', 'True',  '".  $this->group_id ."', '42', '', now())");

    xtc_db_query("INSERT INTO `configuration_group` (`configuration_group_id`, `configuration_group_title`, `configuration_group_description`, `sort_order`, `visible`) VALUES (".  $this->group_id .", 'Amazon Payments Advanced', '', NULL, 1)");



		$result = xtc_db_query("show columns from ".TABLE_ORDERS." like 'amazon_order_id'");
		if (xtc_db_num_rows($result) == 0) {
			xtc_db_query("ALTER TABLE `". TABLE_ORDERS."` ADD `amazon_order_id` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL");
	    }
		$result2 = xtc_db_query("show columns from ".TABLE_ORDERS." like 'amazon_contract_id'");
		if (xtc_db_num_rows($result2) == 0) {
			xtc_db_query("ALTER TABLE `". TABLE_ORDERS."` ADD `amazon_contract_id` VARCHAR( 255 )");
		}

		$result3 = xtc_db_query("show columns from ".TABLE_ADMIN_ACCESS." like 'amz_configuration'");
		if (xtc_db_num_rows($result3) == 0) {
			xtc_db_query("ALTER TABLE `". TABLE_ADMIN_ACCESS."` ADD `amz_configuration` TINYINT(1) DEFAULT 1");
		}
		
		$this->updateDB();

	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where  configuration_group_id = '". $this->group_id."'");
        xtc_db_query("delete from `configuration_group` where configuration_group_id = '". $this->group_id."'");

	}
	
	function updateDB(){
	    
	    # Update V2.01
	    $this->createConfigValue('AMZ_IPN_PW', $this->randomString());
	    $this->createConfigValue('AMZ_SET_SELLER_ORDER_ID', 'False');
	    $result = xtc_db_query("show columns from amz_transactions like 'amz_tx_merchant_id'");
		if (xtc_db_num_rows($result) == 0) {
			xtc_db_query("ALTER TABLE `amz_transactions` ADD `amz_tx_merchant_id` VARCHAR( 64 ) NOT NULL ;");
			xtc_db_query("UPDATE amz_transactions SET amz_tx_merchant_id = '".xtc_db_input(AWS_MERCHANT_ID)."' WHERE amz_tx_merchant_id = ''");
		}
		$this->createConfigValue('AMZ_STATUS_NONAUTHORIZED', '1');


        
        # Update V2.10
        $result4 = xtc_db_query("show columns from ".TABLE_CUSTOMERS." like 'amazon_customer_id'");
		if (xtc_db_num_rows($result4) == 0) {
			xtc_db_query("ALTER TABLE `". TABLE_CUSTOMERS."` ADD `amazon_customer_id` VARCHAR( 255 )");
		}
        $this->createConfigValue('ADD MODULE_PAYMENT_AM_APA_POPUP', 'True');
        $this->createConfigValue('MODULE_PAYMENT_AM_APA_LPA_MODE', 'login_pay');
        $this->createConfigValue('AMZ_BUTTON_SIZE_LPA', 'medium');
        $this->createConfigValue('AMZ_BUTTON_COLOR_LPA', 'Gold');
        $this->createConfigValue('AMZ_BUTTON_TYPE_LOGIN', 'LwA');
        $this->createConfigValue('AMZ_BUTTON_TYPE_PAY', 'PwA');
        $this->createConfigValue('AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE', '1');
        xtc_db_query("ALTER TABLE `". TABLE_ORDERS."` CHANGE `amazon_order_id` `amazon_order_id` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL");



		$q = "UPDATE 
	            ".TABLE_CONFIGURATION." 
	          SET 
	            configuration_value = '".xtc_db_input(AMZ_FILE_VERSION)."' 
	          WHERE 
	            configuration_key = 'AMZ_VERSION'";
	    xtc_db_query($q);        
	}
	
	
	function createConfigValue($key, $value){
	    $q = "SELECT * FROM ".TABLE_CONFIGURATION." WHERE configuration_key = '".xtc_db_input($key)."'";
	    $rs = xtc_db_query($q);
	    if(xtc_db_num_rows($rs)==0){
            $q = "INSERT INTO
                         ".TABLE_CONFIGURATION." 
                  SET 
                    configuration_key = '".xtc_db_input($key)."',
                    configuration_value = '".xtc_db_input($value)."', 
                    configuration_group_id = '".  $this->group_id ."',
                    date_added = now()";	    
            xtc_db_query($q);
	    }
	    


	}


    function randomString($length=10) {
        $chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n','N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v','V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

        $max_chars = count($chars) - 1;
        srand( (double) microtime()*1000000);

        $rand_str = '';
        for($i=0;$i<$length;$i++) {
         $rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
        }
         return $rand_str;
  }

	function keys() {
		return array ('MODULE_PAYMENT_AM_APA_STATUS', 0);
	}
}
?>

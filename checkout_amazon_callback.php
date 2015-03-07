<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   checkout_amazon_callback.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
?><?php

// HTTP RAW POSTDAT holen
$response = file_get_contents("php://input");


/**
 * Clean comments of json content and decode it with json_decode().
 * Work like the original php json_decode() function with the same params
 *
 * @param   string  $json    The json string being decoded
 * @param   bool    $assoc   When TRUE, returned objects will be converted into associative arrays.
 * @param   integer $depth   User specified recursion depth. (>=5.3)
 * @param   integer $options Bitmask of JSON decode options. (>=5.4)
 * @return  string
 */
function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {

    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

    if(version_compare(phpversion(), '5.4.0', '>=')) {
        $json = json_decode($json, $assoc, $depth, $options);
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) {
        $json = json_decode($json, $assoc, $depth);
    }
    else {
        $json = json_decode($json, $assoc);
    }

    return $json;
}
include ('includes/application_top.php');
include_once('AmazonAdvancedPayment/.config.inc.php');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonTransactions.class.php');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');


$response = json_clean_decode($response);
$message = json_clean_decode($response->Message);
$responseXml =  simplexml_load_string($message->NotificationData);
$response_xml = $responseXml;

AlkimAmazonHandler::log('ipn', array('summary'=>$message->NotificationType) , '', print_r($response, true));

if(MODULE_PAYMENT_AM_APA_IPN_STATUS == 'True' && MODULE_PAYMENT_AM_APA_STATUS == 'True' && $_GET["pw"] == AMZ_IPN_PW){
        ob_start();
        switch ($message->NotificationType ) {

                case 'PaymentAuthorize':

                            $q = "SELECT * FROM amz_transactions WHERE amz_tx_type = 'auth' AND amz_tx_amz_id = '".xtc_db_input($responseXml->AuthorizationDetails->AmazonAuthorizationId)."'";
                            $rs = xtc_db_query($q);
                            if(xtc_db_num_rows($rs) == 0){
                                die;
                            }
                            $r = xtc_db_fetch_array($rs);
                            $sqlArr = array(
                                                'amz_tx_status' => (string)$responseXml->AuthorizationDetails->AuthorizationStatus->State,
                                                'amz_tx_last_change' => time(),
                                                'amz_tx_expiration' => strtotime($responseXml->AuthorizationDetails->ExpirationTimestamp),
                                                'amz_tx_last_update'=>time()
                                            );
                            xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_id = ".(int)$r["amz_tx_id"]);
                            AlkimAmazonTransactions::refreshAuthorization($responseXml->AuthorizationDetails->AmazonAuthorizationId);
                            if($sqlArr["amz_tx_status"] == 'Open'){
                                AlkimAmazonTransactions::setOrderStatusAuthorized($r["amz_tx_order_reference"]);
                                if(AlkimAmazonHandler::isAutomaticAuth($responseXml->AuthorizationDetails->AmazonAuthorizationId) && AMZ_CAPTURE_MODE == 'after_auth'){
		                            AlkimAmazonTransactions::capture($responseXml->AuthorizationDetails->AmazonAuthorizationId,$r["amz_tx_amount"]);
		                        }

		                    }elseif($sqlArr["amz_tx_status"] == 'Declined'){
		                        $reason = (string)$responseXml->AuthorizationDetails->AuthorizationStatus->ReasonCode;
		                        if($reason == 'AmazonRejected'){
		                            $orderRef = AlkimAmazonTransactions::getOrderRefFromAmzId($responseXml->AuthorizationDetails->AmazonAuthorizationId);
		                            AlkimAmazonTransactions::cancelOrder($orderRef);
		                        }
		                        AlkimAmazonHandler::intelligentDeclinedMail($responseXml->AuthorizationDetails->AmazonAuthorizationId, $reason);

		                    }

                break;
         case 'PaymentCapture':

                            $q = "SELECT * FROM amz_transactions WHERE amz_tx_type = 'capture' AND amz_tx_amz_id = '".xtc_db_input($responseXml->CaptureDetails->AmazonCaptureId)."'";
                            $rs = xtc_db_query($q);
                            if(xtc_db_num_rows($rs) == 0){
                                die;
                            }
                            $r = xtc_db_fetch_array($rs);
                            $sqlArr = array(
                                                'amz_tx_status' => (string)$responseXml->CaptureDetails->CaptureStatus->State,
                                                'amz_tx_last_change' => time(),
                                                'amz_tx_amount_refunded' =>(float)$responseXml->CaptureDetails->RefundedAmount->Amount,
                                                'amz_tx_last_update'=>time()
                                            );
                            xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_id = ".(int)$r["amz_tx_id"]);
                            
                            if(AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE == 'True' && $sqlArr['amz_tx_status'] == 'Completed'){
                                $orderTotal = AlkimAmazonTransactions::getOrderRefTotal($r["amz_tx_order_reference"]);
                                if($r["amz_tx_amount"] == $orderTotal){
                                    AlkimAmazonTransactions::closeOrder($r["amz_tx_order_reference"]);
                                }
                            }
                break;

         case 'PaymentRefund':

                            $q = "SELECT * FROM amz_transactions WHERE amz_tx_type = 'refund' AND amz_tx_amz_id = '".xtc_db_input($responseXml->RefundDetails->AmazonRefundId)."'";
                            $rs = xtc_db_query($q);
                            if(xtc_db_num_rows($rs) == 0){
                                die;
                            }
                            $r = xtc_db_fetch_array($rs);
                            $sqlArr = array(
                                                'amz_tx_status' => (string)$responseXml->RefundDetails->RefundStatus->State,
                                                'amz_tx_last_change' => time(),
                                                'amz_tx_last_update'=>time()
                                            );
                            xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_id = ".(int)$r["amz_tx_id"]);


                break;
         case 'OrderReferenceNotification':

                            $q = "SELECT * FROM amz_transactions WHERE amz_tx_type = 'order_ref' AND amz_tx_amz_id = '".xtc_db_input($responseXml->OrderReference->AmazonOrderReferenceId)."'";
                            $rs = xtc_db_query($q);
                            if(xtc_db_num_rows($rs) == 0){
                                die;
                            }
                            $r = xtc_db_fetch_array($rs);
                            $sqlArr = array(
                                                'amz_tx_status' => (string)$responseXml->OrderReference->OrderReferenceStatus->State,
                                                'amz_tx_last_change' => time(),
                                                'amz_tx_last_update'=>time()
                                            );
                            xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_id = ".(int)$r["amz_tx_id"]);


                break;


        }
        if(AMZ_CAPTURE_MODE=='after_shipping'){
        AlkimAmazonHandler::shippingCapture();
    }
    $str = ob_get_contents() ;
    ob_end_clean();
    echo "OK";

}



<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   AlkimAmazonTransactions.class.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
?><?php

class AlkimAmazonTransactions{




    public static function authorize($orderRef, $amount, $timeout=1440, $comment=''){
            $service = AlkimAmazonHandler::getService();
            $authorizeRequest = new OffAmazonPaymentsService_Model_AuthorizeRequest();
            $authorizeRequest->setAmazonOrderReferenceId($orderRef);
            $authorizeRequest->setSellerId(AWS_MERCHANT_ID);
            $authorizeRequest->setTransactionTimeout($timeout);
            $authorizeRequest->setSoftDescriptor($comment);
           if(MODULE_PAYMENT_AM_APA_PROVOCATION == 'hard_decline' && MODULE_PAYMENT_AM_APA_MODE == 'sandbox'){
            $authorizeRequest->setSellerAuthorizationNote('{"SandboxSimulation": {"State":"Declined", "ReasonCode":"AmazonRejected"}}');
           }
           if(MODULE_PAYMENT_AM_APA_PROVOCATION == 'soft_decline' && MODULE_PAYMENT_AM_APA_MODE == 'sandbox'){
             $authorizeRequest->setSellerAuthorizationNote('{"SandboxSimulation": {"State":"Declined", "ReasonCode":"InvalidPaymentMethod", "PaymentMethodUpdateTimeInMins":2}}');
           }

            $authorizeRequest->setAuthorizationReferenceId(self::getNextAuthRef($orderRef));
            $authorizeRequest->setAuthorizationAmount(new OffAmazonPaymentsService_Model_Price());
            $authorizeRequest->getAuthorizationAmount()->setAmount($amount);
            $authorizeRequest->getAuthorizationAmount()->setCurrencyCode('EUR');
            $authId = '';
            try {
                $response = $service->authorize($authorizeRequest);
                $details = $response->getAuthorizeResult()->getAuthorizationDetails();


                $sqlArr = array(
                                    'amz_tx_order_reference'    =>  $orderRef,
                                    'amz_tx_type'   => 'auth',
                                    'amz_tx_time' => time(),
                                    'amz_tx_expiration'=>strtotime($details->getExpirationTimestamp()),
                                    'amz_tx_amount'=>$amount,
                                    'amz_tx_status'=>$details->getAuthorizationStatus()->getState(),
                                    'amz_tx_reference'=>$details->getAuthorizationReferenceId(),
                                    'amz_tx_amz_id'=>$details->getAmazonAuthorizationId(),
                                    'amz_tx_last_change'=>time(),
                                    'amz_tx_last_update'=>time(),
                                    'amz_tx_merchant_id'=>AWS_MERCHANT_ID


                                );
              $authId = $details->getAmazonAuthorizationId();           
              xtc_db_perform('amz_transactions', $sqlArr);

            }
            catch (OffAmazonPaymentsService_Exception $e) {
                echo 'ERROR: '.$e->getMessage();
                AlkimAmazonHandler::log('exception', array('orderRef'=>$orderRef, 'txnId'=> $authId, 'summary'=>'Authorization exception') , print_r($authorizeRequest, true), print_r($e, true));
            }
            
            AlkimAmazonHandler::log('api', array('orderRef'=>$orderRef, 'txnId'=> $authId, 'summary'=>'Authorization') , print_r($authorizeRequest, true), print_r($response, true));
            return $response;
    }


     public static function capture($authId, $amount){


            if($authId){
                $orderRef = self::getOrderRefFromAmzId($authId);
                $service = AlkimAmazonHandler::getService();
                $captureRequest = new OffAmazonPaymentsService_Model_CaptureRequest();
                $captureRequest->setAmazonAuthorizationId($authId);
                $captureRequest->setSellerId(AWS_MERCHANT_ID);
                $captureRequest->setCaptureReferenceId(self::getNextCaptureRef($orderRef));
                $captureRequest->setCaptureAmount(new OffAmazonPaymentsService_Model_Price());
                $captureRequest->getCaptureAmount()->setAmount($amount);
                $captureRequest->getCaptureAmount()->setCurrencyCode('EUR');
                if(MODULE_PAYMENT_AM_APA_PROVOCATION == 'capture_decline' && MODULE_PAYMENT_AM_APA_MODE == 'sandbox'){
                    $captureRequest->setSellerCaptureNote('{"SandboxSimulation":{"State":"Declined", "ReasonCode":"AmazonRejected"}}');
                }
                $captureId = '';
                try {
                    $response = $service->capture($captureRequest);

                    $details = $response->getCaptureResult()->getCaptureDetails();
                    $sqlArr = array(
                                        'amz_tx_order_reference'    =>  $orderRef,
                                        'amz_tx_type'   => 'capture',
                                        'amz_tx_time' => time(),
                                        'amz_tx_expiration'=>0,
                                        'amz_tx_amount'=>$amount,
                                        'amz_tx_status'=>$details->getCaptureStatus()->getState(),
                                        'amz_tx_reference'=>$details->getCaptureReferenceId(),
                                        'amz_tx_amz_id'=>$details->getAmazonCaptureId(),
                                        'amz_tx_last_change'=>time(),
                                        'amz_tx_last_update'=>time(),
                                        'amz_tx_merchant_id'=>AWS_MERCHANT_ID


                                    );
                  $captureId = $details->getAmazonCaptureId();          
                  $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($details->getAmazonCaptureId())."'";
                  $rs = xtc_db_query($q);
                  if(xtc_db_num_rows($rs)==0){
                    xtc_db_perform('amz_transactions', $sqlArr);
                  }

                }
                catch (OffAmazonPaymentsService_Exception $e) {
                    echo 'ERROR: '.$e->getMessage();
                    AlkimAmazonHandler::log('exception', array('orderRef'=>$orderRef, 'txnId'=> $captureId, 'summary'=>'Capture exception') , print_r($captureRequest, true), print_r($e, true));
                }
                AlkimAmazonHandler::log('api', array('orderRef'=>$orderRef, 'txnId'=> $captureId, 'summary'=>'Capture') , print_r($captureRequest, true), print_r($response, true));
                return $response;
            }else{
                //TODO: No auth for capture
            }
    }

    public static function refund($captureId, $amount){

        $orderRef = self::getOrderRefFromAmzId($captureId);
        $service = AlkimAmazonHandler::getService();
    	$refund = new OffAmazonPaymentsService_Model_Price();
	    $refund->setCurrencyCode('EUR');
	    $refund->setAmount($amount);

	    $refundRequest = new OffAmazonPaymentsService_Model_RefundRequest();
	    $refundRequest->setSellerId(AWS_MERCHANT_ID);
	    $refundRequest->setAmazonCaptureId($captureId);
        $refundRequest->setRefundReferenceId(self::getNextRefundRef($orderRef));
	    $refundRequest->setRefundAmount($refund);
	    $refundId = '';
        try{
            $response = $service->refund($refundRequest);
            $details = $response->getRefundResult()->getRefundDetails();
            $sqlArr = array(
                                        'amz_tx_order_reference'    =>  $orderRef,
                                        'amz_tx_type'   => 'refund',
                                        'amz_tx_time' => time(),
                                        'amz_tx_expiration'=>0,
                                        'amz_tx_amount'=>$amount,
                                        'amz_tx_status'=>$details->getRefundStatus()->getState(),
                                        'amz_tx_reference'=>$details->getRefundReferenceId(),
                                        'amz_tx_amz_id'=>$details->getAmazonRefundId(),
                                        'amz_tx_last_change'=>time(),
                                        'amz_tx_last_update'=>time(),
                                        'amz_tx_merchant_id'=>AWS_MERCHANT_ID


                                    );
             $refundId = $details->getAmazonRefundId();
                  xtc_db_perform('amz_transactions', $sqlArr);
        }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
            AlkimAmazonHandler::log('exception', array('orderRef'=>$orderRef, 'txnId'=> $refundId, 'summary'=>'Refund exception') , print_r($refundRequest, true), print_r($e, true));
        }
        AlkimAmazonHandler::log('api', array('orderRef'=>$orderRef, 'txnId'=> $refundId, 'summary'=>'Refund') , print_r($refundRequest, true), print_r($response, true));
	    return $response;

    }
    public static function refreshOrderReference($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($orderRef)."'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        $oldStatus = (string)$r["amz_tx_status"];
        $service = AlkimAmazonHandler::getService();
        $orderRefRequest = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest();
	    $orderRefRequest->setSellerId(AWS_MERCHANT_ID);
	    $orderRefRequest->setAmazonOrderReferenceId($orderRef);
	     try{
            $response = $service->getOrderReferenceDetails($orderRefRequest);
            if(is_object($response)){
                $details = $response->getGetOrderReferenceDetailsResult()->getOrderReferenceDetails();
                $sqlArr = array(
                                            'amz_tx_status' => (string)$details->getOrderReferenceStatus()->getState(),
                                            'amz_tx_last_change' =>strtotime((string)$details->getOrderReferenceStatus()->getLastUpdateTimestamp()),
                                            'amz_tx_last_update'=>time()
                                        );
                 xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_amz_id = '".xtc_db_input($orderRef)."'");
                 if($oldStatus == 'Suspended' && $sqlArr["amz_tx_status"] == 'Open'){
                    self::reAuthByOrderRef($orderRef);
                 }
            }

        }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
        }
    }

    public static function closeOrder($orderRef){
        $service = AlkimAmazonHandler::getService();
        $orderRefRequest = new OffAmazonPaymentsService_Model_CloseOrderReferenceRequest();
	    $orderRefRequest->setSellerId(AWS_MERCHANT_ID);
	    $orderRefRequest->setAmazonOrderReferenceId($orderRef);
	     try{
            $response = $service->closeOrderReference($orderRefRequest);
         }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
            AlkimAmazonHandler::log('exception', array('orderRef'=>$orderRef, 'summary'=>'closeOrder exception') , print_r($orderRefRequest, true), print_r($e, true));
        }
        AlkimAmazonHandler::log('api', array('orderRef'=>$orderRef, 'summary'=>'closeOrder') , print_r($orderRefRequest, true), print_r($response, true));
    }
    public static function cancelOrder($orderRef){
        $service = AlkimAmazonHandler::getService();
        $orderRefRequest = new OffAmazonPaymentsService_Model_CancelOrderReferenceRequest();
	    $orderRefRequest->setSellerId(AWS_MERCHANT_ID);
	    $orderRefRequest->setAmazonOrderReferenceId($orderRef);
	     try{
            $response = $service->cancelOrderReference($orderRefRequest);
         }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
            AlkimAmazonHandler::log('exception', array('orderRef'=>$orderRef, 'summary'=>'cancelOrder exception') , print_r($orderRefRequest, true), print_r($e, true));
        }
        AlkimAmazonHandler::log('api', array('orderRef'=>$orderRef, 'summary'=>'cancelOrder') , print_r($orderRefRequest, true), print_r($response, true));
    }

    public static function refreshRefund($refundId){
        $service = AlkimAmazonHandler::getService();
        $refundRequest = new OffAmazonPaymentsService_Model_GetRefundDetailsRequest();
	    $refundRequest->setSellerId(AWS_MERCHANT_ID);
	    $refundRequest->setAmazonRefundId($refundId);
	     try{
            $response = $service->getRefundDetails($refundRequest);
            $details = $response->getGetRefundDetailsResult()->getRefundDetails();
             $sqlArr = array(
                                        'amz_tx_status' => (string)$details->getRefundStatus()->getState(),
                                        'amz_tx_last_change' =>strtotime((string)$details->getRefundStatus()->getLastUpdateTimestamp()),
                                        'amz_tx_last_update'=>time()
                                    );
             xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_amz_id = '".xtc_db_input($refundId)."'");

        }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
        }
    }

     public static function refreshCapture($captureId){
        $service = AlkimAmazonHandler::getService();
        $captureRequest = new OffAmazonPaymentsService_Model_GetCaptureDetailsRequest();
	    $captureRequest->setSellerId(AWS_MERCHANT_ID);
	    $captureRequest->setAmazonCaptureId($captureId);
	     try{
            $response = $service->getCaptureDetails($captureRequest);
            $details = $response->getGetCaptureDetailsResult()->getCaptureDetails();

             $sqlArr = array(
                                        'amz_tx_status' => (string)$details->getCaptureStatus()->getState(),
                                        'amz_tx_last_change' =>strtotime((string)$details->getCaptureStatus()->getLastUpdateTimestamp()),
                                        'amz_tx_amount_refunded' =>(float)$details->getRefundedAmount()->getAmount(),
                                        'amz_tx_last_update'=>time()
                                    );

            xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_amz_id = '".xtc_db_input($captureId)."'");
            
            if(AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE == 'True' && $sqlArr['amz_tx_status'] == 'Completed'){
                   $q = "SELECT * FROM amz_transactions WHERE amz_tx_type = 'capture' AND amz_tx_amz_id = '".xtc_db_input($captureId)."'";
                   $rs = xtc_db_query($q);
                   if(xtc_db_num_rows($rs) == 1){
                       $r = xtc_db_fetch_array($rs);
                       $orderTotal = AlkimAmazonTransactions::getOrderRefTotal($r["amz_tx_order_reference"]);
                       if($r["amz_tx_amount"] == $orderTotal){
                            self::closeOrder($r["amz_tx_order_reference"]);
                       }
                   }
                           
            }

        }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
        }
    }

    public static function refreshAuthorization($authId){
        $service = AlkimAmazonHandler::getService();
        $authorizationRequest = new OffAmazonPaymentsService_Model_GetAuthorizationDetailsRequest();
	    $authorizationRequest->setSellerId(AWS_MERCHANT_ID);
	    $authorizationRequest->setAmazonAuthorizationId($authId);
	     try{
             $response = $service->getAuthorizationDetails($authorizationRequest);
             $details = $response->getGetAuthorizationDetailsResult()->getAuthorizationDetails();
                $address = $details->getAuthorizationBillingAddress();
                if(is_object($address)){
                        $addressArr = AlkimAmazonHandler::getAddressArrayFromAmzAddress($address);
                        $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($authId)."'";
                        $txRs = xtc_db_query($q);
                        $txR = xtc_db_fetch_array($txRs);

                        $sqlArr = array(
                                            'billing_name'=>$addressArr["name"],
                                            'billing_firstname'=>$addressArr["firstname"],
                                            'billing_lastname'=>$addressArr["lastname"],
                                            'billing_company'=>$addressArr["company"],
                                            'billing_suburb'=>$addressArr["suburb"],
                                            'billing_street_address'=>$addressArr["street_address"],
                                            'billing_postcode'=>$addressArr["postcode"],
                                            'billing_city'=>$addressArr["city"],
                                            'billing_country'=>$addressArr["country"]["title"],
                                            'billing_address_format_id'=>5,
                                            'billing_country_iso_code_2'=>$addressArr["country"]["iso_code_2"]
                                        );

                        xtc_db_perform(TABLE_ORDERS, $sqlArr, 'update', " amazon_order_id = '".xtc_db_input($txR["amz_tx_order_reference"])."'");
                }
             $sqlArr = array(
                                        'amz_tx_status' => (string)$details->getAuthorizationStatus()->getState(),
                                        'amz_tx_last_change' =>strtotime((string)$details->getAuthorizationStatus()->getLastUpdateTimestamp()),
                                        'amz_tx_last_update'=>time()
                                    );

            xtc_db_perform('amz_transactions', $sqlArr, 'update', " amz_tx_amz_id = '".xtc_db_input($authId)."'");
            
            if((string)$details->getAuthorizationStatus()->getState() == 'Open' && AMZ_CAPTURE_MODE == 'after_auth'){
                if(AlkimAmazonHandler::isAutomaticAuth($authId)){
                    AlkimAmazonTransactions::capture($authId, AlkimAmazonHandler::getAuthAmount($authId));
                }
            }
            
            if((string)$details->getAuthorizationStatus()->getState() == 'Declined'){
                $reason = (string)$details->getAuthorizationStatus()->getReasonCode();

		        if($reason == 'AmazonRejected'){
		             $orderRef = AlkimAmazonTransactions::getOrderRefFromAmzId($authId);
		             AlkimAmazonTransactions::cancelOrder($orderRef);
		        }
                AlkimAmazonHandler::intelligentDeclinedMail($authId, $reason);
            }

        }catch (OffAmazonPaymentsService_Exception $e) {
            echo 'ERROR: '.$e->getMessage();
        }
    }

    public static function intelligentRefresh($r){
                switch($r["amz_tx_type"]){
                    case 'refund':
                        self::refreshRefund($r["amz_tx_amz_id"]);
                        break;
                    case 'capture':
                        self::refreshCapture($r["amz_tx_amz_id"]);
                        break;
                   case 'auth':
                        self::refreshAuthorization($r["amz_tx_amz_id"]);
                        break;
                    case 'order_ref':
                        self::refreshOrderReference($r["amz_tx_amz_id"]);
                        break;

                }
    }

    public static function getAuthorizationForCapture($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_status = 'Open' AND amz_tx_type = 'auth' AND amz_tx_order_reference = '".xtc_db_input($orderRef)."'";
        $rs = xtc_db_query($q);
        if($r = xtc_db_fetch_array($rs)){
            return $r;
        }
    }

    public static function captureTotalFromAuth($authId){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_type='auth' AND amz_tx_amz_id = '".xtc_db_input($authId)."'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        if($r){
            return self::capture($authId, $r["amz_tx_amount"]);
        }else{
            return false;
        }

    }
    
    public static function reAuthByOrderRef($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_status = 'Declined' AND amz_tx_type = 'auth' AND amz_tx_order_reference = '".xtc_db_input($orderRef)."' ORDER BY amz_tx_id DESC LIMIT 1";
        $rs = xtc_db_query($q);
        if($r = xtc_db_fetch_array($rs)){
            self::reAuth($r["amz_tx_amz_id"]);
        }
    }
    
    public static function reAuth($authId){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($authId)."'";
        $rs = xtc_db_query($q);
        if($r = xtc_db_fetch_array($rs)){
            self::authorize($r["amz_tx_order_reference"], $r["amz_tx_amount"]);
        }
    }
    
    public static function getNextAuthRef($orderRef){
           return self::getNextRef($orderRef, 'auth');
    }

    public static function getNextCaptureRef($orderRef){
           return self::getNextRef($orderRef, 'capture');
    }

    public static function getNextRefundRef($orderRef){
           return self::getNextRef($orderRef, 'refund');
    }

    public static function getNextRef($orderRef, $type){
            $lastId = 0;
            $prefix = substr($type, 0, 1);
            $q = "SELECT * FROM amz_transactions WHERE amz_tx_type='".$type."' AND amz_tx_order_reference = '".xtc_db_input($orderRef)."' ORDER BY amz_tx_id DESC LIMIT 1";
            $rs = xtc_db_query($q);
            if($r = xtc_db_fetch_array($rs)){
                $lastId = (int)str_replace($orderRef.'-'.$prefix, '', $r["amz_tx_reference"]);
            }
            $newId = $lastId+1;
            return $orderRef.'-'.$prefix.str_pad($newId, 2, '0', STR_PAD_LEFT);
    }

    public static function getOrderRefFromAmzId($amzId){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($amzId)."'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return $r["amz_tx_order_reference"];
    }
    public static function fastAuth($orderRef, $amount, $comment=''){
           ob_start();
           $response = self::authorize($orderRef, $amount, 0, $comment);
           ob_end_clean();
           if(is_object($response)){
               if($response->getAuthorizeResult()->getAuthorizationDetails()->getAuthorizationStatus()->getState() != 'Open'){
                    return $response;
               }
               self::setOrderStatusAuthorized($orderRef);
           }
           return $response;
    }

    public static function setOrderStatusAuthorized($orderRef){
        $oid = self::getOrdersIdFromOrderRef($orderRef);
        if($oid){
        $newStatus = MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK;
        $comment = 'Amazon Payments Advanced';
        self::setOrderStatus($oid, $newStatus, $comment);
        }else{
            $_SESSION["amzSetStatusAuthorized"][] = $orderRef;
        }
    }

    public static function setOrderStatusCaptured($orderRef){
        $oid = self::getOrdersIdFromOrderRef($orderRef);
        if($oid){
        $newStatus = MODULE_PAYMENT_AM_APA_ORDER_STATUS_PAYED;
        $comment = 'alkim media Amazon Modul';
        self::setOrderStatus($oid, $newStatus, $comment);
        }else{
            $_SESSION["amzSetStatusCaptured"][] = $orderRef;
        }
    }


    public static function setOrderStatus($oid, $status, $comment){
         $sql_data_array = array ('orders_id' => $oid,
                           'orders_status_id' => $status,
                           'date_added' => 'now()',
                           'customer_notified' => 0,
                           'comments' => $comment);
        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
        $q = "UPDATE orders SET orders_status = ".(int)$status." WHERE orders_id = ".(int)$oid;
        xtc_db_query($q);
    }

    public static function getOrdersIdFromOrderRef($orderRef){
        $q = "SELECT orders_id FROM orders WHERE amazon_order_id = '".xtc_db_input($orderRef)."'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return $r["orders_id"];
    }

    public static function getOrderTotalFromOrderRef($orderRef){
        $oid = self::getOrdersIdFromOrderRef($orderRef);
        $total = self::getOrderTotal($oid);
        return $total;
    }

    public static function getOrderRefTotal($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_order_reference = '".xtc_db_input($orderRef)."' AND amz_tx_type = 'order_ref'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return (float)$r["amz_tx_amount"];
    }

    public static function getOrderTotal($oid){
        $q = "SELECT * FROM orders_total WHERE orders_id = ".(int)$oid." AND class = 'ot_total'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return (float)$r["value"];
    }



}



?>

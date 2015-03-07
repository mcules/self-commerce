<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   AlkimAmazonHandler.class.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
?><?php

class AlkimAmazonHandler{

    public static function getConfig(){
        $config = array();
        if (MODULE_PAYMENT_AM_APA_MODE == 'live') {
            $config['environment'] = 'live';
        } else {
            $config['environment'] = 'sandbox';
        }
        $config['merchantId'] = AWS_MERCHANT_ID;
        $config['accessKey'] = AWS_ACCESS_KEY_ID;
        $config['secretKey'] = AWS_SECRET_ACCESS_KEY;
        $config['applicationName'] = 'xtcm-am_apa';
        $config['applicationVersion'] = '0.1';
    	$config['cnName'] = 'sns.amazonaws.com';
        $config['region'] = 'DE';
        $config['serviceURL'] = '';
        $config['widgetURL'] = '';
        $config['caBundleFile'] = DIR_FS_DOCUMENT_ROOT . 'AmazonAdvancedPayment/ca-bundle.crt';
        $config['clientId'] = '';
        return $config;
    }

    public static function throwException($msg){
        throw new Exception($msg);
    }

    public static function getService(){
        $config  = self::getConfig();
        return new OffAmazonPaymentsService_Client($config);
    }

    public static function getClassForStatus($status){
        switch($status){
            case 'Open':
            case 'Completed':
            case 'Closed':
                return 'amzGreen';
                break;
            case 'Pending':
                return 'amzOrange';
                break;
            default:
                return 'amzRed';
                break;
       }

    }

    public static function getAdminSkeleton($orders_id){
        global $request_type;

        $q = "SELECT amazon_order_id FROM orders WHERE orders_id = ".(int)$orders_id;
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        if($r["amazon_order_id"]){
            $ret = '<link rel="stylesheet" type="text/css" href="../AmazonAdvancedPayment/css/admin.css" />
                    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
                    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
                    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
                    <script src="../AmazonAdvancedPayment/js/admin.js"></script>
                    <input type="hidden" class="amzAjaxHandler" value="'.xtc_href_link('amz_configuration.php', 'ajax=1', ($request_type?$request_type:'NONSSL')).'" />
                    <div class="amzAdminWr" data-orderRef="'.$r["amazon_order_id"].'">
                        <div class="amzAdminOrderHistoryWr">

                            <div class="amzAdminOrderHistory">

                            </div>
                        </div>
                        <div class="amzAdminOrderSummary">

                        </div>
                        <div class="amzAdminOrderActions">

                        </div>
                    </div>';
             return $ret;
        }
    }

    public static function isOrderRefValid($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_type = 'order_ref' AND amz_tx_order_reference = '".xtc_db_input($orderRef)."'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return ($r["amz_tx_merchant_id"] == '' || $r["amz_tx_merchant_id"] == AWS_MERCHANT_ID);
    }

    public static function getOrderHistory($orderRef){

        $ret = '';
        $q = "SELECT * FROM amz_transactions WHERE amz_tx_order_reference = '".xtc_db_input($orderRef)."' ORDER BY amz_tx_time";
        $referenceStatus = '';
        $rs = xtc_db_query($q);
        $ret = '';
        while($r = xtc_db_fetch_array($rs)){
            if($r["amz_tx_type"] == 'order_ref'){
                $referenceStatus = $r["amz_tx_status"];
            }
            $ret .= '<tr>
                            <td>
                                '.self::translateTransactionType($r["amz_tx_type"]).'
                            </td>
                            <td>
                                '.self::formatAmount($r["amz_tx_amount"]).'
                            </td>
                            <td>
                                '.date('Y-m-d H:i:s', $r["amz_tx_time"]).'
                            </td>
                            <td>
                                <span class="'.self::getClassForStatus($r["amz_tx_status"]).'">'.$r["amz_tx_status"].'</span>
                            </td>
                            <td>
                                '.date('Y-m-d H:i:s', $r["amz_tx_last_change"]).'
                            </td>
                            <td>
                                '.$r["amz_tx_amz_id"].'
                            </td>
                            <td>
                                '.($r["amz_tx_expiration"]!=0?date('Y-m-d H:i:s', $r["amz_tx_expiration"]):'-').'
                            </td>
                      </tr>';

        }

        if($ret != ''){
            return '<h3>'.AMZ_HISTORY.'</h3><table>
                        <tr class="headline">
                            <td>
                                '.AMZ_TX_TYPE_HEADING.'
                            </td>
                            <td>
                                '.AMZ_TX_AMOUNT_HEADING.'
                            </td><td>
                                '.AMZ_TX_TIME_HEADING.'
                            </td>
                            <td>
                                '.AMZ_TX_STATUS_HEADING.'
                            </td>
                            <td>
                                '.AMZ_TX_LAST_CHANGE_HEADING.'
                            </td>
                            <td>
                                '.AMZ_TX_ID_HEADING.'
                            </td>
                            <td>
                                '.AMZ_TX_EXPIRATION_HEADING.'
                            </td>
                      </tr>
                   '.$ret.'</table>
                   <div>
                   '.
                   (self::isOrderRefValid($orderRef)?
                   '
                       <a href="#" class="amzAjaxLink amzButton" data-action="refreshOrder" data-orderRef="'.$orderRef.'">'.AMZ_REFRESH.'</a>
                       '.($referenceStatus=='Open' || $referenceStatus == 'Suspended'?'
                       <a href="#" class="amzAjaxLink amzButton" data-action="cancelOrder" data-orderRef="'.$orderRef.'">'.AMZ_CANCEL_ORDER.'</a>
                       <a href="#" class="amzAjaxLink amzButton" data-action="closeOrder" data-orderRef="'.$orderRef.'">'.AMZ_CLOSE_ORDER.'</a>
                       ':'').'
                    '
                    :
                    '<div style="font-weight:bold; color:#cc0000;">'.AMZ_MERCHANT_ID_INVALID.'</div>'
                   ).'
                   </div>
                   ';
        }



    }

    public static function getOrderAuthorizedAmount($orderRef, $pending = true){
        $q = "SELECT SUM(amz_tx_amount) AS auth_sum FROM amz_transactions WHERE
                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_type='auth'
                        AND
                    (amz_tx_status = 'Open'
                        ".($pending?" OR amz_tx_status = 'Pending' ":"")."
                    )
                    ";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return (float)$r["auth_sum"];
    }

     public static function getOrderCapturedAmount($orderRef){
        $q = "SELECT SUM(amz_tx_amount) AS capture_sum FROM amz_transactions WHERE
                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_type='capture'
                        AND
                    (amz_tx_status = 'Completed'
                         OR
                    amz_tx_status = 'Closed'
                    )";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return (float)$r["capture_sum"];
    }

    public static function getOrderRefundedAmount($orderRef){
        $q = "SELECT SUM(amz_tx_amount) AS refund_sum FROM amz_transactions WHERE
                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_type='refund'
                        AND
                    amz_tx_status = 'Completed'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return (float)$r["refund_sum"];
    }

    public static function getOrderOpenAuthorizations($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE

                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_type='auth'
                        AND
                    amz_tx_status = 'Open'";
        $rs = xtc_db_query($q);
        $ret = array();
        while($r = xtc_db_fetch_array($rs)){
            $ret[] = $r;
        }

        return $ret;
    }

    public static function getOrderCaptures($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE
                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_type='capture'";
        $rs = xtc_db_query($q);
        $ret = array();
        while($r = xtc_db_fetch_array($rs)){
            $ret[] = $r;
        }

        return $ret;
    }

    public static function getOrderUnclosedCaptures($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE
                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_status != 'Closed'
                        AND
                    amz_tx_status != 'Declined'
                        AND
                    amz_tx_type='capture'";
        $rs = xtc_db_query($q);
        $ret = array();
        while($r = xtc_db_fetch_array($rs)){
            $ret[] = $r;
        }

        return $ret;
    }


    public static function getOrderSummary($orderRef){
        $ret = '<h3>'.AMZ_SUMMARY.'</h3><table>
                    <tr>
                        <td><b>'.AMZ_ORDER_AUTH_TOTAL.'</b></td>
                        <td class="amzAmountCell">'.self::formatAmount(($auth = self::getOrderAuthorizedAmount($orderRef, false))).'</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><b>'.AMZ_ORDER_CAPTURE_TOTAL.'</b></td>
                        <td class="amzAmountCell">'.self::formatAmount(($captured = self::getOrderCapturedAmount($orderRef))).'</td>
                    </tr>
                     <tr>
                        <td><b>'.AMZ_TX_AMOUNT_REFUNDED_HEADING.'</b></td>
                        <td class="amzAmountCell">'.self::formatAmount(($refunded = self::getOrderRefundedAmount($orderRef))).'</td>
                    </tr>
                     <tr>
                        <td><b>'.AMZ_TX_SUM.'</b></td>
                        <td class="amzAmountCell"><b>'.self::formatAmount($captured-$refunded).'</b></td>
                    </tr>

                </table>';
        return $ret;





    }
     public static function getOrderState($orderRef){
        $q = "SELECT * FROM amz_transactions WHERE
                    amz_tx_order_reference = '".xtc_db_input($orderRef)."'
                         AND
                    amz_tx_type='order_ref'";
        $rs = xtc_db_query($q);
        $r = xtc_db_fetch_array($rs);
        return $r["amz_tx_status"];
     }

     public static function getOrderActions($orderRef){
            $orderState = self::getOrderState($orderRef);
            $ret = '';
           if(!self::isOrderRefValid($orderRef)){
            return '';
           }
            if($orderState == 'Open' || $orderState == 'Closed'){
                    $openAuth = self::getOrderOpenAuthorizations($orderRef);
                    if(count($openAuth)>0){
                        $ret .= '<h4>'.AMZ_CAPTURE_FROM_AUTH_HEADING.'</h4>';
                        $ret .= '<table>
                                    <tr class="headline">
                                        <td class="amzAmountCell">
                                            '.AMZ_TX_AMOUNT_HEADING.'
                                        </td><td>

                                            '.AMZ_TX_TIME_HEADING.'
                                        </td>
                                        <td>
                                            '.AMZ_TX_ID_HEADING.'
                                        </td>
                                        <td>

                                            '.AMZ_TX_EXPIRATION_HEADING.'
                                        </td>
                                        <td>
                                            '.AMZ_TX_ACTION_HEADING.'
                                        </td>
                                  </tr>';

                        foreach($openAuth AS $r){
                             $ret .= '<tr>
                                        <td class="amzAmountCell">

                                            '.self::formatAmount($r["amz_tx_amount"]).'
                                        </td>
                                        <td>
                                            '.date('Y-m-d H:i:s', $r["amz_tx_time"]).'
                                        </td>
                                        <td>

                                            '.$r["amz_tx_amz_id"].'
                                        </td>
                                        <td>
                                            '.($r["amz_tx_expiration"]!=0?date('Y-m-d H:i:s', $r["amz_tx_expiration"]):'-').'
                                        </td>
                                        <td>
                                            <div>
                                                <a href="#" class="amzAjaxLink amzButton" data-action="captureTotalFromAuth" data-authid="'.$r["amz_tx_amz_id"].'">'.AMZ_CAPTURE_TOTAL_FROM_AUTH.'</a>
                                            </div>
                                            <div>
                                                <input type="text" class="amzAmountField" value="'.self::formatAmount($r["amz_tx_amount"]).'" />
                                                <a href="#" class="amzAjaxLink amzButton" data-action="captureAmountFromAuth" data-authid="'.$r["amz_tx_amz_id"].'">'.AMZ_CAPTURE_AMOUNT_FROM_AUTH.'</a>

                                            </div>

                                        </td>
                                  </tr>';

                        }
                        $ret .= '</table>';
                    }
            }
            if($orderState == 'Open'){
                    $amountLeftToAuthorize = self::getAmountLeftToAuthorize($orderRef);
                    $amountLeftToOverAuthorize = self::getAmountLeftToOverAuthorize($orderRef);
                    if($amountLeftToAuthorize > 0 || $amountLeftToOverAuthorize> 0 ){
                        $ret .= '<h4>'.AMZ_AUTHORIZE.'</h4>';
                        $ret .= '<table style="width:100%">
                                    <tr class="headline">
                                        <td class="amzAmountCell">
                                            '.AMZ_TX_AMOUNT_NOT_AUTHORIZED_YET_HEADING.'
                                        </td>
                                        <td class="amzAmountCell">
                                            '.AMZ_TX_AMOUNT_POSSIBLE_HEADING.'
                                        </td>

                                        <td>
                                            '.AMZ_TX_ACTION_HEADING.'
                                        </td>
                                  </tr>';

                      if($amountLeftToAuthorize+$amountLeftToOverAuthorize > 0){
                             $ret .= '<tr>
                                        <td class="amzAmountCell">
                                            '.self::formatAmount($amountLeftToAuthorize).'
                                        </td>
                                        <td class="amzAmountCell">
                                            '.self::formatAmount($amountLeftToAuthorize+$amountLeftToOverAuthorize).'
                                        </td>
                                        <td>
                                            '.($amountLeftToAuthorize>0?'
                                            <a href="#" class="amzAjaxLink amzButton" data-action="authorizeAmount" data-amount="'.$amountLeftToAuthorize.'" data-orderRef="'.$orderRef.'">'.AMZ_AUTHORIZE.'</a>
                                            '
                                            :
                                            ''
                                            ).'
                                             <div>
                                                        <nobr>
                                                            <input type="text" class="amzAmountField" value="'.self::formatAmount(($amountLeftToAuthorize>0?$amountLeftToAuthorize:$amountLeftToOverAuthorize)).'" />
                                                            <a href="#" class="amzAjaxLink amzButton" data-action="authorizeAmountFromField" data-orderRef="'.$orderRef.'">'.($amountLeftToAuthorize>0?AMZ_AUTHORIZE_AMOUNT:AMZ_OVER_AUTHORIZE_AMOUNT).'</a>
                                                        </nobr>
                                                    </div>
                                        </td>
                                  </tr>';
                       }

                       $ret .= '</table>';

                    }

               }

                    $captures = self::getOrderUnclosedCaptures($orderRef);
                    if(count($captures) > 0){
                        $ret .= '<h4>'.AMZ_REFUNDS.'</h4><table>
                                                <tr class="headline">

                                                    <td class="amzAmountCell">
                                                        '.AMZ_TX_AMOUNT_HEADING.'
                                                    </td>
                                                    <td class="amzAmountCell">
                                                        '.AMZ_TX_AMOUNT_REFUNDED_HEADING.'
                                                    </td>
                                                    <td>
                                                        '.AMZ_TX_AMOUNT_REFUNDABLE_HEADING.'
                                                    </td class="amzAmountCell">
                                                    <td>
                                                        '.AMZ_TX_TIME_HEADING.'
                                                    </td>
                                                    <td>
                                                        '.AMZ_TX_STATUS_HEADING.'
                                                    </td>
                                                    <td>
                                                        '.AMZ_TX_LAST_CHANGE_HEADING.'
                                                    </td>
                                                    <td>
                                                        '.AMZ_TX_ID_HEADING.'
                                                    </td>

                                                    <td>
                                                        '.AMZ_TX_ACTION_HEADING.'
                                                    </td>
                                              </tr>
                                           ';
                        foreach($captures AS $r){
                                $ret .= '<tr>

                                                <td class="amzAmountCell">
                                                    '.self::formatAmount($r["amz_tx_amount"]).'
                                                </td>
                                                <td class="amzAmountCell">
                                                    '.self::formatAmount($r["amz_tx_amount_refunded"]).'
                                                </td>
                                                <td class="amzAmountCell">
                                                    '.self::formatAmount(($refundable = (min((75+$r["amz_tx_amount"]), (round($r["amz_tx_amount"]*1.15,2)))-$r["amz_tx_amount_refunded"]))).'
                                                </td>
                                                <td>
                                                    '.date('Y-m-d H:i:s', $r["amz_tx_time"]).'
                                                </td>
                                                <td>
                                                    <span class="'.self::getClassForStatus($r["amz_tx_status"]).'">'.$r["amz_tx_status"].'</span>
                                                </td>
                                                <td>
                                                    '.date('Y-m-d H:i:s', $r["amz_tx_last_change"]).'
                                                </td>
                                                <td>
                                                    '.$r["amz_tx_amz_id"].'
                                                </td>

                                                <td>
                                                    '.($r["amz_tx_amount"]-$r["amz_tx_amount_refunded"]>0?'
                                                    <div>
                                                        <a href="#" class="amzAjaxLink amzButton" data-action="refundAmount" data-amount="'.($r["amz_tx_amount"]-$r["amz_tx_amount_refunded"]).'" data-captureid="'.$r["amz_tx_amz_id"].'">'.AMZ_REFUND_TOTAL.'</a>
                                                    </div>
                                                        ':
                                                        ''
                                                      ).'
                                                    <div>
                                                        <nobr>
                                                            <input type="text" class="amzAmountField" value="'.self::formatAmount(($r["amz_tx_amount"]-$r["amz_tx_amount_refunded"]>0?($r["amz_tx_amount"]-$r["amz_tx_amount_refunded"]):$refundable)).'" />
                                                            <a href="#" class="amzAjaxLink amzButton" data-action="refundAmountFromField" data-captureid="'.$r["amz_tx_amz_id"].'">'.($r["amz_tx_amount"]-$r["amz_tx_amount_refunded"]>0?AMZ_REFUND_AMOUNT:AMZ_REFUND_OVER_AMOUNT).'</a>
                                                        </nobr>
                                                    </div>
                                                </td>
                                          </tr>';

                         }

                }
                        $ret .= '</table>';









           if($ret != ''){
                $ret =  $ret = '<h3>'.AMZ_ACTIONS.'</h3>'.$ret;
           }
        return $ret;





    }

    public static function getAmountLeftToAuthorize($orderRef){
        $total = AlkimAmazonTransactions::getOrderRefTotal($orderRef);
        $authorized = self::getOrderAuthorizedAmount($orderRef);
        $captured = self::getOrderCapturedAmount($orderRef);
        $left = $total - $authorized - $captured;
        $left = min($left, $total);
        $left = round(max(0, $left), 2);
        return $left;
    }

    public static function getAmountLeftToOverAuthorize($orderRef){
        $total = AlkimAmazonTransactions::getOrderRefTotal($orderRef);
        $authorized = self::getOrderAuthorizedAmount($orderRef);
        $captured = self::getOrderCapturedAmount($orderRef);


        $left = min(($total+75), round(($total*1.15), 2)) - $authorized - $captured;

        $left -= self::getAmountLeftToAuthorize($orderRef);
        $left = round(max(0, $left), 2);
        return $left;
    }



    public static function formatAmount($amount){
        return number_format($amount, 2, ',', '');
    }

    public static function translateTransactionType($str){

            switch($str){
                case 'auth':
                    $str = AMZ_AUTH_TEXT;
                    break;
                case 'order_ref':
                    $str = AMZ_ORDER_TEXT;
                    break;
                case 'capture':
                    $str = AMZ_CAPTURE_TEXT;
                    break;
                case 'refund':
                    $str = AMZ_REFUND_TEXT;
                    break;
            }

            return $str;

    }


    public static function sendSoftDeclinedMail($orderRef){
        self::sendDeclinedMail($orderRef, 'soft');
    }
    public static function sendHardDeclinedMail($orderRef){
        self::sendDeclinedMail($orderRef, 'hard');
    }

    public static function sendDeclinedMail($orderRef, $type){
        $q = "SELECT * FROM orders WHERE amazon_order_id = '".xtc_db_input($orderRef)."'";
        $rs = xtc_db_query($q);
        if($r = xtc_db_fetch_array($rs)){
            $smarty = new Smarty;
            $smarty->template_dir = DIR_FS_CATALOG.'templates';
            $smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
            $smarty->config_dir = DIR_FS_CATALOG.'lang';
            $smarty->assign('language', $r["language"]);
            $smarty->assign('ORDER_NR', $r["orders_id"]);
            $smarty->assign('ORDER_DATE', date(DATE_FORMAT, strtotime($r["date_purchased"])));
            $smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
            $html = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$r["language"].'/amazon_'.$type.'_decline.html');
            $txt = $smarty->fetch(CURRENT_TEMPLATE.'/mail/'.$r["language"].'/amazon_'.$type.'_decline.txt');
            xtc_php_mail(AMZ_PAYMENT_EMAIL, AMZ_PAYMENT_NAME, $r["customers_email_address"], $r["customers_name"], '', AMZ_PAYMENT_EMAIL, AMZ_PAYMENT_NAME,
                        '', '', constant('AMZ_'.strtoupper($type).'_DECLINE_SUBJECT_'.strtoupper($r["language"])), $html, $txt);


        }
    }

    public static function intelligentDeclinedMail($amz_id, $reason){
        if(AMZ_SEND_MAILS_ON_DECLINE == 'True'){
            $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($amz_id)."'";
            $rs = xtc_db_query($q);
            if($r = xtc_db_fetch_array($rs)){
                if($r["amz_tx_status"] == 'Declined' && $r["amz_tx_customer_informed"] == 0){
                      $informed = 0;
                      if($reason == 'InvalidPaymentMethod'){
                          self::sendSoftDeclinedMail($r["amz_tx_order_reference"]);
                          $informed = 1;
                      }elseif($reason == 'AmazonRejected'){
                          self::sendHardDeclinedMail($r["amz_tx_order_reference"]);
                          $informed = 1;
                      }

                      if($informed == 1){
                            $q = "UPDATE amz_transactions SET amz_tx_customer_informed = 1 WHERE amz_tx_id = '".(int)$r["amz_tx_id"]."'";
                            xtc_db_query($q);
                      }


                }
            }
        }
    }


    public static function isAutomaticAuth($amz_id){
            if(AMZ_AUTHORIZATION_MODE == 'after_checkout'){
                $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($amz_id)."'";
                $rs = xtc_db_query($q);
                if($r = xtc_db_fetch_array($rs)){
                    if($r["amz_tx_type"] == 'auth'){
                        $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($r["amz_tx_order_reference"])."'";
                        $rs = xtc_db_query($q);
                        if($orderRefR = xtc_db_fetch_array($rs)){
                                if($r["amz_tx_amount"] == $orderRefR["amz_tx_amount"]){
                                    return true;
                                }
                        }
                    }
                }

            }
            return false;

    }

     public static function getAuthAmount($amz_id){

                $q = "SELECT * FROM amz_transactions WHERE amz_tx_amz_id = '".xtc_db_input($amz_id)."'";
                $rs = xtc_db_query($q);
                if($r = xtc_db_fetch_array($rs)){
                    return $r["amz_tx_amount"];
                }
    }

    public static function shippingCapture(){
        if(AMZ_CAPTURE_MODE=='after_shipping' && MODULE_PAYMENT_AM_APA_STATUS == 'True'){
            $q = "SELECT DISTINCT o.amazon_order_id FROM ".TABLE_ORDERS." o
                            JOIN amz_transactions AS a1 ON (o.amazon_order_id = a1.amz_tx_order_reference AND a1.amz_tx_type = 'auth' AND a1.amz_tx_status = 'Open')
                            LEFT JOIN amz_transactions AS a2 ON (o.amazon_order_id = a2.amz_tx_order_reference AND a2.amz_tx_type = 'capture')
                    WHERE
                        o.amazon_order_id != ''
                            AND
                        o.orders_status = '".AMZ_SHIPPED_STATUS."'
                            AND
                        a2.amz_tx_id IS NULL";
            $rs = xtc_db_query($q);
            while($r = xtc_db_fetch_array($rs)){
                $r = AlkimAmazonTransactions::getAuthorizationForCapture($r["amazon_order_id"]);
                $authId = $r["amz_tx_amz_id"];
                AlkimAmazonTransactions::refreshAuthorization($authId);
                AlkimAmazonTransactions::captureTotalFromAuth($authId);
            }
        }



    }

    public static function reserveNextOrdersId(){
        $q = "INSERT INTO orders () VALUES ()";
        xtc_db_query($q);
        return xtc_db_insert_id();
    }

     public static function searchProducts($q, $categories_id = 0,$manufacturers_id = 0){
            if(strlen($q)<3 && $categories_id == 0 && $manufacturers_id == 0){
                $ret = '';
            }else{

                        $categories_id = (int)$categories_id;
                        $manufacturers_id = (int)$manufacturers_id;
                        $keys = array_unique(explode(' ', $q));
                        $keys = array_values($keys);
                        foreach($keys AS $k) {

                            $t[] = " (pd.products_name LIKE '%" . xtc_db_input($k) . "%'


                                             OR p.products_model LIKE '%" . xtc_db_input($k) . "%'
                                            OR m.manufacturers_name LIKE '%" . xtc_db_input($k) . "%'

                                            ) ";
                        }

                        $q = "SELECT p.*, pd.*, pe.amz_products_excluded_id FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p
                                    LEFT OUTER JOIN manufacturers m ON (m.manufacturers_id = p.manufacturers_id)
                                     LEFT OUTER JOIN amz_products_excluded pe ON (p.products_id = pe.products_id)
                                    ".($categories_id>0?' JOIN products_to_categories p2c ON (p.products_id = p2c.products_id) ':'')."
                                    WHERE
                                        " . implode(' AND ', $t) . "
                                        AND pd.language_id = '" . $_SESSION['languages_id'] . "'
                                        ".($manufacturers_id>0?' AND p.manufacturers_id = '.$manufacturers_id:'')."
                                        ".($categories_id>0?' AND p2c.categories_id = '.$categories_id:'')."

                                    AND p.products_id = pd.products_id GROUP BY p.products_id
                                    LIMIT 20";

                        $rs = xtc_db_query($q);
                        $ret = '<div id="productSearchResultsSubWr">';

                        while($r = xtc_db_fetch_array($rs)){

                                $ret .= '<div class="productLine '.($r["amz_products_excluded_id"]?' excluded ':'').'" data-pid="'.$r["products_id"].'">
                                            <div class="imgWr">
                                                <img src="'.DIR_WS_CATALOG.'images/product_images/thumbnail_images/'.$r["products_image"].'" />
                                            </div>


                                            <div class="descWr">
                                                <div><b>'.$r["products_name"].'</b></div>
                                                <div>'.$r["products_model"].'</div>
                                            </div>
                                          </div>';


                        }
                        $ret .= '</div>';
         }
             $q = "SELECT DISTINCT p.*, pd.* FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p, amz_products_excluded pe

                        WHERE

                            pd.language_id = '" . $_SESSION['languages_id'] . "'
                            AND pe.products_id = p.products_id
                        AND p.products_id = pd.products_id GROUP BY p.products_id
                        LIMIT 20";

            $rs = xtc_db_query($q);
            $ret2 = '<div id="productSearchResultsSubWr2">';

            while($r = xtc_db_fetch_array($rs)){

                    $ret2 .= '<div class="productLine" data-pid="'.$r["products_id"].'">
                                <div class="imgWr">
                                    <img src="'.DIR_WS_CATALOG.'images/product_images/thumbnail_images/'.$r["products_image"].'" />
                                </div>


                                <div class="descWr">
                                    <div><b>'.$r["products_name"].'</b></div>
                                    <div>'.$r["products_model"].'</div>
                                </div>
                              </div>';


            }
            $ret2 .= '</div>';

            return json_encode(array('searchResult'=>utf8_encode($ret),'excluded'=>utf8_encode($ret2))); ;

    }

    public static function excludeProducts($pids){

        if(!is_array($pids)){
            $pids = array($pids);
        }

        foreach($pids AS $pid){
            $sql_arr = array('products_id'=>(int)$pid);
            xtc_db_perform('amz_products_excluded', $sql_arr);
        }
    }

     public static function includeProducts($pids){

      if(!is_array($pids)){
            $pids = array($pids);
        }

        foreach($pids AS $pid){

            $q = "DELETE FROM amz_products_excluded WHERE products_id = ".(int)$pid;
            xtc_db_query($q);
        }
    }

    public static function hasCartExcludedProducts(){

        if (is_array($_SESSION["cart"]->contents)) {

          foreach($_SESSION["cart"]->contents AS $id=>$arr){
            $q = "SELECT * FROM amz_products_excluded WHERE products_id = ".xtc_get_prid($id);
            $rs = xtc_db_query($q);
            if(xtc_db_num_rows($rs)>0){
                return true;
            }
          }
        }
        return false;

    }



   public static function getExcludedShipping(){
        $excluded = explode(',', AMZ_EXCLUDED_SHIPPING);
        if(!is_array($excluded)){
            $excluded = array();
        }
        foreach($excluded AS $k=>$v){
            if(trim($v)!=''){
                $excluded[$k] = trim($v);
            }else{
                unset($excluded[$k]);
            }
        }
        return $excluded;
    }

    public static function isCurrentShippingExcluded(){
        $excluded = self::getExcludedShipping();
        $t = explode('_', $_SESSION["shipping"]["id"]);
        $current = $t[0];
        return in_array($current, $excluded);
    }
    public static function getAddressArrayFromAmzAddress($address){

        $name        = $address->getName();
        $t           = explode(' ', $name);
        $lastNameKey = max(array_keys($t));
        $lastName    = $t[$lastNameKey];
        unset($t[$lastNameKey]);
        $firstName = implode(' ', $t);

        if((string)$address->getAddressLine3() != ''){
            $street = trim($address->getAddressLine3());
            $company = trim($address->getAddressLine1() . ' ' . $address->getAddressLine2());
        }elseif((string)$address->getAddressLine2() != ''){
            $street = trim($address->getAddressLine2());
            $company = trim($address->getAddressLine1());
        }else{
            $street = trim($address->getAddressLine1());
        }



        $city              = $address->getCity();
        $postcode          = $address->getPostalCode();
        $countryCode       = $address->getCountryCode();
        $phone       = $address->getPhone();
        $sql                           = "SELECT countries_name, countries_id FROM " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . $countryCode . "' LIMIT 1";
        $country_query                 = xtc_db_query($sql);
        $country_result                = xtc_db_fetch_array($country_query);
        return array(
            'name' => self::autoDecode((string) $name),
            'firstname' => self::autoDecode($firstName),
            'lastname' => self::autoDecode($lastName),
            'company' => self::autoDecode($company),
            'phone'=>self::autoDecode($phone),
            'street_address' => self::autoDecode($street),
            'suburb' => '',
            'city' => self::autoDecode($city),
            'postcode' => self::autoDecode($postcode),
            'state' => '',
            'country' => array(
                'iso_code_2' => self::autoDecode($countryCode),
                'title' => $country_result['countries_name'],
                'id'=>$country_result["countries_id"]
            ),
            'country_iso_2' => self::autoDecode($countryCode),
            'format_id' => '5'
        );




    }

    public static function log($type, $data, $request = '', $response = ''){

        switch($type){
            case 'api':
                $fn = 'apa_api.log';
                break;
            case 'ipn':
                $fn = 'apa_ipn.log';
                break;
            case 'exception':
                $fn = 'apa_exception.log';
                break;
            default:
                return false;
        }

        $path = DIR_FS_CATALOG.'AmazonAdvancedPayment/log/'.$fn;

        if(file_exists($path) && filesize($path) > 4000000){
            rename($path, $path.'.'.date('Y-m-d_H-i-s').'.log');
        }
        if(file_exists($path)){
            chmod($path, 0777);
        }

        $logHandle = fopen($path, 'a+');
        $fields = array('date', 'orderRef', 'txnId', 'summary', 'requestDocument', 'responseDocument');
        $data["date"] = date('Y-m-d H:i:s');
        if($request != ''){
            $requestPath = 'AmazonAdvancedPayment/log/documents/'.$type.'/request_'.date('Y-m-d_H-i-s').'_'.md5($request).'.txt';
            file_put_contents(DIR_FS_CATALOG.$requestPath, $request);
            $data["requestDocument"] = DIR_FS_CATALOG.$requestPath;
        }

        if($response != ''){
            $responsePath = 'AmazonAdvancedPayment/log/documents/'.$type.'/response_'.date('Y-m-d_H-i-s').'_'.md5($request).'.txt';
            file_put_contents(DIR_FS_CATALOG.$responsePath, $response);
            $data["responseDocument"] = DIR_FS_CATALOG.$responsePath;
        }

        $writeArr = array();

        foreach($fields AS $field){
            $writeArr[] = (string)$data[$field];
        }

        fputcsv($logHandle, $writeArr);
        fclose($logHandle);


    }
    public static function autoEncode($str){
      if (self::isUTF8($str)) {
        return $str;
      }
      return utf8_encode($str);
    }


    public static function autoDecode($str){
      if (self::isUTF8($str)) {
        return utf8_decode($str);
      }
      return $str;
    }

    public static function convertCharset($str){
      if ($_SESSION['language_charset'] == 'utf-8') {
        return self::autoEncode($str);
      }else{
        return self::autoDecode($str);
      }
    }


    public static function isUTF8($str){
          if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
            return true;
          } else {
            return false;
          }
    }
}



<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   amz_configuration.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/

  require('includes/application_top.php');
    require_once (DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');
    require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
  include_once(DIR_FS_CATALOG.'lang/'.$_SESSION["language"].'/modules/payment/am_apa.php');
  require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/.config.inc.php');
    require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
    require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonTransactions.class.php');
    require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonTransactionHistoryRenderer.class.php');
  
  if($_GET["action"] == 'dbUpdate'){
    include_once(DIR_FS_CATALOG.'includes/modules/payment/am_apa.php');
    $amApa = new am_apa;
    $amApa->updateDB();
    xtc_redirect(xtc_href_link('modules.php', 'set=payment&module=am_apa'));
    die;
  
  }
  
  
  
if($_GET["ajax"]=='1'){
    if($_GET["action"]=='shippingCapture'){
        $_POST["action"]='shippingCapture';
    }
    
    switch($_POST["action"]){
        case 'getHistory':
            echo AlkimAmazonHandler::getOrderHistory($_POST["orderRef"]);
            break;
        case 'getSummary':
            echo AlkimAmazonHandler::getOrderSummary($_POST["orderRef"]);
            break;
        case 'getActions':
            echo AlkimAmazonHandler::getOrderActions($_POST["orderRef"]);
            break;
        case 'captureTotalFromAuth':
            $response = AlkimAmazonTransactions::captureTotalFromAuth($_POST["authId"]);

            $details = $response->getCaptureResult()->getCaptureDetails();
            $status = $details->getCaptureStatus()->getState();
            if($status == 'Completed'){
                echo AMZ_CAPTURE_SUCCESS;
            }else{
                echo '<br/><b>'.AMZ_CAPTURE_FAILED.'</b>';
            }
            break;
         case 'captureAmountFromAuth':
            $response = AlkimAmazonTransactions::capture($_POST["authId"], $_POST["amount"]);
            if(is_object($response)){
                $details = $response->getCaptureResult()->getCaptureDetails();
                $status = $details->getCaptureStatus()->getState();
                if($status == 'Completed'){
                    echo AMZ_CAPTURE_SUCCESS;
                }else{
                    echo '<br/><b>'.AMZ_CAPTURE_FAILED.'</b>';
                }
            }else{
               // echo AMZ_REFUND_FAILED;
            }
            break;

        case 'refundAmount':
            $response = AlkimAmazonTransactions::refund($_POST["captureId"], $_POST["amount"]);
            if(is_object($response)){
                $details = $response->getRefundResult()->getRefundDetails();
                $status = $details->getRefundStatus()->getState();
                if($status == 'Pending'){
                    $q = "UPDATE amz_transactions SET amz_tx_amount_refunded = amz_tx_amount_refunded + ".(float)$_POST["amount"]." WHERE amz_tx_amz_id = '".xtc_db_input($_POST["captureId"])."'";
                    xtc_db_query($q);
                    echo AMZ_REFUND_SUCCESS;
                }else{
                    echo AMZ_REFUND_FAILED;
                }
            }else{
               // echo AMZ_REFUND_FAILED;
            }
            break;
        case 'authorizeAmount':
            $response = AlkimAmazonTransactions::authorize($_POST["orderRef"], $_POST["amount"]);
            if($response){
                $details = $response->getAuthorizeResult()->getAuthorizationDetails();
                $status = $details->getAuthorizationStatus()->getState();
                if($status == 'Open' || $status == 'Pending'){
                    echo AMZ_AUTHORIZATION_SUCCESSFULLY_REQUESTED;
                }else{
                    echo '<br/><b>'.AMZ_AUTHORIZATION_REQUEST_FAILED.'</b>';
                }
            }else{
                 echo '<br/><b>'.AMZ_AUTHORIZATION_REQUEST_FAILED.'</b>';
            }
            break;
         case 'refreshOrder':
            $q = "SELECT * FROM amz_transactions WHERE amz_tx_order_reference = '".xtc_db_input($_POST["orderRef"])."' AND amz_tx_status != 'Closed' AND amz_tx_status != 'Declined'";
            $rs = xtc_db_query($q);
            while($r = xtc_db_fetch_array($rs)){
               AlkimAmazonTransactions::intelligentRefresh($r);
            }
            echo '<br/><b>'.AMZ_FINISHED_REFRESHING_ORDER.'</b>';
            break;
         case 'closeOrder':
                AlkimAmazonTransactions::closeOrder($_POST["orderRef"]);
               echo '<br/><b>'.AMZ_ORDER_CLOSED.'</b>';
            break;
         case 'cancelOrder':
                AlkimAmazonTransactions::cancelOrder($_POST["orderRef"]);
               echo '<br/><b>'.AMZ_ORDER_CANCELLED.'</b>';
            break;
          case 'shippingCapture':
                AlkimAmazonHandler::shippingCapture();
            break;
          case 'searchProducts':
               echo AlkimAmazonHandler::searchProducts(utf8_decode($_GET["q"]), (int)$_GET["categories_id"],(int)$_GET["manufacturers_id"]);
               break;
          case 'excludeProduct':
               AlkimAmazonHandler::excludeProducts($_POST["pid"]);
               echo AlkimAmazonHandler::searchProducts(utf8_decode($_GET["q"]), (int)$_GET["categories_id"],(int)$_GET["manufacturers_id"]);
               break;
          case 'includeProduct':
               AlkimAmazonHandler::includeProducts($_POST["pid"]);
               echo AlkimAmazonHandler::searchProducts(utf8_decode($_GET["q"]), (int)$_GET["categories_id"],(int)$_GET["manufacturers_id"]);
               break;
          case 'updateTransactionHistory':
               echo AlkimAmazonTransactionHistoryRenderer::getTableBody($_GET);
               break;
                     
    }

    die;
}



if(isset($_POST["action"])){

    switch($_POST["action"]){
        case 'saveAmzConfig';
            
            if(
                (
                    empty($_POST["configuration"]["MODULE_PAYMENT_AM_APA_MERCHANTID"])
                        ||
                    empty($_POST["configuration"]["MODULE_PAYMENT_AM_APA_ACCESKEY"])
                        ||
                    empty($_POST["configuration"]["MODULE_PAYMENT_AM_APA_SECRETKEY"])
                )
                    &&
                $_POST["configuration"]["MODULE_PAYMENT_AM_APA_STATUS"] == 'True'
               ){
            
                $_POST["configuration"]["MODULE_PAYMENT_AM_APA_STATUS"] = 'False';
            
            }
            
            foreach($_POST["configuration"] AS $k=>$v){
                $q = "SELECT COUNT(*) AS num FROM ".TABLE_CONFIGURATION." WHERE configuration_key='".xtc_db_input($k)."'";
                $rs = xtc_db_query($q);
                $r = xtc_db_fetch_array($rs);

                $sql_arr = array('configuration_value'=>$v);
                if($r["num"] == 0){
                    $sql_arr["configuration_key"] = $k;
                    xtc_db_perform(TABLE_CONFIGURATION, $sql_arr);
                }else{
                    xtc_db_perform(TABLE_CONFIGURATION, $sql_arr, 'update', " configuration_key='".xtc_db_input($k)."' ");
                }


            }
            $_SESSION["checkCredentials"] = true;
            xtc_redirect(xtc_href_link('amz_configuration.php'));
            break;

    }



}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="../AmazonAdvancedPayment/css/admin.css">
<link rel="stylesheet" type="text/css" href="../AmazonAdvancedPayment/css/thickbox.css">
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css">
<script type="text/javascript" src="includes/general.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-1.7.2.js"></script>
<script type="text/javascript" src="../AmazonAdvancedPayment/js/thickbox.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.9.1/jquery-ui.js"></script>

<script>
var searchTO;
var ajaxHelper = 'amz_configuration.php?ajax=1';

$(document).on('keyup', '.productSearch', function(){
    clearTimeout(searchTO);
    searchTO = setTimeout(function(){
        doSearch();
    }, 300);


});
$(document).on('change', '[name=cPath], [name=manufacturer]', function(){
    doSearch();
});
$(document).on('click', '#productSearchResults .productLine', function(){
    $('#productSearchResults, #productSearchResults2').css('opacity', 0.5);
            $.post(ajaxHelper+'&q='+encodeURIComponent($('.productSearch').val())+'&categories_id='+$('[name=cPath]').val()+'&manufacturers_id='+parseInt($('[name=manufacturer]').val()), {action:"excludeProduct", pid:$(this).attr('data-pid')}, function(data){
               data = $.parseJSON(data);
               $('#productSearchResults').html(data.searchResult).css('opacity', 1);
               $('#productSearchResults2').html(data.excluded).css('opacity', 1);
            });
});
$(document).on('click', '#productSearchResults2 .productLine', function(){
    $('#productSearchResults, #productSearchResults2').css('opacity', 0.5);
            $.post(ajaxHelper+'&q='+encodeURIComponent($('.productSearch').val())+'&categories_id='+$('[name=cPath]').val()+'&manufacturers_id='+parseInt($('[name=manufacturer]').val()), {action:"includeProduct", pid:$(this).attr('data-pid')}, function(data){
               data = $.parseJSON(data);
               $('#productSearchResults').html(data.searchResult).css('opacity', 1);
               $('#productSearchResults2').html(data.excluded).css('opacity', 1);
            });
});

$(document).on('click', '.excludeAll', function(e){
    e.preventDefault();
    var pids = [];
    $('#productSearchResultsSubWr .productLine').each(function(){
        pids.push($(this).attr('data-pid'));
    });
     $.post(ajaxHelper+'&q='+encodeURIComponent($('.productSearch').val())+'&categories_id='+$('[name=cPath]').val()+'&manufacturers_id='+parseInt($('[name=manufacturer]').val()), {action:"excludeProduct", pid:pids}, function(data){
               data = $.parseJSON(data);
               $('#productSearchResults').html(data.searchResult).css('opacity', 1);
               $('#productSearchResults2').html(data.excluded).css('opacity', 1);
            });

});

$(document).on('click', '.includeAll', function(e){
    e.preventDefault();
    var pids = [];
    $('#productSearchResultsSubWr2 .productLine').each(function(){
        pids.push($(this).attr('data-pid'));
    });
     $.post(ajaxHelper+'&q='+encodeURIComponent($('.productSearch').val())+'&categories_id='+$('[name=cPath]').val()+'&manufacturers_id='+parseInt($('[name=manufacturer]').val()), {action:"includeProduct", pid:pids}, function(data){
               data = $.parseJSON(data);
               $('#productSearchResults').html(data.searchResult).css('opacity', 1);
               $('#productSearchResults2').html(data.excluded).css('opacity', 1);
            });

});

function doSearch(){
           if($('#productSearchResults').length > 0){


            $('#productSearchResults, #productSearchResults2').css('opacity', 0.5);
            $.post(ajaxHelper+'&q='+encodeURIComponent($('.productSearch').val())+'&categories_id='+$('[name=cPath]').val()+'&manufacturers_id='+parseInt($('[name=manufacturer]').val()), {action:"searchProducts"}, function(data){
               data = $.parseJSON(data);
               $('#productSearchResults').html(data.searchResult).css('opacity', 1);
               $('#productSearchResults2').html(data.excluded).css('opacity', 1);
            });
          }
}


function transactionFilter(page){
    var q = $('.transactionFilterInput').serialize()+'&page='+page;
    $('#transactionHistoryTable tbody *').css('opacity', 0.5);
    if($('.thSorter.active').length > 0){
        q += '&sort='+escape($('.thSorter.active').attr('data-sort'));
    }
    $.post(ajaxHelper+'&'+q, {action:"updateTransactionHistory"}, function(data){
               $('#transactionHistoryTable tbody').html(data);
    });
}
$(document).ready(function(){
    if($('[name=manufacturer]').length > 0){
        $('[name=manufacturer]').trigger('change');
    }
    $('.transactionFilterInput').bind('keyup change', function(){
        clearTimeout(searchTO);
        searchTO = setTimeout(function(){
            transactionFilter(1);
        }, 300);
    });
    $('.pageLink').live('click', function(){
        transactionFilter($(this).attr('data-page'));
        return false;
    });
    $('.thSorter').click(function(){
        $('.thSorter').removeClass('active');
        $(this).addClass('active');
        transactionFilter(1);
    });
    $('.timeInput').datepicker({ dateFormat: "yy-mm-dd" });
});
</script>

<style>
.amzConfWr{
    font-family:Verdana;
}
.configurationTable td{
    border-bottom: 1px solid #CCCCCC;
    color: #000000;
    font-family: Verdana,Arial,sans-serif;
    font-size: 10px;
    text-align: left;
    vertical-align: middle;
}
.productLine{
    height:30px;
    padding:8px;
    border-bottom:1px solid #ddd;
    position:relative;
    clear:both;
    cursor: pointer;
}

.productLine.excluded{
    opacity:0.3;
}
.imgWr{
    float:left;
    width:120px;
    height:30px;
    margin-right:10px;

}

.imgWr img{

    max-width:120px;
    max-height:30px;

}
#productSearchResults, #productSearchResults2{
    height:400px;
    border:1px solid #ddd;

    overflow-y:scroll;
}
#transactionHistoryTable{
    font-size:12px;
    font-family:Verdana, Arial; 
    width:1200px;
    
}

#transactionHistoryTable tbody tr:nth-child(odd){
   background:#ddd;
}

#transactionHistoryTable td{
   padding:4px;
}
.transactionFilterInput{
    width:120px;
}
.timeInput{
    width:80px;
}

.thSorter{
    font-size:16px;
    cursor:pointer;
    color:#999;
}

.thSorter.active{
    color:#000;
    
}

.amzError, .amzSuccess{
    padding:10px;
    margin:10px 0;
    border:1px solid #ccc;
}

.amzError{

    background:#EF4A58;
}

.amzSuccess{
        background:#66DB39;
}


</style>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php
define('NO_JQUERY', 1);
if($_GET["mode"] != 'excludeProducts'){
require(DIR_WS_INCLUDES . 'header.php');
 ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
     <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
    </table>
    </td>
<!-- body_text //-->
<td valign="top" class="amzConfWr">

<?php
} else{

    echo '<div style="padding:10px; font-family:Verdana;" class="amzConfWr">';
}

 if($_GET["mode"] == 'excludeProducts'){

$q = "SELECT * FROM manufacturers ORDER BY manufacturers_name";
$rs = xtc_db_query($q);
$manufacturersArr = array(array('id'=>0, 'text'=>AMZ_ALL_MANUFACTURERS));
while($r = xtc_db_fetch_array($rs)){
     $manufacturersArr[] = array('id'=>$r["manufacturers_id"], 'text'=>$r["manufacturers_name"]);
}
?>
  <div style="height:95px;">
                         <h2 style="font-size:15px;"><?php echo AMZ_EXCLUDE_PRODUCTS; ?></h2>
                         <input type="text" class="productSearch" placeholder="<?php echo AMZ_SEARCH; ?>" style="width: 240px; font-size:14px; font-weight:bold; padding:7px; border:1px solid #ddd; margin:10px 0;" />
                         <?php echo xtc_draw_pull_down_menu('cPath', xtc_get_category_tree(), $current_category_id); ?>
                         <?php echo xtc_draw_pull_down_menu('manufacturer', $manufacturersArr, $currentManufacturer); ?>
                     </div>

       <table>
       <tr>
       <td style="width:500px; padding-right:20px;">
       <h3 style="font-size:13px;"><?php echo AMZ_SEARCH_RESULT; ?>          </h3>
       <a href="#" class="excludeAll"><?php echo AMZ_EXCLUDE_ALL_PRODUCTS; ?></a>
    <div id="productSearchResults"></div>
    </td><td style="width:500px;">
     <h3 style="font-size:13px;"><?php echo AMZ_EXCLUDED_PRODUCTS; ?></h3>
     <a href="#" class="includeAll"><?php echo AMZ_INCLUDE_ALL_PRODUCTS; ?></a>
    <div id="productSearchResults2"></div>
             </td>
            </tr>
           </table>

<?php }else{ 



if($_GET["action"] == 'transactionHistory'){
        echo AlkimAmazonTransactionHistoryRenderer::render($_GET);
        ?>       
        </td>
        <!-- body_text_eof //-->
          </tr>
        </table>
        <!-- body_eof //-->
        <!-- footer //-->
        <?php
            require(DIR_WS_INCLUDES . 'footer.php');
        ?>
        <!-- footer_eof //-->
        </body>
        </html>
        <?php require(DIR_WS_INCLUDES . 'application_bottom.php');
        die;
}
 
 
if(isset($_SESSION["checkCredentials"]) && AWS_MERCHANT_ID != '' && MODULE_PAYMENT_AM_APA_ACCESKEY != '' && MODULE_PAYMENT_AM_APA_SECRETKEY_TITLE != ''){
        $config                     = AlkimAmazonHandler::getConfig();
        $service                    = new OffAmazonPaymentsService_Client($config);
        $getOrderRefDetailsReqModel = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest(array(
            'AmazonOrderReferenceId' => 'S00-0000000-0000000',
            'SellerId' => AWS_MERCHANT_ID
        ));
        $error = false;
        try {
                $getOrderRefDetailsResp = $service->getOrderReferenceDetails($getOrderRefDetailsReqModel);
        }catch (OffAmazonPaymentsService_Exception $e) {
                switch($e->getErrorCode()){
                    case 'InvalidOrderReferenceId':
                        break;
                    case 'InvalidParameterValue':
                        $error = true;
                        $errorMessage = AMZ_INVALID_MERCHANT_ID;
                        break;
                    case 'InvalidAccessKeyId':
                        $error = true;
                        $errorMessage = AMZ_INVALID_ACCESS_KEY;
                        break;
                    case 'SignatureDoesNotMatch':
                        $error = true;
                        $errorMessage = AMZ_INVALID_SECRET;
                        break;
                }
        }

        if($error){
            echo '<div class="amzError">
                    '.$errorMessage.'
                  </div>';
            xtc_db_perform(TABLE_CONFIGURATION, array('configuration_value'=>'False'), 'update', " configuration_key='MODULE_PAYMENT_AM_APA_STATUS' ");
        }else{
            echo '<div class="amzSuccess">
                    '.AMZ_CREDENTIALS_SUCCESS.'
                  </div>';
        }
        unset($_SESSION["checkCredentials"]);
}   
?> 
  
<form name="configuration" action="<?php echo xtc_href_link('amz_configuration.php'); ?>" method="post">
<input type="hidden" name="action" value="saveAmzConfig" />
<table width="100%"  border="0" cellspacing="0" cellpadding="8" class="configurationTable">

                                    <tr>
                                      <td colspan="3" bgcolor="#741212" align="right"><span style="font-weight:bold;font-size:3em;color:white;font-size:150%"><?php echo AMZ_HEADING_AMAZON_PAYMENTS_ACCOUNT; ?></span></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_MERCHANTID_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("MODULE_PAYMENT_AM_APA_MERCHANTID"); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_MERCHANTID_DESC; ?></td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_ACCESKEY_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("MODULE_PAYMENT_AM_APA_ACCESKEY"); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_ACCESKEY_DESC; ?></td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_SECRETKEY_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInputPassword("MODULE_PAYMENT_AM_APA_SECRETKEY"); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_SECRETKEY_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_CLIENTID_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("MODULE_PAYMENT_AM_APA_CLIENTID"); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_CLIENTID_DESC; ?></td>
                                    </tr>
                                    <tr>
                                    <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" bgcolor="#741212" align="right"><span style="font-weight:bold;font-size:3em;color:white;font-size:150%"><?php echo AMZ_HEADING_GENERAL_SETTINGS; ?></span></td>
                                    </tr>
									<tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_STATUS_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[MODULE_PAYMENT_AM_APA_STATUS]', MODULE_PAYMENT_AM_APA_STATUS); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_STATUS_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_DEBUG_MODE_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[AMZ_DEBUG_MODE]', AMZ_DEBUG_MODE); ?></td>
                                      <td><?php echo AMZ_DEBUG_MODE_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_ALLOWED_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("MODULE_PAYMENT_AM_APA_ALLOWED"); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_ALLOWED_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_LPA_MODE_TITLE; ?></b></td>
                                      <td><?php echo renderLpaModeSelect('configuration[MODULE_PAYMENT_AM_APA_LPA_MODE]', MODULE_PAYMENT_AM_APA_LPA_MODE); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_LPA_MODE_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_MODE_TITLE; ?></b></td>
                                      <td><?php echo renderModeSelect('configuration[MODULE_PAYMENT_AM_APA_MODE]', MODULE_PAYMENT_AM_APA_MODE); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_MODE_DESC; ?></td>
                                    </tr>
                                     <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_PROVOCATION_TITLE; ?></b></td>
                                      <td><?php echo renderProvocationSelect('configuration[MODULE_PAYMENT_AM_APA_PROVOCATION]', MODULE_PAYMENT_AM_APA_PROVOCATION); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_PROVOCATION_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_POPUP_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[MODULE_PAYMENT_AM_APA_POPUP]', MODULE_PAYMENT_AM_APA_POPUP); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_POPUP_DESC; ?></td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo AMZ_EXCLUDED_SHIPPING_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_EXCLUDED_SHIPPING"); ?></td>
                                      <td>
                                      <?php

                                      $sm = explode(';', str_replace('.php', '', MODULE_SHIPPING_INSTALLED));
                                      $sm = implode(', ', $sm);
                                      echo $sm;

                                      ?>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_EXCLUDED_PRODUCTS_TITLE; ?></b></td>
                                      <td><a href="<?php echo xtc_href_link('amz_configuration.php', 'mode=excludeProducts&KeepThis=true&TB_iframe=true&height=600&width=900', 'SSL'); ?>" class="thickbox" ><?php echo AMZ_EXCLUDED_PRODUCTS_OPEN;?></a></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK_TITLE; ?></b></td>
                                      <td><?php echo xtc_cfg_pull_down_order_statuses(MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK, 'MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK'); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_ORDER_STATUS_OK_DESC; ?></td>
                                    </tr>
                                    
                                    <tr>
                                      <td><b><?php echo AMZ_STATUS_NONAUTHORIZED_TITLE; ?></b></td>
                                      <td><?php echo xtc_cfg_pull_down_order_statuses(AMZ_STATUS_NONAUTHORIZED, 'AMZ_STATUS_NONAUTHORIZED'); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_ALLOW_GUESTS_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[MODULE_PAYMENT_AM_APA_ALLOW_GUESTS]', MODULE_PAYMENT_AM_APA_ALLOW_GUESTS); ?></td>
                                      <td><?php echo MODULE_PAYMENT_AM_APA_ALLOW_GUESTS_DESC; ?></td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo AMZ_AUTHORIZATION_CONFIG_TITLE; ?></b></td>
                                      <td><?php echo renderAuthorizationSelect('configuration[AMZ_AUTHORIZATION_MODE]', AMZ_AUTHORIZATION_MODE); ?></td>
                                      <td><?php echo AMZ_AUTHORIZATION_CONFIG_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_CAPTURE_CONFIG_TITLE; ?></b></td>
                                      <td><?php echo renderCaptureSelect('configuration[AMZ_CAPTURE_MODE]', AMZ_CAPTURE_MODE); ?></td>
                                      <td><?php echo AMZ_CAPTURE_CONFIG_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_SHIPPED_STATUS_TITLE; ?></b></td>
                                      <td><?php echo xtc_cfg_pull_down_order_statuses(AMZ_SHIPPED_STATUS, 'AMZ_SHIPPED_STATUS'); ?></td>
                                      <td><?php echo AMZ_SHIPPED_STATUS_DESC; ?></td>
                                    </tr>
                                     <tr>
                                      <td><b><?php echo AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE]', AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE); ?></td>
                                      <td><?php echo AMZ_CLOSE_ORDER_ON_COMPLETE_CAPTURE_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_DOWNLOAD_ONLY_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[AMZ_DOWNLOAD_ONLY]', AMZ_DOWNLOAD_ONLY); ?></td>
                                      <td><?php echo AMZ_DOWNLOAD_ONLY_DESC; ?></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_SET_SELLER_ORDER_ID_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[AMZ_SET_SELLER_ORDER_ID]', AMZ_SET_SELLER_ORDER_ID); ?></td>
                                      <td><?php echo AMZ_SET_SELLER_ORDER_ID_DESC; ?></td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo AMZ_AGB_ID_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_AGB_ID"); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_REVOCATION_ID_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_REVOCATION_ID"); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                    <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" bgcolor="#741212" align="right"><span style="font-weight:bold;font-size:3em;color:white;font-size:150%"><?php echo AMZ_HEADING_DESIGN_SETTINGS; ?></span></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_AGB_DISPLAY_MODE_TITLE; ?></b></td>
                                      <td><?php echo renderLegalDisplaySelect('configuration[AMZ_AGB_DISPLAY_MODE]', AMZ_AGB_DISPLAY_MODE); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_BUTTON_SIZE_TITLE; ?></b></td>
                                      <td><?php echo renderButtonSizeSelect("configuration[AMZ_BUTTON_SIZE]", AMZ_BUTTON_SIZE); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_BUTTON_SIZE_TITLE_LPA; ?></b></td>
                                      <td><?php echo renderButtonSizeLpaSelect("configuration[AMZ_BUTTON_SIZE_LPA]", AMZ_BUTTON_SIZE_LPA); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_SHOW_ON_CHECKOUT_PAYMENT_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[AMZ_SHOW_ON_CHECKOUT_PAYMENT]', AMZ_SHOW_ON_CHECKOUT_PAYMENT); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>

                                    <tr>
                                      <td><b><?php echo AMZ_BUTTON_COLOR_TITLE; ?></b></td>
                                      <td><?php echo renderButtonColorSelect("configuration[AMZ_BUTTON_COLOR]", AMZ_BUTTON_COLOR); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_BUTTON_COLOR_TITLE_LPA; ?></b></td>
                                      <td><?php echo renderButtonColorLpaSelect("configuration[AMZ_BUTTON_COLOR_LPA]", AMZ_BUTTON_COLOR_LPA); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    
                                    
                                    <tr>
                                      <td><b><?php echo AMZ_BUTTON_TYPE_LOGIN_TITLE; ?></b></td>
                                      <td><?php echo renderButtonTypeLoginSelect("configuration[AMZ_BUTTON_TYPE_LOGIN]", AMZ_BUTTON_TYPE_LOGIN); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_BUTTON_TYPE_PAY_TITLE; ?></b></td>
                                      <td><?php echo renderButtonTypePaySelect("configuration[AMZ_BUTTON_TYPE_PAY]", AMZ_BUTTON_TYPE_PAY); ?></td>
                                      <td><?php echo AMZ_BUTTON_TYPE_PAY_DESC; ?></td>
                                    </tr>
                                     <tr>
                                      <td><b><?php echo AMZ_TEMPLATE_TITLE; ?></b></td>
                                      <td><?php echo renderTemplateSelect("configuration[AMZ_TEMPLATE]", AMZ_TEMPLATE); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
									<tr>
                                    <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" bgcolor="#741212" align="right"><span style="font-weight:bold;font-size:3em;color:white;font-size:150%"><?php echo AMZ_HEADING_IPN_SETTINGS; ?></span></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_IPN_STATUS_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[MODULE_PAYMENT_AM_APA_IPN_STATUS]', MODULE_PAYMENT_AM_APA_IPN_STATUS); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_IPN_PW_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_IPN_PW"); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_IPN_URL_TITLE; ?></b></td>
                                      <td><?php echo HTTPS_CATALOG_SERVER.DIR_WS_CATALOG.'checkout_amazon_callback.php?pw='.AMZ_IPN_PW; ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                    <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" bgcolor="#741212" align="right"><span style="font-weight:bold;font-size:3em;color:white;font-size:150%"><?php echo AMZ_HEADING_CRONJOB_SETTINGS; ?></span></td>
                                    </tr>
                                     <tr>
                                      <td><b><?php echo MODULE_PAYMENT_AM_APA_CRON_STATUS_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[MODULE_PAYMENT_AM_APA_CRON_STATUS]', MODULE_PAYMENT_AM_APA_CRON_STATUS); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_CRON_PW_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_CRON_PW"); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_CRON_URL_TITLE; ?></b></td>
                                      <td><?php echo HTTP_SERVER.DIR_WS_CATALOG.'checkout_amazon_cron.php?pw='.AMZ_CRON_PW; ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                    <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" bgcolor="#741212" align="right"><span style="font-weight:bold;font-size:3em;color:white;font-size:150%"><?php echo AMZ_HEADING_MAIL_SETTINGS; ?></span></td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_PAYMENT_EMAIL_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_PAYMENT_EMAIL"); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_PAYMENT_NAME_TITLE; ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_PAYMENT_NAME"); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><b><?php echo AMZ_SEND_MAILS_ON_DECLINE_TITLE; ?></b></td>
                                      <td><?php echo renderBoolSelect('configuration[AMZ_SEND_MAILS_ON_DECLINE]', AMZ_SEND_MAILS_ON_DECLINE); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                 <?php
                                 $languages = xtc_get_languages();
                                 foreach ($languages AS $l) {
                                 ?>
                                  <tr>
                                      <td><b><?php echo AMZ_SOFT_DECLINE_SUBJECT_TITLE.' '.xtc_image(DIR_WS_LANGUAGES . $l['directory'] .'/admin/images/'. $l['image'], $l['name']); ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_SOFT_DECLINE_SUBJECT_".strtoupper($l['directory'])); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                 <?php
                                 }
                                 ?>
                                 <?php
                                 $languages = xtc_get_languages();
                                 foreach ($languages AS $l) {
                                 ?>
                                  <tr>
                                      <td><b><?php echo AMZ_HARD_DECLINE_SUBJECT_TITLE.' '.xtc_image(DIR_WS_LANGUAGES . $l['directory'] .'/admin/images/'. $l['image'], $l['name']); ?></b></td>
                                      <td><?php echo renderStandardInput("AMZ_HARD_DECLINE_SUBJECT_".strtoupper($l['directory'])); ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                 <?php
                                 }
                                 ?>


                            </table>
                        <button type="submit" class="amzButton" onclick="this.blur();"><?php echo AMZ_SAVE; ?></button>                </form>

    <?php } ?>


</td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php
 if($_GET["mode"] != 'excludeProducts'){
    require(DIR_WS_INCLUDES . 'footer.php');
 }else{
    echo '</div>';
}
?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php');

function renderBoolSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'True', 'text'=>AMZ_YES), array('id'=>'False', 'text'=>AMZ_NO));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}
function renderLpaModeSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'pay', 'text'=>'Pay'),
				 array('id'=>'login', 'text'=>'Login'), 
				 array('id'=>'login_pay', 'text'=>'Login &amp; Pay'),
				 );
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}
function renderModeSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'sandbox', 'text'=>AMZ_SANDBOX), array('id'=>'live', 'text'=>AMZ_LIVE));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}
function renderTemplateSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'1', 'text'=>AMZ_TEMPLATE_1), array('id'=>'2', 'text'=>AMZ_TEMPLATE_2));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}


function renderProvocationSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'no', 'text'=>'nein'), array('id'=>'hard_decline', 'text'=>'Hard Decline'), array('id'=>'soft_decline', 'text'=>'Soft Decline (2min)'), array('id'=>'capture_decline', 'text'=>'Capture Decline'));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderAuthorizationSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'fast_auth', 'text'=>AMZ_FAST_AUTH), array('id'=>'after_checkout', 'text'=>AMZ_AUTH_AFTER_CHECKOUT), array('id'=>'manually', 'text'=>AMZ_MANUALLY));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderCaptureSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'after_auth', 'text'=>AMZ_CAPTURE_AFTER_AUTH), array('id'=>'after_shipping', 'text'=>AMZ_AFTER_SHIPPING), array('id'=>'manually', 'text'=>AMZ_MANUALLY));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderButtonSizeSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'medium', 'text'=>AMZ_BUTTON_SIZE_MEDIUM), array('id'=>'large', 'text'=>AMZ_BUTTON_SIZE_LARGE), array('id'=>'x-large', 'text'=>AMZ_BUTTON_SIZE_XLARGE));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderButtonSizeLpaSelect($name, $value, $params = ''){

    $arr = array(array('id' => 'small', 'text' => AMZ_BUTTON_SIZE_SMALL),array('id'=>'medium', 'text'=>AMZ_BUTTON_SIZE_MEDIUM), array('id'=>'large', 'text'=>AMZ_BUTTON_SIZE_LARGE), array('id'=>'x-large', 'text'=>AMZ_BUTTON_SIZE_XLARGE));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}
function renderButtonColorSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'orange', 'text'=>AMZ_BUTTON_COLOR_ORANGE), array('id'=>'tan', 'text'=>AMZ_BUTTON_COLOR_TAN));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderButtonColorLpaSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'Gold', 'text'=>AMZ_BUTTON_COLOR_ORANGE), array('id'=>'LightGray', 'text'=>AMZ_BUTTON_COLOR_TAN_LIGHT),  array('id'=>'DarkGray', 'text'=>AMZ_BUTTON_COLOR_TAN_DARK));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderButtonTypeLoginSelect($name, $value, $params = ''){

    $arr = array(array('id'=>'LwA', 'text'=> AMZ_BUTTON_TYPE_LOGIN_LWA), array('id'=>'Login', 'text'=>AMZ_BUTTON_TYPE_LOGIN_LOGIN),  array('id'=>'A', 'text'=>AMZ_BUTTON_TYPE_LOGIN_A));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderButtonTypePaySelect($name, $value, $params = ''){

    $arr = array(array('id'=>'PwA', 'text'=> AMZ_BUTTON_TYPE_PAY_PWA), array('id'=>'Pay', 'text'=>AMZ_BUTTON_TYPE_PAY_PAY),  array('id'=>'A', 'text'=>AMZ_BUTTON_TYPE_PAY_A));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}

function renderLegalDisplaySelect($name, $value, $params = ''){

    $arr = array(array('id'=>'Short', 'text'=>AMZ_AGB_DISPLAY_MODE_SHORT), array('id'=>'Long', 'text'=>AMZ_AGB_DISPLAY_MODE_LONG));
    return xtc_draw_pull_down_menu($name, $arr, $value, $params);

}


function renderStandardInput($key){
    return '<input name="configuration['.$key.']" value="'. (@constant($key) ? constant($key) : '') .'" style="width:350px;"/>';

}

function renderStandardInputPassword($key){
	return '<input name="configuration['.$key.']" type="password" value="'.constant($key).'" style="width:350px;"/>';

}


?>

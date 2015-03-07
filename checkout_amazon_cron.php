<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   checkout_amazon_cron.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
?><?php
@ini_set('max_execution_time', 180);
include ('includes/application_top.php');
include_once('AmazonAdvancedPayment/.config.inc.php');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonTransactions.class.php');
require_once(DIR_FS_CATALOG.'AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
echo '<pre>';
if(MODULE_PAYMENT_AM_APA_CRON_STATUS == 'True' && $_GET["pw"] == AMZ_CRON_PW && MODULE_PAYMENT_AM_APA_STATUS == 'True'){
    if(AMZ_CAPTURE_MODE=='after_shipping'){
        AlkimAmazonHandler::shippingCapture();
    }
    $q = "SELECT * FROM amz_transactions WHERE amz_tx_status = 'Pending' ORDER BY amz_tx_last_update ASC";
    $rs = xtc_db_query($q);
    while($r = xtc_db_fetch_array($rs)){
        AlkimAmazonTransactions::intelligentRefresh($r);
        sleep(1);
    }

    $q = "SELECT * FROM amz_transactions WHERE amz_tx_status != 'Closed' AND amz_tx_status != 'Declined' AND amz_tx_status != 'Completed' ORDER BY amz_tx_last_update ASC LIMIT 40";
    $rs = xtc_db_query($q);
    while($r = xtc_db_fetch_array($rs)){
        AlkimAmazonTransactions::intelligentRefresh($r);
        sleep(1);
    }
    echo 'COMPLETED';
}

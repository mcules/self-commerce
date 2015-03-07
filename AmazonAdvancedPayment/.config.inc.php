<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   .config.inc.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
?><?php

    define('AWS_ACCESS_KEY_ID', MODULE_PAYMENT_AM_APA_ACCESKEY);
    define('AWS_SECRET_ACCESS_KEY', MODULE_PAYMENT_AM_APA_SECRETKEY);
	define('AWS_MERCHANT_ID', MODULE_PAYMENT_AM_APA_MERCHANTID);
	define('AMZ_FILE_VERSION', '2.1.0');
    define('MODULE_PAYMENT_AM_APA_MARKETPLACEID', 'A1OCY9REWJOCW5');
    set_include_path(DIR_FS_DOCUMENT_ROOT . 'AmazonAdvancedPayment/' . PATH_SEPARATOR . get_include_path() . PATH_SEPARATOR);

 	function amzAutoload($className){
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach($includePaths as $includePath){
			if(@file_exists($filePath)){
			    require_once $filePath;
                return;
            }elseif(@file_exists($includePath.'/'.$filePath)){
                require_once $includePath.$filePath;
                return;
            }
        }
    }
    spl_autoload_register('amzAutoload');

    function prepareMoneyInfo($str) {
	return mb_convert_encoding($str, "UTF-8", "ISO-8859-15");
}

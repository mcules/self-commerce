<?php
  /* --------------------------------------------------------------
   $Id: install_step5.php 1252 2005-09-27 22:20:09Z matthias $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (install_step5.php,v 1.25 2003/08/24); www.nextcommerce.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

   require('includes/application.php');

   include('language/'.$_SESSION['language'].'.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Self-Commerce Installer - STEP 4 / Webserver Configuration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.messageStackError, .messageStackWarning { font-family: Verdana, Arial, sans-serif; font-weight: bold; font-size: 10px; background-color: #; }
.mainTable	{width: 900px; margin-top: 0px;}
.logo {background-image: url(images/logo.png); background-repeat: no-repeat; width: 283px; height: 100px;}
.code {background-image: url(images/install.png); background-repeat: no-repeat; background-position: left; width: 617px; background-color: #d9e7f9;}
.frame1 {border: 1px solid #d9e7f9; border-top: 0px; background-color: #EFF6FC;}
.blocktitle {border-top: 1px solid #d9e7f9; border-left: 1px solid #cbcfde; border-right: 1px solid #cbcfde; border-bottom: 1px solid #cbcfde; height: 17px; background-color: #ffffff; font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; color: #999999; font-weight: bold;}
.left_top {background-color: #d9e7f9; border-bottom: 1px solid #cbcfde; font-family: Verdana, Arial, san-serif; font-size: 10px; padding: 10px;}
.left_top2 {background-color: #d9e7f9; font-family: Verdana, Arial, san-serif; font-size: 10px; padding: 10px;}
.frame2 {border: 1px solid #d9e7f9; border-left: 0px;}
.green {border: 1px solid #66CC33; background-color: #CCFF99; padding: 2px;}
.red {border: 1px solid #ff6600; background-color: #FFCC99; padding: 2px;}
.h1 {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 12px;}
.h1:hover {color: #ff6600;}
h2 {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; font-weight: normal;}
.lineBlue {border: 1px solid #cbcfde;}
.lineRed {border: 1px solid #ff6600;}
.lineBottom {border-bottom: 1px solid #ff6600;}
.lineGreen {border: 1px solid #66cc33;}
.warning {background-color: #cbcfde;}
.small {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px;}
.normal {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 11px;}
.title {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 12px; font-weight: bold; background-image: url(images/icons/title.gif); background-repeat: no-repeat; padding-left: 20px;}
.welcome {text-align: left; padding: 15px 15px 15px 15px;}
.note {color: #5a5a5a;}
.noteBox {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; background-color: #FFCC99; padding: 2px;}
UL.liste {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; color: #ff6600; list-style: none; padding: 2px;}
-->
</style>
</head>
<body>
<table class="mainTable" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr> 
		<td colspan="2" >
    		
    		<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
          			<td class="logo"></td>
          			<td class="code"></td>
        		</tr>
      		</table>
      		
      	</td>
	</tr>
	<tr> 
		<td class="frame1" width="180" valign="top" >
      		<table width="180" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2" height="17" class="blocktitle" align="center">Self-Commerce Install</td>
				</tr>
        		<tr> 
					<td class="left_top2" width="135" ><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_LANGUAGE; ?></td>
                	<td class="left_top2" width="35"><img src="images/icons/ok.gif"></td>
              	</tr>
              	<tr> 
                	<td class="left_top2" width="135" ><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_DB_CONNECTION; ?></td>
               		<td class="left_top2" width="35"><img src="images/icons/ok.gif"></td>
              	</tr>
              	<tr> 
                	<td class="left_top2">&nbsp;&nbsp;&nbsp;<img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_DB_IMPORT; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif"></td>
              	</tr>
              	<tr> 
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_WEBSERVER_SETTINGS; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif"></td>
                </tr>
              	<tr>
                	<td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_WRITE_CONFIG; ?></td>
	            </tr>
           </table>
		</td>
		<td align="right" valign="top" class="frame2" width="100%">
			<h2 class="welcome"><?php echo TEXT_WELCOME_STEP5; ?></h2><hr class="lineBlue"> 
				<table width="98%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<span class="title"><?php echo TITLE_WEBSERVER_CONFIGURATION; ?></span><hr class="lineRed">

							<?php
							  $db = array();
							  $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
							  $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
							  $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
							  $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
							  $db_error = false;
							  xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);
							  if (!$db_error) {
							    xtc_db_test_connection($db['DB_DATABASE']);
							  }
							  if ($db_error) {
							?>
							<h2 class="normal"><img src="images/icons/error.gif" width="16" height="16"><strong><?php echo TEXT_CONNECTION_ERROR; ?></strong></h2><hr class="lineRed">
          					<p class="small"><strong><?php echo TEXT_DB_ERROR; ?></strong></p>
							<p class="h1 warning"><strong><?php echo $db_error; ?></strong></p>
							<p class="small"><?php echo TEXT_DB_ERROR_1; ?></p>
							<p class="small"><?php echo TEXT_DB_ERROR_2; ?></p>
							<form name="install" action="install_step4.php" method="post">
							<?php
							    reset($_POST);
							    while (list($key, $value) = each($_POST)) {
							      if ($key != 'x' && $key != 'y') {
							        if (is_array($value)) {
							          for ($i=0; $i<sizeof($value); $i++) {
							            echo xtc_draw_hidden_field_installer($key . '[]', $value[$i]);
							          }
							        } else {
							          echo xtc_draw_hidden_field_installer($key, $value);
							        }
							      }
							    }
							?>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
							  	<tr>
							    	<td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a></td>
							    	<td align="center"><input type="image" src="images/button_back.gif" border="0" alt="Back"></td>
							  	</tr>
							</table>
							</form>
<?php
  } else {
    $file_contents = '<?php' . "\n" .
                     '/* --------------------------------------------------------------' . "\n" .
                     '' . "\n" .
                     '  Self-Commerce - Fresh up your eCommerce' . "\n" .
                     '  http://www.self-commerce.de' . "\n" .
                     '' . "\n" .
                     '   Copyright (c) 2007 SELF-Commerce' . "\n" .
                     '  --------------------------------------------------------------' . "\n" .
                     '  based on:' . "\n" . 
                     '  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)' . "\n" . 
                     '  (c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com' . "\n" .
                     '  (c) 2004- XT:Commerce (configure.php); www.xtcommerce.com' . "\n" .                      
                     '' . "\n" .
                     '  Released under the GNU General Public License' . "\n" . 
                     '  --------------------------------------------------------------*/' . "\n" .
                     '' . "\n" .
                     '// Define the webserver and path parameters' . "\n" .
                     '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
                     '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
                     '  define(\'HTTP_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\'); // eg, http://localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'HTTPS_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\'); // eg, https://localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'ENABLE_SSL\', ' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '); // secure webserver for checkout procedure?' . "\n" .
                     '  define(\'DIR_WS_CATALOG\', \'' . $_POST['DIR_WS_CATALOG'] . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_FS_DOCUMENT_ROOT\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\');' . "\n" .
                     '  define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
                     '  define(\'DIR_WS_ORIGINAL_IMAGES\', DIR_WS_IMAGES .\'product_images/original_images/\');' . "\n" .
                     '  define(\'DIR_WS_THUMBNAIL_IMAGES\', DIR_WS_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                     '  define(\'DIR_WS_INFO_IMAGES\', DIR_WS_IMAGES .\'product_images/info_images/\');' . "\n" .
                     '  define(\'DIR_WS_POPUP_IMAGES\', DIR_WS_IMAGES .\'product_images/popup_images/\');' . "\n" .
                     '  define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
                     '  define(\'DIR_WS_INCLUDES\',DIR_FS_DOCUMENT_ROOT. \'includes/\');' . "\n" .
                     '  define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
                     '  define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
                     '  define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
                     '  define(\'DIR_WS_LANGUAGES\', DIR_FS_CATALOG . \'lang/\');' . "\n" .
                     '' . "\n" .
                     '  define(\'DIR_WS_DOWNLOAD_PUBLIC\', DIR_WS_CATALOG . \'pub/\');' . "\n" .
                     '  define(\'DIR_FS_DOWNLOAD\', DIR_FS_CATALOG . \'download/\');' . "\n" .
                     '  define(\'DIR_FS_DOWNLOAD_PUBLIC\', DIR_FS_CATALOG . \'pub/\');' . "\n" .
                     '  define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
                     '' . "\n" .
                     '// define our database connection' . "\n" .
                     '  define(\'DB_SERVER\', \'' . $_POST['DB_SERVER'] . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'DB_SERVER_USERNAME\', \'' . $_POST['DB_SERVER_USERNAME'] . '\');' . "\n" .
                     '  define(\'DB_SERVER_PASSWORD\', \'' . $_POST['DB_SERVER_PASSWORD']. '\');' . "\n" .
                     '  define(\'DB_DATABASE\', \'' . $_POST['DB_DATABASE']. '\');' . "\n" .
                     '  define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
                     '  define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); // leave empty \'\' for default handler or set to \'mysql\'' . "\n" .                     '?>';
    $fp = fopen(DIR_FS_CATALOG . 'includes/configure.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);

    $file_contents = '<?php' . "\n" .
                     '/* --------------------------------------------------------------' . "\n" .
                     '  ### Be careful, this is the backup of your original configuration data ###' . "\n" .
                     '' . "\n" .
                     '  Self-Commerce - Fresh up your eCommerce' . "\n" .
                     '  http://www.self-commerce.de' . "\n" .
                     '' . "\n" .
                     '   Copyright (c) 2007 SELF-Commerce' . "\n" .
                     '  --------------------------------------------------------------' . "\n" .
                     '  based on:' . "\n" . 
                     '  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)' . "\n" . 
                     '  (c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com' . "\n" .
                     '  (c) 2004- XT:Commerce (configure.php); www.xtcommerce.com' . "\n" .                      
                     '' . "\n" .
                     '  Released under the GNU General Public License' . "\n" .
                     '  --------------------------------------------------------------*/' . "\n" .
                     '' . "\n" .
                     '// Define the webserver and path parameters' . "\n" .
                     '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
                     '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
                     '  define(\'HTTP_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\'); // eg, http://localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'HTTPS_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\'); // eg, https://localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'ENABLE_SSL\', ' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '); // secure webserver for checkout procedure?' . "\n" .
                     '  define(\'DIR_WS_CATALOG\', \'' . $_POST['DIR_WS_CATALOG'] . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_FS_DOCUMENT_ROOT\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\');' . "\n" .
                     '  define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
                     '  define(\'DIR_WS_ORIGINAL_IMAGES\', DIR_WS_IMAGES .\'product_images/original_images/\');' . "\n" .
                     '  define(\'DIR_WS_THUMBNAIL_IMAGES\', DIR_WS_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                     '  define(\'DIR_WS_INFO_IMAGES\', DIR_WS_IMAGES .\'product_images/info_images/\');' . "\n" .
                     '  define(\'DIR_WS_POPUP_IMAGES\', DIR_WS_IMAGES .\'product_images/popup_images/\');' . "\n" .
                     '  define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
                     '  define(\'DIR_WS_INCLUDES\',DIR_FS_DOCUMENT_ROOT. \'includes/\');' . "\n" .
                     '  define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
                     '  define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
                     '  define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
                     '  define(\'DIR_WS_LANGUAGES\', DIR_FS_CATALOG . \'lang/\');' . "\n" .
                     '' . "\n" .
                     '  define(\'DIR_WS_DOWNLOAD_PUBLIC\', DIR_WS_CATALOG . \'pub/\');' . "\n" .
                     '  define(\'DIR_FS_DOWNLOAD\', DIR_FS_CATALOG . \'download/\');' . "\n" .
                     '  define(\'DIR_FS_DOWNLOAD_PUBLIC\', DIR_FS_CATALOG . \'pub/\');' . "\n" .
                     '  define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
                     '' . "\n" .
                     '// define our database connection' . "\n" .
                     '  define(\'DB_SERVER\', \'' . $_POST['DB_SERVER'] . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'DB_SERVER_USERNAME\', \'' . $_POST['DB_SERVER_USERNAME'] . '\');' . "\n" .
                     '  define(\'DB_SERVER_PASSWORD\', \'' . $_POST['DB_SERVER_PASSWORD']. '\');' . "\n" .
                     '  define(\'DB_DATABASE\', \'' . $_POST['DB_DATABASE']. '\');' . "\n" .
                     '  define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
                     '  define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); // leave empty \'\' for default handler or set to \'mysql\'' . "\n" .
                     '?>';
    $fp = fopen(DIR_FS_CATALOG . 'includes/configure.org.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);
//create a configure.php
    $file_contents = '<?php' . "\n" .
                     '/* --------------------------------------------------------------' . "\n" .
                     '' . "\n" .
                     '  Self-Commerce - Fresh up your eCommerce' . "\n" .
                     '  http://www.self-commerce.de' . "\n" .
                     '' . "\n" .
                     '   Copyright (c) 2007 SELF-Commerce' . "\n" .
                     '  --------------------------------------------------------------' . "\n" .
                     '  based on:' . "\n" . 
                     '  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)' . "\n" . 
                     '  (c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com' . "\n" .
                     '  (c) 2004- XT:Commerce (configure.php); www.xtcommerce.com' . "\n" .                      
                     '' . "\n" .
                     '  Released under the GNU General Public License' . "\n" . 
                     '  --------------------------------------------------------------*/' . "\n" .
                     '' . "\n" .
                     '// Define the webserver and path parameters' . "\n" .
                     '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
                     '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
                     '  define(\'HTTP_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\'); // eg, http://localhost or - https://localhost should not be empty for productive servers' . "\n" .
                     '  define(\'HTTP_CATALOG_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\');' . "\n" .
                     '  define(\'HTTPS_CATALOG_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\');' . "\n" .
                     '  define(\'ENABLE_SSL_CATALOG\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '\'); // secure webserver for catalog module' . "\n" .
                     '  define(\'DIR_FS_DOCUMENT_ROOT\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\'); // where the pages are located on the server' . "\n" .
                     '  define(\'DIR_WS_ADMIN\', \'' . $_POST['DIR_WS_CATALOG'] .'admin/' . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_FS_ADMIN\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path .'admin/' . '\'); // absolute pate required' . "\n" .
                     '  define(\'DIR_WS_CATALOG\', \'' . $_POST['DIR_WS_CATALOG'] . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_FS_CATALOG\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_IMAGES\', DIR_FS_CATALOG . \'images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_ORIGINAL_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/original_images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_THUMBNAIL_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_INFO_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/info_images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_POPUP_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/popup_images/\');' . "\n" .
                     '  define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_IMAGES\', DIR_WS_CATALOG . \'images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_ORIGINAL_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/original_images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_THUMBNAIL_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_INFO_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/info_images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_POPUP_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/popup_images/\');' . "\n" .
                     '  define(\'DIR_WS_INCLUDES\', \'includes/\');' . "\n" .
                     '  define(\'DIR_WS_BOXES\', DIR_WS_INCLUDES . \'boxes/\');' . "\n" .
                     '  define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
                     '  define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
                     '  define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
                     '  define(\'DIR_WS_LANGUAGES\', DIR_WS_CATALOG. \'lang/\');' . "\n" .
                     '  define(\'DIR_FS_LANGUAGES\', DIR_FS_CATALOG. \'lang/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_MODULES\', DIR_FS_CATALOG . \'includes/modules/\');' . "\n" .
                     '  define(\'DIR_FS_BACKUP\', DIR_FS_ADMIN . \'backups/\');' . "\n" .
                     '  define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
                     '  define(\'DIR_WS_FILEMANAGER\', DIR_WS_MODULES . \'fckeditor/editor/filemanager/browser/default/\');' . "\n" .
                     '' . "\n" .
                     '// define our database connection' . "\n" .
                     '  define(\'DB_SERVER\', \'' . $_POST['DB_SERVER'] . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'DB_SERVER_USERNAME\', \'' . $_POST['DB_SERVER_USERNAME'] . '\');' . "\n" .
                     '  define(\'DB_SERVER_PASSWORD\', \'' . $_POST['DB_SERVER_PASSWORD']. '\');' . "\n" .
                     '  define(\'DB_DATABASE\', \'' . $_POST['DB_DATABASE']. '\');' . "\n" .
                     '  define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persisstent connections?' . "\n" .
                     '  define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); // leave empty \'\' for default handler or set to \'mysql\'' . "\n" .
                     '' . "\n" .
 '?>';
    $fp = fopen(DIR_FS_CATALOG . 'admin/includes/configure.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);


//Create a backup of the original configure    
    $file_contents = '<?php' . "\n" .
                     '/* --------------------------------------------------------------' . "\n" .
                     '  ### Be careful, this is the backup of your original configuration data ###' . "\n" .
                     '' . "\n" .
                     '  Self-Commerce - Fresh up your eCommerce' . "\n" .
                     '  http://www.self-commerce.de' . "\n" .
                     '' . "\n" .
                     '   Copyright (c) 2007 SELF-Commerce' . "\n" .
                     '  --------------------------------------------------------------' . "\n" .
                     '  based on:' . "\n" . 
                     '  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)' . "\n" . 
                     '  (c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com' . "\n" .
                     '  (c) 2004- XT:Commerce (configure.php); www.xtcommerce.com' . "\n" .                      
                     '' . "\n" .
                     '  Released under the GNU General Public License' . "\n" .
                     '  --------------------------------------------------------------*/' . "\n" .
                     '' . "\n" .
                     '// Define the webserver and path parameters' . "\n" .
                     '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
                     '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
                     '  define(\'HTTP_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\'); // eg, http://localhost or - https://localhost should not be empty for productive servers' . "\n" .
                     '  define(\'HTTP_CATALOG_SERVER\', \'' . $_POST['HTTP_SERVER'] . '\');' . "\n" .
                     '  define(\'HTTPS_CATALOG_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\');' . "\n" .
                     '  define(\'ENABLE_SSL_CATALOG\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '\'); // secure webserver for catalog module' . "\n" .
                     '  define(\'DIR_FS_DOCUMENT_ROOT\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\'); // where the pages are located on the server' . "\n" .
                     '  define(\'DIR_WS_ADMIN\', \'' . $_POST['DIR_WS_CATALOG'] .'admin/' . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_FS_ADMIN\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path .'admin/' . '\'); // absolute pate required' . "\n" .
                     '  define(\'DIR_WS_CATALOG\', \'' . $_POST['DIR_WS_CATALOG'] . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_FS_CATALOG\', \'' . $_SERVER['DOCUMENT_ROOT'].$local_install_path  . '\'); // absolute path required' . "\n" .
                     '  define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_IMAGES\', DIR_FS_CATALOG . \'images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_ORIGINAL_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/original_images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_THUMBNAIL_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_INFO_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/info_images/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_POPUP_IMAGES\', DIR_FS_CATALOG_IMAGES .\'product_images/popup_images/\');' . "\n" .
                     '  define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_IMAGES\', DIR_WS_CATALOG . \'images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_ORIGINAL_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/original_images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_THUMBNAIL_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/thumbnail_images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_INFO_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/info_images/\');' . "\n" .
                     '  define(\'DIR_WS_CATALOG_POPUP_IMAGES\', DIR_WS_CATALOG_IMAGES .\'product_images/popup_images/\');' . "\n" .
                     '  define(\'DIR_WS_INCLUDES\', \'includes/\');' . "\n" .
                     '  define(\'DIR_WS_BOXES\', DIR_WS_INCLUDES . \'boxes/\');' . "\n" .
                     '  define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
                     '  define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
                     '  define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
                     '  define(\'DIR_WS_LANGUAGES\', DIR_WS_CATALOG. \'lang/\');' . "\n" .
                     '  define(\'DIR_FS_LANGUAGES\', DIR_FS_CATALOG. \'lang/\');' . "\n" .
                     '  define(\'DIR_FS_CATALOG_MODULES\', DIR_FS_CATALOG . \'includes/modules/\');' . "\n" .
                     '  define(\'DIR_FS_BACKUP\', DIR_FS_ADMIN . \'backups/\');' . "\n" .
                     '  define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
                     '  define(\'DIR_WS_FILEMANAGER\', DIR_WS_MODULES . \'fckeditor/editor/filemanager/browser/default/\');' . "\n" .
                     '' . "\n" .
                     '// define our database connection' . "\n" .
                     '  define(\'DB_SERVER\', \'' . $_POST['DB_SERVER'] . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
                     '  define(\'DB_SERVER_USERNAME\', \'' . $_POST['DB_SERVER_USERNAME'] . '\');' . "\n" .
                     '  define(\'DB_SERVER_PASSWORD\', \'' . $_POST['DB_SERVER_PASSWORD']. '\');' . "\n" .
                     '  define(\'DB_DATABASE\', \'' . $_POST['DB_DATABASE']. '\');' . "\n" .
                     '  define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persisstent connections?' . "\n" .
                     '  define(\'STORE_SESSIONS\', \'' . (($_POST['STORE_SESSIONS'] == 'files') ? '' : 'mysql') . '\'); // leave empty \'\' for default handler or set to \'mysql\'' . "\n" .
                     '' . "\n" .

 '?>';
    
    $fp = fopen(DIR_FS_CATALOG . 'admin/includes/configure.org.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);

?>
						<center>
						<img src="images/logo_step5.png" width="283" height="100"><br />
            			<span class="h1"><?php echo TEXT_WS_CONFIGURATION_SUCCESS; ?></h1> </center>
            			<p align="center"><a href="install_step6.php"><img src="images/button_continue.gif" width="77" height="23" border="0"></a></p>
						</form>
<?php
  }
?>
  
					</td>
				</tr>
			</table>
      
		</td>
	</tr>
</table>
<p class="small" align="center"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>

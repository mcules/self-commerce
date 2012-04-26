<?php
/* --------------------------------------------------------------
   $Id: install_step1.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(install.php,v 1.7 2002/08/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (install_step1.php,v 1.10 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
  
  require('includes/application.php');

  include('language/'.$_SESSION['language'].'.php');
  
  if (!$script_filename = str_replace('\\', '/', getenv('PATH_TRANSLATED'))) {
    $script_filename = getenv('SCRIPT_FILENAME');
  }
  $script_filename = str_replace('//', '/', $script_filename);

  if (!$request_uri = getenv('REQUEST_URI')) {
    if (!$request_uri = getenv('PATH_INFO')) {
      $request_uri = getenv('SCRIPT_NAME');
    }

    if (getenv('QUERY_STRING')) $request_uri .=  '?' . getenv('QUERY_STRING');
  }

  $dir_fs_www_root_array = explode('/', dirname($script_filename));
  $dir_fs_www_root = array();
  for ($i=0; $i<sizeof($dir_fs_www_root_array)-2; $i++) {
    $dir_fs_www_root[] = $dir_fs_www_root_array[$i];
  }
  $dir_fs_www_root = implode('/', $dir_fs_www_root);

  $dir_ws_www_root_array = explode('/', dirname($request_uri));
  $dir_ws_www_root = array();
  for ($i=0; $i<sizeof($dir_ws_www_root_array)-1; $i++) {
    $dir_ws_www_root[] = $dir_ws_www_root_array[$i];
  }
  $dir_ws_www_root = implode('/', $dir_ws_www_root);
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Self-Commerce Installer - STEP 1 / Settings</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.messageStackError, .messageStackWarning { font-family: Verdana, Arial, sans-serif; font-weight: bold; font-size: 10px; background-color: #; }
.mainTable	{width: 900px; margin-top: 0px;}
.logo {background-image: url(images/logo.png); background-repeat: no-repeat; width: 283px; height: 100px;}
.code {background-image: url(images/install.png); background-repeat: no-repeat; background-position: left; width: 617px; background-color: #d9e7f9;}
.frame1 {border: 1px solid #d9e7f9; border-top: 0px; background-color: #EFF6FC;}
.blocktitle {border-top: 1px solid #d9e7f9; border-left: 1px solid #cbcfde; border-right: 1px solid #cbcfde; border-bottom: 1px solid #cbcfde; height: 17px; background-color: #ffffff; font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; color: #999999; font-weight: bold;}
.left_top {background-color: #d9e7f9; border-bottom: 1px solid #cbcfde; font-family: Verdana, Arial, san-serif; font-size: 10px; padding: 20px 10px 20px 10px;}
.left_top2 {background-color: #d9e7f9; font-family: Verdana, Arial, san-serif; font-size: 10px; padding: 20px 10px 20px 10px;}
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
-->
</style>
</head>

<body>
<table class="mainTable" height="80%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr> 
		<td colspan="2" >
    		<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
          			<td class="logo"></td>
          			<td class="code"></td>
        		</tr>
      		</table>
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
                	<td colspan="2" class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_DB_CONNECTION; ?></td>
              	</tr>
              	<tr> 
                	<td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_WEBSERVER_SETTINGS; ?></td>
              	</tr>
			</table>
    	</td>
		<td align="right" valign="top" class="frame2">
			<h2 class="welcome"><?php echo TEXT_WELCOME_STEP1; ?></h2><hr class="lineBlue">
			
			<form name="install" method="post" action="install_step2.php">
            <table width="98%" border="0" cellpadding="0" cellspacing="0">
          		<tr>
    				<td>
    					<span class="title"><?php echo TITLE_CUSTOM_SETTINGS; ?></span><hr class="lineRed">
						<p class="small"><?php echo xtc_draw_checkbox_field_installer('install[]', 'database', true); ?>
                			<strong><?php echo TEXT_IMPORT_DB; ?></strong><br />
                			<?php echo TEXT_IMPORT_DB_LONG; ?></p>
              			<p class="small"><?php echo xtc_draw_checkbox_field_installer('install[]', 'configure', true); ?> 
                			<strong><?php echo TEXT_AUTOMATIC; ?></strong><br />
                			<?php echo TEXT_AUTOMATIC_LONG; ?></p>
					</td>
  				</tr>
		</table>
        <br />
        <hr class="lineBlue">
        <table width="98%" border="0" cellpadding="0" cellspacing="0">
        	<tr> 
            	<td>
              		<span class="title"><?php echo TITLE_DATABASE_SETTINGS; ?></span><hr class="lineRed">
					<p class="small"><strong><?php echo TEXT_DATABASE_SERVER; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DB_SERVER','localhost'); ?><br />
	                	<?php echo TEXT_DATABASE_SERVER_LONG; ?></p>
              		<p class="small"><strong><?php echo TEXT_USERNAME; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DB_SERVER_USERNAME'); ?><br />
	                	<?php echo TEXT_USERNAME_LONG; ?></p>
              		<p class="small"><strong><?php echo TEXT_PASSWORD; ?></strong><br />
                		<?php echo xtc_draw_input_field_installer('DB_SERVER_PASSWORD'); ?><br />
                		<?php echo TEXT_PASSWORD_LONG; ?></p>
              		<p class="small"><strong><?php echo TEXT_DATABASE; ?></strong><br />
                		<?php echo xtc_draw_input_field_installer('DB_DATABASE'); ?><br />
                		<?php echo TEXT_DATABASE_LONG; ?></p>
                </td>
        	</tr>
        </table>
		<br />
        <hr class="lineBlue">
        <table width="98%" border="0" cellpadding="0" cellspacing="0">
        	<tr> 
        		<td>
	        		<span class="title"><?php echo TITLE_WEBSERVER_SETTINGS; ?></span><hr class="lineRed">
	              	<p class="small"><strong><?php echo TEXT_WS_ROOT; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root,'','size=60'); ?><br />
	                	<?php echo TEXT_WS_ROOT_LONG; ?></p>
	              	<p class="small"><strong><?php echo TEXT_WS_XTC; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DIR_FS_CATALOG', $local_install_path,'','size=60'); ?><br />
	                	<?php echo TEXT_WS_XTC_LONG; ?></p>
	              	<p class="small"><strong><?php echo TEXT_WS_ADMIN; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DIR_FS_ADMIN', $local_install_path.'admin/','','size=60'); ?><br />
	               		<?php echo TEXT_WS_ADMIN_LONG; ?></p>
	              	<p class="small"><strong><?php echo TEXT_WS_CATALOG; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DIR_WS_CATALOG', $dir_ws_www_root . '/','','size=60'); ?><br />
	                 	<?php echo TEXT_WS_CATALOG_LONG; ?></p>
	              	<p class="small"><strong><?php echo TEXT_WS_ADMINTOOL; ?></strong><br />
	                	<?php echo xtc_draw_input_field_installer('DIR_WS_ADMIN', $dir_ws_www_root . '/admin/','','size=60'); ?><br />
	                 	<?php echo TEXT_WS_ADMINTOOL_LONG; ?></p>
                 </td>
			</tr>
        </table>
		<br />
		<hr class="lineBlue">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
  			<tr>
			    <td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a></td>
			    <td align="center"><input type="image" src="images/button_continue.gif" border="0" alt="Continue"></td>
  			</tr>
		</table>
		</form>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      
    </td>
  </tr>
</table>
<p class="small" align="center"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>
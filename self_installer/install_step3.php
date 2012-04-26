<?php
  /* --------------------------------------------------------------
   $Id: install_step3.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(install_3.php,v 1.6 2002/08/15); www.oscommerce.com 

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

   require('includes/application.php');

   include('language/'.$_SESSION['language'].'.php'); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Self-Commerce Installer - STEP 3 / DB Import</title>
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
                	<td colspan="2" class="left_top2">&nbsp;&nbsp;&nbsp;<img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_DB_IMPORT; ?></td>
                </tr>
              	<tr> 
                	<td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_WEBSERVER_SETTINGS; ?></td>
	        	</tr>
			</table>
		</td>
    	<td width="100%" align="right" valign="top" class="frame2"> 
      		<h2 class="welcome"><?php echo TEXT_WELCOME_STEP3; ?></h2><hr class="lineBlue">
		    <table width="98%" border="0">
        		<tr>
          			<td> 
						<?php
						  if (xtc_in_array('database', $_POST['install'])) {
						    $db = array();
						    $db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
						    $db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
						    $db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
						    $db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
						
						    xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);
						
						    $db_error = false;
						    $sql_file = DIR_FS_CATALOG . 'self_installer/self_commerce.sql';
						//    $script_filename = (($SCRIPT_FILENAME) ? $SCRIPT_FILENAME : $HTTP_SERVER_VARS['SCRIPT_FILENAME']);
						//    $script_directory = dirname($script_filename);
						//    $sql_file = $script_directory . '/nextcommerce.sql';
						
						//    @xtc_set_time_limit(0);
						    xtc_db_install($db['DB_DATABASE'], $sql_file);
					
					    	if ($db_error) {
						?>
            			<h2 class="normal"><img src="images/icons/error.gif" width="16" height="16">&nbsp;<strong><?php echo TEXT_TITLE_ERROR; ?></strong></h2><hr class="lineRed">
            			<p class="h1 warning"><strong><?php echo $db_error; ?></strong></p>
            			
            			<form name="install" action="install_step3.php" method="post">

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
							    <td align="center"><input type="image" src="images/button_retry.gif" border="0" alt="Retry"></td>
						  	</tr>
						</table>

						</form>

<?php
    } else {
?>
            			<span class="title"><?php echo TEXT_TITLE_SUCCESS; ?></span><hr class="lineRed">
				        
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
						<?php
						      if (xtc_in_array('configure', $_POST['install'])) {
						?>
						    <td align="center"><input type="image" src="images/button_continue.gif" border="0" alt="Continue"></td>
						<?php
						      } else {
						?>
						    <td align="center"><a href="index.php"><img src="images/button_continue.gif" border="0" alt="Continue"></a></td>
						<?php
						      }
						?>
						  </tr>
						</table>

						</form>

<?php
    }
  }
?>
                  
					</td>
				</tr>
			</table>
     
		</td>
	</tr>
</table>
<p align="center" class="small"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>
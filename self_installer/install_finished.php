<?php
  /* --------------------------------------------------------------
   $Id: install_finished.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (install_finished.php,v 1.5 2003/08/17); www.nextcommerce.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
  
  require('includes/application.php');
  require('../admin/includes/configure.php'); 
  
  include('language/'.$_SESSION['language'].'.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>XT-Commerce Installer - Finished</title>
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
.small_strong {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; font-weight: bold;}
.normal {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 11px;}
.title {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 12px; font-weight: bold; background-image: url(images/icons/title.gif); background-repeat: no-repeat; padding-left: 20px;}
.welcome {text-align: left; padding: 15px 15px 15px 15px;}
.note {color: #5a5a5a; font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; padding-left: 2px;}
.noteBox {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; background-color: #FFCC99; padding: 2px;}
UL.liste {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; color: #ff6600; list-style: none; padding: 2px;}
.note_red_title {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; color: #ff6600; font-weight: bold; padding-left: 15px;}
.note_red {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; color: #ff6600; font-weight: bold; padding-left: 2px;}
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
					<td class="left_top2" width="135" ><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_LANGUAGE; ?></td>
                	<td class="left_top2" width="35"><img src="images/icons/ok.gif" alt="OK" /></td>
              	</tr>
              	<tr> 
                	<td class="left_top2" width="135" ><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_DB_CONNECTION; ?></td>
               		<td class="left_top2" width="35"><img src="images/icons/ok.gif" alt="OK" /></td>
              	</tr>
              	<tr> 
                	<td class="left_top2">&nbsp;&nbsp;&nbsp;<img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_DB_IMPORT; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif" alt="OK" /></td>
              	</tr>
              	<tr> 
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_WEBSERVER_SETTINGS; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif" alt="OK" /></td>
                </tr>
              	<tr>
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_WRITE_CONFIG; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif" alt="Ok" /></td>
	            </tr>
	            <tr>
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_ADMIN_CONFIG; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif" alt="Ok" /></td>
	            </tr>
				<tr>
                	<td class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_USERS_CONFIG; ?></td>
                	<td class="left_top"><img src="images/icons/ok.gif" alt="Ok" /></td>
               </tr>
			</table>
		</td>
		<td align="right" valign="top" class="frame2" width="100%">
			<h2 class="welcome"><?php echo TEXT_WELCOME_FINISHED; ?></h2><hr class="lineBlue">
			<table width="98%" border="0" cellpadding="0" cellspacing="0">
        		<tr>
          			<td>
          				<span class="title"><?php echo TITLE_SHOP_CONFIG; ?></span><hr class="lineRed">
            			<p class="h1" align="center"><img src="images/logo_step5.png" width="283" height="100" alt="Logo Step 5" /><br />
	              			<?php echo TEXT_SHOP_CONFIG_SUCCESS; ?><br /><br />
	              			<?php echo TEXT_TEAM; ?><br /><br /><br />
	              			<a href="<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'index.php'; ?>" target="_blank"><img src="images/button_catalog.gif" border="0" alt="Catalog" /></a><br /><br /><br />
	              		</p>
				</tr>
            </table>
		</td>
	</tr>
</table>
<p class="small" align="center"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>
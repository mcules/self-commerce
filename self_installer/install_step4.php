<?php
  /* --------------------------------------------------------------
   $Id: install_step4.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(install_4.php,v 1.9 2002/08/19); www.oscommerce.com
   (c) 2003	 nextcommerce (install_step4.php,v 1.14 2003/08/17); www.nextcommerce.org

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
                	<td colspan="2" class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_WEBSERVER_SETTINGS; ?></td>
                </tr>
              	<tr>
                	<td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" /><?php echo BOX_WRITE_CONFIG; ?></td>
	            </tr>
           </table>
		</td>
		<td align="right" valign="top" class="frame2"> 
      		
      		<h2 class="welcome"><?php echo TEXT_WELCOME_STEP4; ?></h2><hr class="lineBlue" />
			<table width="98%" border="0" cellpadding="0" cellspacing="0">
        		<tr>
          			<td>
          			
	          			<span class="title"><?php echo TITLE_WEBSERVER_CONFIGURATION; ?></span><hr class="lineRed" />
			            <?php
			 				 if ( ( (file_exists(DIR_FS_CATALOG . 'includes/configure.php')) && (!is_writeable(DIR_FS_CATALOG . 'includes/configure.php')) ) || ( (file_exists(DIR_FS_CATALOG . 'admin/includes/configure.php')) && (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.php')) ) || ( (file_exists(DIR_FS_CATALOG . 'admin/includes/local/configure.php')) && (!is_writeable(DIR_FS_CATALOG . 'admin/includes/local/configure.php')) ) || ( (file_exists(DIR_FS_CATALOG . 'includes/local/configure.php')) && (!is_writeable(DIR_FS_CATALOG . 'includes/local/configure.php')) )) {
						?>
	            		<h2 class="normal"><img src="images/icons/error.gif" width="16" height="16" alt="Error" /><strong><font color="#FF0000" size="2"><?php echo TITLE_STEP4_ERROR; ?></font></strong></h2><hr class="lineRed" />
	            		<p class="small"><?php echo TEXT_STEP4_ERROR; ?>
			            	<ul class="liste">
				                <li>cd <?php echo DIR_FS_CATALOG; ?>admin/includes/</li>
				                <li>touch configure.php</li>
				                <li>chmod 706 configure.php</li>
				                <li>chmod 706 configure.org.php</li>
			              	</ul>
              				<ul class="liste">
				                <li>cd <?php echo DIR_FS_CATALOG; ?>includes/</li>
				                <li>touch configure.php</li>
								<li>chmod 706 configure.php</li>
                  				<li>chmod 706 configure.org.php</li>
              				</ul>
            			
            
						<p class="noteBox"><?php echo TEXT_STEP4_ERROR_1; ?></p>
			            <p class="noteBox"><?php echo TEXT_STEP4_ERROR_2; ?></p>
            
						<form name="install" action="install_step4.php" method="post">
							<p class="small">
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
							</p>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
			                	<tr> 
			                  		<td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel" /></a></td>
			                  		<td align="center"><input type="image" src="images/button_retry.gif" alt="Retry" /></td>
			                	</tr>
			              	</table>
						</form>
            
<?php
  } else {
?>
            
						<form name="install" action="install_step5.php" method="post">
							<p class="small"><?php echo TEXT_VALUES; ?><br /><br />
				                includes/configure.php<br />
				                includes/configure.org.php<br />
				                admin/includes/configure.php<br />
				                admin/includes/configure.org.php<br />
							</p>
							<span class="title"><?php echo TITLE_CHECK_CONFIGURATION; ?></span><hr class="lineRed" />
				            <p class="small"><strong><?php echo TEXT_HTTP; ?></strong><br />
				            	<?php echo xtc_draw_input_field_installer('HTTP_SERVER', 'http://' . getenv('HTTP_HOST')); ?><br />
				                <span class="note"><?php echo TEXT_HTTP_LONG; ?></span></p>
				            <p class="small"><strong><?php echo TEXT_HTTPS; ?></strong><br />
				                <?php echo xtc_draw_input_field_installer('HTTPS_SERVER', 'https://' . getenv('HTTP_HOST')); ?><br />
				                <span class="note"><?php echo TEXT_HTTPS_LONG; ?></span></p>
				            <p class="small"><?php echo xtc_draw_checkbox_field_installer('ENABLE_SSL', 'true'); ?> 
				                <strong><font color="red"><?php echo TEXT_SSL; ?></font></strong><br />
				                <span class="note"><?php echo TEXT_SSL_LONG; ?></span></p>
				            <p class="small"><strong><?php echo TEXT_WS_ROOT; ?></strong><br />
				                <?php echo xtc_draw_input_field_installer('DIR_FS_DOCUMENT_ROOT'); ?><br />
				                <span class="note"><?php echo TEXT_WS_ROOT_LONG; ?></span></p>
				            <p class="small"><strong><?php echo TEXT_WS_XTC; ?></strong><br />
				                <?php echo xtc_draw_input_field_installer('DIR_FS_CATALOG'); ?><br />
				                <span class="note"><?php echo TEXT_WS_XTC_LONG; ?></span></p>
				            <p class="small"><strong><?php echo TEXT_WS_ADMIN; ?></strong><br />
				                <?php echo xtc_draw_input_field_installer('DIR_FS_ADMIN'); ?><br />
				                <span class="note"><?php echo TEXT_WS_ADMIN_LONG; ?></span></p>
				            <p class="small"><strong><?php echo TEXT_WS_CATALOG; ?></strong><br />
				                <?php echo xtc_draw_input_field_installer('DIR_WS_CATALOG'); ?><br />
				                <span class="note"><?php echo TEXT_WS_CATALOG_LONG; ?></span></p>
				            <p class="small"><strong><?php echo TEXT_WS_ADMINTOOL; ?></strong><br />
				                <?php echo xtc_draw_input_field_installer('DIR_WS_ADMIN'); ?><br />
				                <span class="note"><?php echo TEXT_WS_ADMINTOOL_LONG; ?></span></p>
			              
							<span class="title"><?php echo TITLE_CHECK_DATABASE; ?></span><hr class="lineRed" />
			              	<p class="small"><strong><?php echo TEXT_DATABASE_SERVER; ?></strong><br />
			                	<?php echo xtc_draw_input_field_installer('DB_SERVER'); ?><br />
			                	<span class="note"><?php echo TEXT_DATABASE_SERVER_LONG; ?></span></p>
			              	<p class="small"><strong><?php echo TEXT_USERNAME; ?></strong><br />
			                	<?php echo xtc_draw_input_field_installer('DB_SERVER_USERNAME'); ?><br />
			                	<span class="note"><?php echo TEXT_USERNAME_LONG; ?></span></p>
			              	<p class="small"><strong><?php echo TEXT_PASSWORD; ?></strong><br />
			                	<?php echo xtc_draw_input_field_installer('DB_SERVER_PASSWORD'); ?><br />
			                	<span class="note"><?php echo TEXT_PASSWORD_LONG; ?></span></p>
			              	<p class="small"><strong><?php echo TEXT_DATABASE; ?></strong><br />
			                	<?php echo xtc_draw_input_field_installer('DB_DATABASE'); ?><br />
			                	<span class="note"><?php echo TEXT_DATABASE_LONG; ?></span></p>
			              	<p class="small"><?php echo xtc_draw_checkbox_field_installer('USE_PCONNECT', 'true'); ?> 
			                	<strong><?php echo TEXT_PERSIST; ?></strong><br />
			                	<span class="note"><?php echo TEXT_PERSIST_LONG; ?></span></p>
			              	<p class="small"><?php echo xtc_draw_radio_field_installer('STORE_SESSIONS', 'files'); ?> 
			                	<strong><?php echo TEXT_SESS_FILE; ?></strong><br />
			                	<?php echo xtc_draw_radio_field_installer('STORE_SESSIONS', 'mysql', true); ?> 
			                	<strong><?php echo TEXT_SESS_DB; ?></strong><br />
			                <span class="note"><?php echo TEXT_SESS_LONG; ?></span></p>
			
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
							  		<td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel" /></a></td>
							    	<td align="center"><input type="hidden" name="install[]" value="configure" />
                                    <input type="image" src="images/button_continue.gif" alt="Continue" /></td>
							  	</tr>
							</table>
			
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

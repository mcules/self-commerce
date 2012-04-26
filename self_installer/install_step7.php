<?php
  /* --------------------------------------------------------------
   $Id: install_step7.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   Released under the GNU General Public License
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (install_step7.php,v 1.26 2003/08/17); www.nextcommerce.org
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('../includes/configure.php'); 
  
  require('includes/application.php');
  
  require_once(DIR_FS_INC . 'xtc_rand.inc.php');
  require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');   
  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
  require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
  require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_countries.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

  include('language/'.$_SESSION['language'].'.php');
  
  // connect do database
  xtc_db_connect() or die('Unable to connect to database server!'); 
 
  // get configuration data
  $configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
  while ($configuration = xtc_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

   $messageStack = new messageStack();
  
    $process = false;
  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $process = true;
        
                $status_discount = xtc_db_prepare_input($_POST['STATUS_DISCOUNT']);
                $status_ot_discount_flag = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT_FLAG']);
                $status_ot_discount = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT']);
                $graduated_price = xtc_db_prepare_input($_POST['STATUS_GRADUATED_PRICE']);
                $show_price = xtc_db_prepare_input($_POST['STATUS_SHOW_PRICE']);
                $show_tax = xtc_db_prepare_input($_POST['STATUS_SHOW_TAX']);

        
                $status_discount2 = xtc_db_prepare_input($_POST['STATUS_DISCOUNT2']);
                $status_ot_discount_flag2 = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT_FLAG2']);
                $status_ot_discount2 = xtc_db_prepare_input($_POST['STATUS_OT_DISCOUNT2']);
                $graduated_price2 = xtc_db_prepare_input($_POST['STATUS_GRADUATED_PRICE2']);
                $show_price2 = xtc_db_prepare_input($_POST['STATUS_SHOW_PRICE2']);
                $show_tax2 = xtc_db_prepare_input($_POST['STATUS_SHOW_TAX2']);

    $error = false;
        // default guests    
     if (strlen($status_discount) < '3') {
        $error = true;
        $messageStack->add('install_step7', ENTRY_DISCOUNT_ERROR);
        }
     if (strlen($status_ot_discount) < '3') {
        $error = true;
        $messageStack->add('install_step7', ENTRY_OT_DISCOUNT_ERROR);
        }
     if ( ($status_ot_discount_flag != '1') && ($status_ot_discount_flag != '0') ) {
        $error = true;

        $messageStack->add('install_step7', SELECT_OT_DISCOUNT_ERROR);
        }
     if ( ($graduated_price != '1') && ($graduated_price != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_GRADUATED_ERROR);
        }
     if ( ($show_price != '1') && ($show_price != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_PRICE_ERROR);
        }
     if ( ($show_tax != '1') && ($show_tax != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_TAX_ERROR);
        }
        
        // default customers
     if (strlen($status_discount2) < '3') {
        $error = true;
      $messageStack->add('install_step7', ENTRY_DISCOUNT_ERROR2);
        }        
     if (strlen($status_ot_discount2) < '3') {
          $error = true;
          $messageStack->add('install_step7', ENTRY_OT_DISCOUNT_ERROR2);
        }
     if ( ($status_ot_discount_flag2 != '1') && ($status_ot_discount_flag2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_OT_DISCOUNT_ERROR2);
        }
     if ( ($graduated_price2 != '1') && ($graduated_price2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_GRADUATED_ERROR2);
        }
     if ( ($show_price2 != '1') && ($show_price2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_PRICE_ERROR2);
        }
     if ( ($show_tax2 != '1') && ($show_tax2 != '0') ) {
        $error = true;
        $messageStack->add('install_step7', SELECT_TAX_ERROR2);
        }
        
if ($error == false) {
                
// admin
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '1', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')");
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES ('0', '2', 'Admin', 1, 'admin_status.gif', '0.00', '1', '0.00', '1', '1', '1')");

// status Guest
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 1, 'Guest', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')");
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (1, 2, 'Gast', 1, 'guest_status.gif', '".$status_discount."', '".$status_ot_discount_flag."', '".$status_ot_discount."', '".$graduated_price."', '".$show_price."', '".$show_tax."')");
// status New customer
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 1, 'New customer', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')");
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (2, 2, 'Neuer Kunde', 1, 'customer_status.gif', '".$status_discount2."', '".$status_ot_discount_flag2."', '".$status_ot_discount2."', '".$graduated_price2."', '".$show_price2."', '".$show_tax2."')");

// status Merchant
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (3, 1, 'Merchant', 1, 'merchant_status.gif', '0.00', '0', '0.00', '1', 1, 0)");
xtc_db_query("INSERT INTO customers_status (customers_status_id, language_id, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices, customers_status_show_price, customers_status_show_price_tax) VALUES (3, 2, 'Händler', 1, 'merchant_status.gif', '0.00', '0', '0.00', '1', 1, 0)");


// create Group prices (Admin wont get own status!)

xtc_db_query("create table personal_offers_by_customers_status_1 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
xtc_db_query("create table personal_offers_by_customers_status_2 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
xtc_db_query("create table personal_offers_by_customers_status_0 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
xtc_db_query("create table personal_offers_by_customers_status_3 (price_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,products_id int NOT NULL,quantity int, personal_offer decimal(15,4)) ");
              xtc_redirect(xtc_href_link('self_installer/install_finished.php', '', 'NONSSL'));
                }                       
        }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Self-Commerce Installer - STEP 7 / Define Pricesystem</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require('includes/form_check.js.php'); ?>
<style type="text/css">
<!--
.messageBox {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 1;
}
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
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_WRITE_CONFIG; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif"></td>
	            </tr>
	            <tr>
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_ADMIN_CONFIG; ?></td>
                	<td class="left_top2"><img src="images/icons/ok.gif"></td>
	            </tr>
				<tr>
                	<td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_USERS_CONFIG; ?></font></td>
               </tr>
               <?php
				  if ($messageStack->size('install_step7') > 0) {
				?>
				<tr>
					<td class="left_top" colspan="2"><?php echo $messageStack->output('install_step7'); ?></td>
				</tr>
               <?php
				  }
				?>
            </table>
		</td>
		<td align="right" valign="top" class="frame2" width="100%">
			<h2 class="welcome"><?php echo TEXT_WELCOME_STEP7; ?></h2><hr class="lineBlue"> 
		    <table width="98%" border="0" cellpadding="0" cellspacing="0">
            	<tr> 
          			<td>
						<form name="install" action="install_step7.php" method="post" onSubmit="return check_form(install_step6);">
              			<input name="action" type="hidden" value="process">
              			<span class="title"><?php echo TITLE_GUEST_CONFIG; ?> </span><span class="note_red_title"><?php echo TEXT_REQU_INFORMATION; ?></span><hr class="lineRed">
		                <span class="note"><?php echo  TITLE_GUEST_CONFIG_NOTE; ?></span>
                        <p><span class="small_strong"><?php echo TEXT_STATUS_DISCOUNT; ?></span><br />
                      		<?php echo xtc_draw_input_field_installer('STATUS_DISCOUNT','0.00'); ?><br />
                      		<span class="note"><?php echo TEXT_STATUS_DISCOUNT_LONG; ?></span></p>
		                <p><span class="small_strong"><?php echo TEXT_STATUS_OT_DISCOUNT_FLAG; ?></span><br />
		                   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG', '1'); ?>&nbsp;&nbsp;
		                   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG', '0', 'true'); ?><br />
		                   <span class="note"><?php echo TEXT_STATUS_OT_DISCOUNT_FLAG_LONG; ?></span></p>
		                <p><span class="small_strong"><?php echo TEXT_STATUS_OT_DISCOUNT; ?></span><br />
		                   <?php echo xtc_draw_input_field_installer('STATUS_OT_DISCOUNT','0.00'); ?><br />
		                   <span class="note"><?php echo TEXT_STATUS_OT_DISCOUNT_LONG; ?></span></p>
		                <p><span class="small_strong"><?php echo TEXT_STATUS_GRADUATED_PRICE; ?></span><br />
		                   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE', '1'); ?>&nbsp;&nbsp;
		                   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE', '0', 'true'); ?><br />
		                   <span class="note"><?php echo TEXT_STATUS_GRADUATED_PRICE_LONG; ?></span></p>
		                <p><span class="small_strong"><?php echo TEXT_STATUS_SHOW_PRICE; ?></span><br />
		                   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE', '1', 'true'); ?>&nbsp;&nbsp;
		                   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE', '0'); ?><br />
		                   <span class="note"><?php echo TEXT_STATUS_SHOW_PRICE_LONG; ?></span></p>
		                <p><span class="small_strong"><?php echo TEXT_STATUS_SHOW_TAX; ?></span><br />
		                   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX', '1', 'true'); ?>&nbsp;&nbsp;
		                   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX', '0'); ?><br />
		                   <span class="note"><?php echo TEXT_STATUS_SHOW_TAX_LONG; ?></span></p>
		                <br /><br />
		                <span class="title"><?php echo TITLE_CUSTOMERS_CONFIG; ?> </span><span class="note_red_title"><?php echo TEXT_REQU_INFORMATION; ?></span><hr class="lineRed">
                  		<span class="note"><?php echo  TITLE_CUSTOMERS_CONFIG_NOTE; ?></span>
                  		<p><span class="small_strong"><?php echo TEXT_STATUS_DISCOUNT; ?></span><br />
                  		   <?php echo xtc_draw_input_field_installer('STATUS_DISCOUNT2','0.00'); ?><br />
                  		   <span class="note"><?php echo TEXT_STATUS_DISCOUNT_LONG; ?></span></p>
                  		<p><span class="small_strong"><?php echo TEXT_STATUS_OT_DISCOUNT_FLAG; ?></span><br />
                  		   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG2', '1'); ?>&nbsp;&nbsp;
                  		   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_OT_DISCOUNT_FLAG2', '0', 'true'); ?><br />
                  		   <span class="note"><?php echo TEXT_STATUS_OT_DISCOUNT_FLAG_LONG; ?></span></p>
                  		<p><span class="small_strong"><?php echo TEXT_STATUS_OT_DISCOUNT; ?></span><br />
                  		   <?php echo xtc_draw_input_field_installer('STATUS_OT_DISCOUNT2','0.00'); ?><br />
                  		   <span class="note"><?php echo TEXT_STATUS_OT_DISCOUNT_LONG; ?></span></p>
                  		<p><span class="small_strong"><?php echo TEXT_STATUS_GRADUATED_PRICE; ?></span><br />
                  		   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE2', '1', 'true'); ?>&nbsp;&nbsp;
                  		   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_GRADUATED_PRICE2', '0'); ?><br />
                  		   <span class="note"><?php echo TEXT_STATUS_GRADUATED_PRICE_LONG; ?></span></p>
                  		<p><span class="small_strong"><?php echo TEXT_STATUS_SHOW_PRICE; ?></span><br />
                  		   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE2', '1', 'true'); ?>&nbsp;&nbsp;
                  		   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_PRICE2', '0'); ?><br />
                  		   <span class="note"><?php echo TEXT_STATUS_SHOW_PRICE_LONG; ?></span></p>
                    	<p><span class="small_strong"><?php echo TEXT_STATUS_SHOW_TAX; ?></span><br />
                      	   <span class="small"><?php echo  TEXT_ZONE_YES; ?> </span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX2', '1', 'true'); ?>&nbsp;&nbsp;
                      	   <span class="small"><?php echo  TEXT_ZONE_NO; ?></span><?php echo xtc_draw_radio_field_installer('STATUS_SHOW_TAX2', '0'); ?><br />
                      	   <span class="note"><?php echo TEXT_STATUS_SHOW_TAX_LONG; ?></span></p>
                          <br /><br />
			              <center>
			                <input name="image" type="image" src="images/button_continue.gif" alt="Continue" align="middle" border="0">
			                <br />
			              </center>
            			</form>
            		</td>
				</tr>
			</table> 
		</td>
	</tr>
</table>
<p class="small" align="center"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>
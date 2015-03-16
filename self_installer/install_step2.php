<?php
  /* --------------------------------------------------------------
   $Id: install_step2.php 1119 2005-07-25 22:19:50Z novalis $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2015 Self-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001	The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003	osCommerce(install_2.php,v 1.4 2002/08/12); www.oscommerce.com
   (c) 2003-2008	nextcommerce (install_step2.php,v 1.16 2003/08/1); www.nextcommerce.org
   (c) 2008			Self-Commerce (install_step2.php) www.self-commerce.de

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application.php');

// include needed functions
require_once(DIR_FS_INC.'xtc_redirect.inc.php');
require_once(DIR_FS_INC.'xtc_href_link.inc.php');
require_once(DIR_FS_INC.'xtc_not_null.inc.php');

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
if (xtc_in_array('database', $_POST['install'])) {
	// do nothin
} else {
	xtc_redirect('install_step4.php');
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Self-Commerce Installer - STEP 2 / DB Connection</title>
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
					<td class="left_top2" width="135" ><img src="images/icons/arrow02.gif" width="13" height="6" alt="arrow" /><?php echo BOX_LANGUAGE; ?></td>
               		<td class="left_top2" width="35"><img src="images/icons/ok.gif" alt="OK" /></td>
              	</tr>
              	<tr>
                	<td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6" alt="arrow" /><?php echo BOX_DB_CONNECTION; ?></td>
                	<td class="left_top2">
		                <?php
		                // test database connection and write permissions
		                if (xtc_in_array('database', $_POST['install'])) {
							$db = array();
							$db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
							$db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
							$db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
							$db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
							$db_error = false;
							xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

							if (!$db_error) {
								xtc_db_test_create_db_permission($db['DB_DATABASE']);
							}

							if ($db_error) {
								echo ('<img src="images/icons/x.gif">');
							} else {
								echo ('<img src="images/icons/ok.gif" alt="OK" />');
							}
		                }
		                ?>
	                </td>
				</tr>
              	<tr>
                	<td colspan="2" class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6" alt="arrow" /><?php echo BOX_DB_IMPORT; ?></td>
                </tr>
              	<tr>
                    <td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6" alt="arrow" /><?php echo BOX_WEBSERVER_SETTINGS; ?></td>
                </tr>
            </table>
		</td>
    	<td align="right" valign="top" class="frame2" width="100%">
     		<h2 class="welcome"><?php echo TEXT_WELCOME_STEP2; ?></h2><hr class="lineBlue">
			<table width="98%" border="0" cellpadding="0" cellspacing="0">
      			<tr>
    				<td>
						<?php
						if (xtc_in_array('database', $_POST['install'])) {
						$db = array();
						$db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
						$db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
						$db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
						$db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
						$db_error = false;
						xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);
						if (!$db_error) {
							xtc_db_test_create_db_permission($db['DB_DATABASE']);
						}
						if ($db_error) {
						?>
						<br />
						<h2 class="normal"><img src="images/icons/error.gif" width="16" height="16" alt="arrow" />&nbsp;<strong><?php echo TEXT_CONNECTION_ERROR; ?></strong></h2><hr class="lineRed">
						<p class="normal"><?php echo TEXT_DB_ERROR; ?></p>
						<p class="h1 warning"><strong><?php echo $db_error; ?></strong></p>
						<p class="small"><?php echo TEXT_DB_ERROR_1; ?></p>
						<p class="small"><?php echo TEXT_DB_ERROR_2; ?></p>
						<form name="install" action="install_step1.php" method="post">
							<?php
							reset($_POST);
							while (list($key, $value) = each($_POST)) {
								if ($key != 'x' && $key != 'y') {
									if (is_array($value)) {
										for ($i=0; $i<sizeof($value); $i++) {
											echo xtc_draw_hidden_field_installer($key . '[]', $value[$i]);
										}
									}
									else {
										echo xtc_draw_hidden_field_installer($key, $value);
									}
								}
							}
							?>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
							  <tr>
							    <td align="center"><a href="index.php"><img src="images/button_cancel.gif" border="0" alt="Cancel" /></a></td>
							    <td align="center"><input type="image" src="images/button_back.gif" border="0" alt="Back" /></td>
							  </tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
<?php } else { ?>
			<span class="title"><?php echo TEXT_CONNECTION_SUCCESS; ?></span><hr class="lineRed">
			<p class="small"><?php echo TEXT_PROCESS_1; ?></p>
			<p class="small"><?php echo TEXT_PROCESS_2; ?></p>
			<p class="small"><?php echo TEXT_PROCESS_3; ?> <strong><?php echo DIR_FS_CATALOG . 'self_installer/self_commerce.sql'; ?></strong>.</p>
			<form name="install" action="install_step3.php" method="post">
				<?php
				reset($_POST);
				while (list($key, $value) = each($_POST)) {
					if ($key != 'x' && $key != 'y') {
						if (is_array($value)) {
							for ($i=0; $i<sizeof($value); $i++) {
								echo xtc_draw_hidden_field_installer($key . '[]', $value[$i]);
							}
						}
						else {
							echo xtc_draw_hidden_field_installer($key, $value);
						}
					}
				}
				?>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td align="center"><a href="install_step1.php"><img src="images/button_cancel.gif" border="0" alt="Cancel" /></a></td>
					    <td align="center"><input type="image" src="images/button_continue.gif" alt="Continue"></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php
    }
  }
?>
		</td>
	</tr>
</table>
<p align="center" class="small"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>
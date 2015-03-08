<?php
  /* --------------------------------------------------------------
   $Id: index.php 1220 2005-09-16 15:53:13Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application.php');

// include needed functions
require_once(DIR_FS_INC.'xtc_image.inc.php');
require_once(DIR_FS_INC.'xtc_draw_separator.inc.php');
require_once(DIR_FS_INC.'xtc_redirect.inc.php');
require_once(DIR_FS_INC.'xtc_href_link.inc.php');

include('language/english.php');

// Include Developer - standard settings for installer
//  require('developer_settings.php');
define('HTTP_SERVER','');
define('HTTPS_SERVER','');
define('DIR_WS_CATALOG','');

$messageStack = new messageStack();

$process = false;
if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
	$process = true;
	$_SESSION['language'] = xtc_db_prepare_input($_POST['LANGUAGE']);
	$error = false;

	if ( ($_SESSION['language'] != 'german') && ($_SESSION['language'] != 'english') ) {
		$error = true;
		$messageStack->add('index', SELECT_LANGUAGE_ERROR);
	}

	if ($error == false) {
		xtc_redirect(xtc_href_link('install_step1.php', '', 'NONSSL'));
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>XT-Commerce Installer - Welcome</title>
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
.green {border: 1px solid #66CC33; background-color: #CCFF99; padding: 2px; font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px;}
.red {border: 1px solid #ff6600; background-color: #FFCC99; padding: 2px; font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px;}
.h1 {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 12px;}
.h1:hover {color: #ff6600;}
h2 {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px; font-weight: normal;}
.lineBlue {border: 1px solid #cbcfde;}
.lineRed {border: 1px solid #ff6600;}
.lineGreen {border: 1px solid #66cc33;}
.warning {background-color: #cbcfde;}
.small {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 10px;}
.title {font-family: Verdana, Arial, Helvetica, san-serif; font-size: 12px; font-weight: bold; background-image: url(images/icons/title.gif); background-repeat: no-repeat; padding-left: 20px;}
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
					<td height="17" class="blocktitle" align="center">Self-Commerce Install</td>
				</tr>
				<tr>
					<td class="left_top" ><img src="images/icons/arrow02.gif" width="13" height="6" alt="Arrow" />&nbsp;<?php echo BOX_LANGUAGE; ?></td>
				</tr>
			</table>
		</td>
		<td align="right" class="frame2" valign="top">
      	<br />
			<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<br />
						<table width="100%" class="green"  border="0" cellpadding="2" cellspacing="2">
							<tr>
								<td width="1"><img src="images/install.gif" border="0" alt="install" /></td>
								<td><a class="h1" href="http://www.self-commerce.de" target="_blank">Installationsanleitung auf www.self-commerce.de</a></td>
                  			</tr>
                		</table>
                		<br /><h2><?php echo TEXT_WELCOME_INDEX; ?></h2><br />
					</td>
				</tr>
				<tr>
<?php
  // permission check to prevent DAU faults.
$ErrorFlag = false;
$message = '';
$Message2 = '';
$ok_message = '';
$block1 = false;
$block2 = false;
$block3 = false;
$block4 = false;
$block5 = false;
$block6 = false;
$block7 = false;
$block8 = false;

$ToCheck['Description']['Files'] = array(
	1 => 'config files'
);
$ToCheck['Description']['Folders'] = array(
	1 => 'admin folders',
	2 => 'smarty folders',
	3 => 'export folders',
	4 => 'media folders',
	5 => 'image folders',
	6 => 'upload folders',
	7 => 'Amazon folders',
);
$ToCheck['Files'] = array(
	1 => array(
		'includes/configure.php',
		'includes/configure.org.php',
		'admin/includes/configure.php',
		'admin/includes/configure.org.php'
	)
);
$ToCheck['Folders'] = array(
	1 => array(
		'admin/backups/',
		'admin/images/graphs',
		'admin/includes/modules/tiny_mce/plugins/media/filemanager/',
		'admin/invoice/'
	),
	2 => array(
		'templates_c/',
		'cache/'
	),
	3 => array(
		'export/'
	),
	4 => array(
		'media/',
		'media/content/',
	),
	5 => array(
		'images/',
		'images/categories/',
		'images/banner/',
		'images/manufacturers/',
		'images/product_images/info_images/',
		'images/product_images/original_images/',
		'images/product_images/popup_images/',
		'images/product_images/thumbnail_images/'
	),
	6 => array(
		'tiny_upload/pics/',
		'tiny_upload/files/'
	),
	7 => array(
		'AmazonAdvancedPayment/log/'
	)
);

$FileErrorFlag = false;
foreach($ToCheck['Files'] as $Block => $BlockContent) {
	foreach($BlockContent as $Content) {
		if (!is_writeable('../' . $Content)) {
			$ErrorFlag = true;
			$FileErrorFlag = true;
			$BlockCheck[$Block] = true;
			$message .= 'WRONG PERMISSION on ' . $Content . '<br />';
			$Message2 .= "chmod 777 $Content<br />";
		}
	}
	if ($BlockCheck[$Block] == true) $message .= '<hr class="lineRed">';
}

$FolderErrorFlag == false;
foreach($ToCheck['Folders'] as $Block => $BlockContent) {
	foreach($BlockContent as $Content) {
		if (!is_writeable('../' . $Content)) {
			$ErrorFlag = true;
			$FolderErrorFlag = true;
			$BlockCheck[$Block] = true;
			$message .= 'WRONG PERMISSION on ' . $Content . '<br />';
			$Message2 .= "chmod 777 $Content<br />";
		}
	}
	if ($BlockCheck[$Block] == true) $message .= '<hr class="lineRed">';
}

if ($FileErrorFlag == true) { $Status = '<strong><font color="#ff0000">ERROR</font></strong>'; }
else { $Status = 'OK'; }
$ok_message .= 'FILE Permissions .............................. ' . $Status . '<hr class="lineGreen">';

if ($FolderErrorFlag == true) { $Status = '<strong><font color="#ff0000">ERROR</font></stong>'; }
else { $Status = 'OK'; }
$ok_message .= 'FOLDER Permissions .............................. ' . $Status . '<hr class="lineGreen">';

// check PHP-Version
$php_flag == false;
if (xtc_check_version() != 1) {
	$ErrorFlag = true;
	$php_flag = true;
	$message .='<strong>ATTENTION!, your PHP Version is to old, XT-Commerce requires atleast PHP 4.1.3.</strong><br /><br />
				Your php Version: <strong><?php echo phpversion(); ?></strong><br /><br />
				XT-Commerce wont work on this server, update PHP or change Server.';
}

$status = 'OK';
if ($php_flag == true) $status='<strong><font color="#ff0000">ERROR</font></strong>';
$ok_message .= 'PHP VERSION .............................. ' . $status . '<hr class="lineGreen">';

$gd = gd_info();

if ($gd['GD Version'] == '') $gd['GD Version'] = '<strong><font color="#ff0000">ERROR NO GDLIB FOUND!</font></strong>';

$status = $gd['GD Version'] . ' <br />  if GDlib Version < 2+ , klick here for further instructions';

// display GDlibversion
$ok_message .= 'GDlib VERSION .............................. ' . $status . '<hr class="lineGreen">';

if ($gd['GIF Read Support'] == 1 || $gd['GIF Support'] == 1) {
	$status = 'OK';
} else {
	$status = '<strong><font color="#ff0000">ERROR</font></strong><br />You don\'t have GIF support within your GDlib, you won\'t be able to use GIF images, and GIF overlayfunctions in XT-Commerce!';
}
$ok_message .= 'GDlib GIF-Support .............................. ' . $status . '<hr class="lineGreen">';
?>
<td class="red">
<?php
if ($ErrorFlag == true) {
	echo '<strong>Attention:</strong><br />';
	echo $message;
}
?>
</td></tr>
<tr>
<?php
if ($ok_message != '') {
?>
<td height="20"></td></tr><tr>
<td class="green">
<strong>Checking:</strong><br />
<?php echo $ok_message; ?>

</td>
<?php } ?>
</tr>

      </table>
      <hr class="lineBlue">


      <table width="98%" border="0" align="right" cellpadding="0" cellspacing="0">
        <tr>
          <td><span class="title"><strong>
            <?php echo TITLE_SELECT_LANGUAGE; ?></strong></span><br />
            <hr class="lineRed">
                                                        <?php
  if ($messageStack->size('index') > 0) {
?><br />
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td><?php echo $messageStack->output('index'); ?></td>
	</tr>
</table>


<?php
  }
?>
            <form name="language" method="post" action="index.php">

              <table width="300" border="0" cellpadding="0" cellspacing="4">
                <tr>
                  <td width="98" class="small"><img src="images/icons/arrow02.gif" width="13" height="6" alt="arrow" />German</td>
                  <td width="192"><img src="../lang/german/icon.gif" width="24" height="16" alt="arrow" />
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'german'); ?>
                  </td>
                </tr>
                <tr>
                  <td class="small"><img src="images/icons/arrow02.gif" width="13" height="6" alt="arrow" />English</td>
                  <td><img src="../lang/english/icon.gif" width="24" height="16" alt="icon" />
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'english'); ?> </td>
                </tr>
              </table>

              <input type="hidden" name="action" value="process">
              <p> <?php if ($ErrorFlag == false) { ?><input type="image" src="images/button_continue.gif" alt="Continue" /> <?php } ?><br />
                <br />
              </p>
            </form>

          </td>
        </tr>
      </table></td>
  </tr>
</table>

<p align="center" class="small"><?php echo TEXT_FOOTER; ?></p>
</body>
</html>
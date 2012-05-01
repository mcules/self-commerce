<?php
/* --------------------------------------------------------------
   $Id: start.php 10 2012-04-28 03:21:05Z deisold $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de
   Copyright (c) 2012 Self-Commerce
   --------------------------------------------------------------*/

require('includes/application.php');

// include needed functions
require_once(DIR_FS_INC.'xtc_redirect.inc.php');
require_once(DIR_FS_INC.'xtc_href_link.inc.php');
require_once(DIR_FS_INC.'xtc_not_null.inc.php');
require_once(DIR_FS_INC.'xtc_db_install_update.inc.php');

include('language/'.$_SESSION['language'].'.php');
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
                            <td class="left_top2" width="135" ><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_LANGUAGE; ?></td>
                            <td class="left_top2" width="35"><img src="images/icons/ok.gif"></td>
                        </tr>
                        <tr>
                            <td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_DB_CONNECTION; ?></td>
                            <td class="left_top2" width="35"><img src="images/icons/ok.gif"></td>
                        </tr>
                        <tr>
                            <td class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_UPDATE_DB; ?></td>
                            <td class="left_top2" width="35"><img src="images/icons/ok.gif"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_UPDATE_DONE; ?></td>
                        </tr>
                    </table>
                </td>
                <td align="right" valign="top" class="frame2" width="100%">
                    <table width="98%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <br />
                                <?php
                                xtc_db_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
                                $Self_Version = preg_replace('/([^0-9\.]+)/', "", $_POST['Version']);
                                $Sql_File = DIR_FS_CATALOG . "self_installer/update_$Self_Version.sql";
                                xtc_db_install_update(DB_DATABASE, $Sql_File);
                                if(!$db_error) { ?>
                                    <span class="title"><?php echo TEXT_UPDATE_STEP2_TITLE; ?></span><hr class="lineRed">
                                    <p class="small"><?php echo TEXT_UPDATE_STEP2_1; ?></p>
                                    <p class="small"><?php echo TEXT_UPDATE_STEP2_2; ?></p>
                                    <form name="install" action="update_finished.php" method="post">
                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center"><a href="update.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a></td>
                                                <td align="center"><input type="image" src="images/button_continue.gif" border="0" alt="Continue"></td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>
                                    <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p align="center" class="small"><?php echo TEXT_FOOTER; ?></p>
    </body>
</html>
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

require('../includes/configure.php');

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
                        <td class="left_top2">
                            <?php
                            // test database connection and write permissions
                            $db_error = false;
                            xtc_db_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);

                            if (!$db_error) {
                                xtc_db_test_connection(DB_DATABASE);
                            }

                            if ($db_error) { echo ('<img src="images/icons/x.gif">'); }
                            else { echo ('<img src="images/icons/ok.gif">'); }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="left_top2"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_UPDATE_DB; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="left_top"><img src="images/icons/arrow02.gif" width="13" height="6"><?php echo BOX_UPDATE_DONE; ?></td>
                    </tr>
                </table>
            </td>
            <td align="right" valign="top" class="frame2" width="100%">
                <h2 class="welcome"><?php echo TEXT_WELCOME_STEP2; ?></h2><hr class="lineBlue">
                <table width="98%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <?php
                            if (!$db_error) {
                                $Version_Query = xtc_db_query_installer("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'SELF_VERSION';");
                                while ($Row = xtc_db_fetch_array($Version_Query)) {
                                    $Self_Version = $Row['configuration_value'];
                                }
                            }

                            if ($db_error) { ?>
                <br />
                <h2 class="normal"><img src="images/icons/error.gif" width="16" height="16">&nbsp;<strong><?php echo TEXT_CONNECTION_ERROR; ?></strong></h2><hr class="lineRed">
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
            </td>
        </tr>
    </table>
<?php } else { ?>
            <span class="title"><?php echo TEXT_CONNECTION_SUCCESS; ?></span><hr class="lineRed">
            <p class="small"><?php echo TEXT_UPDATE_1; ?></p>
            <p class="small"><?php echo TEXT_PROCESS_2; ?></p>
            <p class="small">
                <?php
                if($Self_Version == NULL) { $Self_Version = '2.0'; }

                /* TODO Muss noch umgestellt werden fuer Livebetrieb */
                $Sql_File = DIR_FS_CATALOG . "self_installer/update_$Self_Version.sql";
                if(file_exists(($Sql_File))) {
                    $Error = false;
                    echo '<span class="green"><strong>Checking Updatefile .............................. OK</strong></span>';
                }
                else {
                    $Error = true;
                    echo '<span class="red"><strong>Checking Updatefile .............................. <font color="red">ERROR</font></strong></span>';
                }
                ?>
                <br />
                <br />
            </p>
            <?php if(!$Error) { ?>
                <p class="small"><?php echo TEXT_UPDATE_2; ?></p>
                <form name="install" action="update_step2.php" method="post">
                    <input type="hidden" name="Version" value="<?php echo $Self_Version.'$%&§()'; ?>" />
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center"><a href="update.php"><img src="images/button_cancel.gif" border="0" alt="Cancel"></a></td>
                            <td align="center"><input type="image" src="images/button_continue.gif" border="0" alt="Continue"></td>
                        </tr>
                    </table>
                </form>
                <?php } ?>
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
<?php
/* --------------------------------------------------------------
   $Id$
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
require ('includes/application_top.php');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
    <title>
      <?php echo TITLE; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <style type="text/css">.h2 { font-family: Trebuchet MS,Palatino,Times New Roman,serif; font-size: 13pt; font-weight: bold; }.h3 { font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 9pt; font-weight: bold; }
    </style>
  </head>
  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
          <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <!-- left_navigation //-->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof //-->
          </table></td>
        <!-- body_text //-->
        <td class="boxCenter" width="100%" valign="top">
 <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_configuration.gif'); ?></td>
    <td class="pageHeading"><?php echo ADMIN_SQL; ?></td>
  </tr>
  <tr>
    <td class="main" valign="top"><?php echo ADMIN_SQL_DESC; ?></td>
  </tr>
</table>
<!-- sql test -->
<span class="main">

<?php
if(isset($_POST['go'])){
	if(ini_get('magic_quotes_gpc') == 1){
		$sql_string = stripslashes($_POST['sql_query']);
	}
	mysql_query("$sql_string");
	if(mysql_error() == ""){
		echo "<br />" . ADMIN_SQL_SUCCESS;
	}
	else{
		echo "<br /><font color=\"red\"><b>" . mysql_error() ."</b></font>";
	}
}
?>

<form name="sql_editor" action="admin_sql.php" method="post">

<br>
<br>

<?php echo ADMIN_SQL_TEXT ?><br>
<textarea name="sql_query" cols="120" rows="10"></textarea>
<br>
<br>
ohne Funktion im Demoshop: <input type="submit" name="" value="absenden" />
</form>

</span>
<!--sql test -->
         
          </td>
        <!-- body_text_eof //-->
      </tr>
    </table>
    <!-- body_eof //-->
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

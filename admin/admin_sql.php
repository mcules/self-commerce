<?php
/* --------------------------------------------------------------
   $Id: admin_sql.php 17 2012-06-04 20:33:29Z deisold $
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
require ('includes/application_top_1.php');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="80" rowspan="2">
            <?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></td>
          <td class="pageHeading">
            <?php echo ADMIN_SQL; ?></td>
        </tr>
        <tr>
          <td class="main" valign="top">
            <?php echo ADMIN_SQL_DESC; ?></td>
        </tr>
      </table></td>
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
    <?php echo ADMIN_SQL_TEXT ?>
    <br>
<textarea name="sql_query" cols="120" rows="10"></textarea>
    <br>
    <br>
    <input type="submit" name="go" value="OK" />
  </form>
</span>
<!--sql test -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

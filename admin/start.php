<?php
/* --------------------------------------------------------------
   $Id: start.php 1235 2005-09-21 19:11:43Z mz $
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
  <body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="0">
      <tr>
        <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
          <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="0" class="columnLeft">
            <!-- left_navigation //-->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof //-->
          </table></td>
        <!-- body_text //-->
        <td class="boxCenter" width="100%" valign="top">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
                      <?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></td>
                    <td class="pageHeading">
                      <?php echo HEADING_TITLE; ?></td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">
                      Self-Commerce News</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td>
                <?php include(DIR_WS_MODULES.FILENAME_SECURITY_CHECK); ?></td>
            </tr>
          </table>
<!-- Geburtstage prüfen - ANFANG realisiert durch TechWay geändert durch ne-xt -->
                <fieldset class="fieldset">
                  <legend>Heutige Geburtstage:</legend> 
<?php 
$ergebnis=xtc_db_query("SELECT * FROM customers ORDER BY customers_dob"); 
$i=0; 
while($row = mysql_fetch_object($ergebnis)) 
    { 
        $gebdat=strtotime($row->customers_dob); 
        $gebjahr=date('Y',$gebdat);   //geburtsjahr z.b. 1980 
        $gebmonat=date('n',$gebdat);   //geburtsmonat z.b. 5 
        $gebtag=date('j',$gebdat);   //geburtstag   z.b. 20
 
        if ($gebmonat == date('n') and $gebtag == date('j'))
        {   //Kunde hat heute Geburtstag 
            echo '<label>'; 
            echo xtc_date_short($row->customers_dob); 
            echo '</label>&nbsp;&nbsp;&nbsp;<label>';
            echo $row->customers_firstname . " " . $row->customers_lastname ;
            echo '</label>&nbsp;&nbsp;&nbsp;<label><a href="mailto:' . $row->customers_email_address .'?subject=Alles Gute ' . $row->customers_firstname . '&body=Alles gute zum Geburtstag wünscht ' . STORE_NAME . '.">';
            echo $row->customers_email_address ;            
            echo '</a></label><br />'; 
        }
      }
                  ?>
                  </fieldset>
                  <fieldset class="fieldset">
                    <legend>Diesen Monat noch:</legend> 
<?php 
$ergebnis=xtc_db_query("SELECT * FROM customers ORDER BY customers_dob"); 
$i=0; 
while($row = mysql_fetch_object($ergebnis)) 
    { 
        $gebdat=strtotime($row->customers_dob); 
        $gebjahr=date('Y',$gebdat);   //geburtsjahr z.b. 1980 
        $gebmonat=date('n',$gebdat);   //geburtsmonat z.b. 5 
        $gebtag=date('j',$gebdat);   //geburtstag   z.b. 20
 
        if ($gebmonat == date('n') and $gebtag > date('j'))
        {   //Kunde hat diesen Monat Geburtstag 
            echo '<label>'; 
            echo xtc_date_short($row->customers_dob); 
            echo '</label>&nbsp;&nbsp;&nbsp;<label>';
            echo $row->customers_firstname . " " . $row->customers_lastname ;
            echo '</label>&nbsp;&nbsp;&nbsp;<label><a href="mailto:' . $row->customers_email_address .'?subject=Alles Gute ' . $row->customers_firstname . '&body=Alles gute zum Geburtstag wünscht ' . STORE_NAME . '.">';
            echo $row->customers_email_address ;            
            echo '</a></label><br />'; 
        }
      }
                  ?>
                  </fieldset>                  
<!--    Geburtstage prüfen - ENDE ------------------------------------------------->
<br /><br />
<iframe  src="http://demoshop.ne-xt.de/news_info.php" width="100%" height="400" align="left" scrolling="yes" marginheight="0" marginwidth="0" frameborder="0"></iframe>


         
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

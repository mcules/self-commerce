<?PHP
/* --------------------------------------------------------------
   $Id: psapi_import.php

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com

   Released under the GNU General Public License
   --------------------------------------------------------------*/
  //VERSION
  
  
 

  
  $modulVersion = "1.0";
  require('includes/application_top.php');
  require('includes/haendlerbund/haendlerbund_importer.php');
  
   if($_GET["api_konfiguration"] == 1) {
	   


    $contentimporter = new haendlerbund_importer();
	echo $contentimporter->process(1);
	
  } else {
	  
	  
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">


<link href="includes/haendlerbund/css/main.css" rel="stylesheet" type="text/css" />
<link href="http://fonts.googleapis.com/css?family=Cuprum" rel="stylesheet" type="text/css" />

<script src="includes/haendlerbund/jquery-1.4.4.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/haendlerbund/jquery.smartWizard.min.js"></script>
<script type="text/javascript" src="includes/haendlerbund/custom.js"></script>



</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>



<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="237"><img src="includes/haendlerbund/images/haendlerbund_logo.png" hspace="10" vspace="10" style="padding:10px" /></td>
    <td valign="top"><img src="includes/haendlerbund/images/groesster-onlinehandelsverband-europas.png" style="padding:10px"  />  </tr>
  </table></td>
      </tr>
      
      <tr>
      <td>
      <div  style="background-color:#387CB0; height:5px;"></div>
      </td>
      </tr>

    <td width="100%" valign="top">
     
    <br/>
    
    
  
      <?PHP
	  
        $contentimporter = new haendlerbund_importer();
		echo $contentimporter->process(0);
        echo $contentimporter->getImportForm();
      
      ?>           
          
    </td>
  </tr>
</table>
 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); 
  }
  ?>
<?php
/* -----------------------------------------------------------------------------------------
   $Id: general.js.php 1262 2005-09-30 10:00:32Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

   // Put CSS-Definitions here, these CSS-files will be loaded at the TOP of every page
?>
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/bootstrap.min.css" type="text/css" media="screen" />
<style type="text/css">
      body {
        padding-top: 120px;
      }
</style>
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/bootstrap-responsive.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/stylesheet.css" type="text/css" />
<link rel="apple-touch-icon" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/img/apple-touch-icon.png">
                                                                                                                                         
<?php if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO )) {
?>
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/cloud-zoom.css" type="text/css" media="screen" />
<?php
}
 ?>

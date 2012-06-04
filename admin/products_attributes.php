<?php
/* --------------------------------------------------------------
Self-Commerce kunigunde
   --------------------------------------------------------------*/
require ('includes/application_top.php');

require ('includes/application_top_1.php');
?>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
<?php echo xtc_image(DIR_WS_ICONS.'heading_configuration.gif'); ?>
                    </td>
                    <td class="pageHeading">
<?php echo HEADING_TITLE; ?>
                    </td>
                <td align="right">

                </td>                    
                  </tr>
                </table>              
<!-- content -->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
  <td>
<!-- content -->
<?php 

include(DIR_WS_MODULES.'products_attributes.php');                

?>
<!--content -->  
  </td>
</tr>
</table>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

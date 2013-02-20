<?php
/* --------------------------------------------------------------
   $Id: start.php 17 2012-06-04 20:33:29Z deisold $
   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de
   Copyright (c) 2012 Self-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2005      XT-Commerce (start.php, 1235 2005/09/21); www.xt-commerce.com
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
require ('includes/application_top.php');
require ('includes/application_top_1.php');
require(DIR_FS_ADMIN . 'includes/classes/SC_Start.php');

$Class_SC_Start = new SC_Start();
?>
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
<?php
echo $Class_SC_Start->getNews();

require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

<?php
/* --------------------------------------------------------------
   $Id: header.php 17 2012-06-04 20:33:29Z deisold $   
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(header.php,v 1.19 2002/04/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (header.php,v 1.17 2003/08/24); www.nextcommerce.org
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0" id="BlockHeader">
  <tr>
    <td class="header_bg" style="padding-left: 5px;">
      <?php echo xtc_image(DIR_WS_IMAGES . 'admin_logo.gif', 'Self-Commerce', '200', '74'); ?></td>
    <td align="center" class="header_bg">
      <?php echo '<a href="start.php"  class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_index.gif', '', '', '').'</a>'; ?>
      <?php echo '<a href="http://www.self-commerce.de" target="_blank" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_support.gif', '', '', '').'</a>'; ?>
      <?php echo '<a href="../index.php" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_shop.gif', '', '', '').'</a>'; ?>
      <?php echo '<a href="' . xtc_href_link(FILENAME_LOGOUT, '', 'NONSSL') . '" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_logout.gif', '', '', '').'</a>'; ?>
      <?php echo '<a href="' . xtc_href_link(FILENAME_CREDITS, '', 'NONSSL') . '" class="headerLink">'. xtc_image(DIR_WS_IMAGES . 'top_credits.gif', '', '', '').'</a>'; ?>
    </td>
    <td align="center" class="header_bg" style="padding-top: 20px;">
    <?php echo BOX_CONFIGURATION_362.'<br />'; if (WARTUNG == 'true'){ echo xtc_image(DIR_WS_IMAGES . 'red_button.gif', 'offline', '20', '20'); } else { echo xtc_image(DIR_WS_IMAGES . 'green_button.gif', 'online', '20', '20');  }?>
    </td>
  </tr>
</table>

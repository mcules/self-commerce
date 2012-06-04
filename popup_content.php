<?php

/* -----------------------------------------------------------------------------------------
   $Id$   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003	 nextcommerce (content_preview.php,v 1.2 2003/08/25); www.nextcommerce.org
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

$content_query = xtDBquery("SELECT
 					*
 					FROM ".TABLE_CONTENT_MANAGER."
 					WHERE content_group='".(int) $_GET['coID']."' and languages_id = '".$_SESSION['languages_id']."'");
$content_data = xtc_db_fetch_array($content_query, true);
?>

<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $content_data['content_heading']; ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo 'templates/'.CURRENT_TEMPLATE.'/stylesheet.css'; ?>" />
</head>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="contentsTopics"><?php echo $content_data['content_heading']; ?></td>
  </tr>
</table>  
<br />
<table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
 <?php

if ($content_data['content_file'] != '') {
	if (strpos($content_data['content_file'], '.txt'))
		echo '<pre>';

	include (DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);

	if (strpos($content_data['content_file'], '.txt'))
		echo '</pre>';
} else {
	echo $content_data['content_text'];
}
?>
<br /><br />
<p class="smallText" align="right"><?php echo '<a href="javascript:window.close()">' . TEXT_CLOSE_WINDOW . '</a>'; ?></p>
</td>
          </tr>
        </table>
</body>
</html>
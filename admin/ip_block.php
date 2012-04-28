<?php
/* --------------------------------------------------------------
   $Id ip_block.php v 1.0 kunigunde

   Self-Commerce - Fresh up You're Ecommerce
   http://www.self-commerce.de

   Copyright (c) 2007 Self-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com 
   (c) 2003-?	 nextcommerce www.nextcommerce.org
   (c) ? xt:commerce www.xtcommerce.com
   
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $bann_id = xtc_db_prepare_input($_GET['bID']);
      $bann_ip = xtc_db_prepare_input($_POST['bann_ip']);

      $sql_data_array = array('bann_ip' => $bann_ip);

      if ($_GET['action'] == 'insert') {
        $insert_sql_data = array('date_added' => 'now()');
        $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
        xtc_db_perform(TABLE_SELF_BANNED_IP, $sql_data_array);
        $bann_id = xtc_db_insert_id();
      } elseif ($_GET['action'] == 'save') {
        $update_sql_data = array('last_modified' => 'now()');
        $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
        xtc_db_perform(TABLE_SELF_BANNED_IP, $sql_data_array, 'update', "bann_id = '" . xtc_db_input($bann_id) . "'");
      }


      if (USE_CACHE == 'true') {
        xtc_reset_cache_block('banned_ips');
      }

      xtc_redirect(xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bann_id));
      break;

    case 'deleteconfirm':
      $bann_id = xtc_db_prepare_input($_GET['bID']);


      xtc_db_query("delete from ".TABLE_SELF_BANNED_IP." where bann_id = '" . xtc_db_input($bann_id) . "'");

 
      if (USE_CACHE == 'true') {
        xtc_reset_cache_block('banned_ips');
      }

      xtc_redirect(xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page']));
      break;
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_BANNED_IP; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $banned_ips_query_raw = "select bann_id, bann_ip, date_added, last_modified from ".TABLE_SELF_BANNED_IP." order by bann_ip";
  $banned_ips_split = new splitPageResults($_GET['page'], '20', $banned_ips_query_raw, $banned_ips_query_numrows);
  $banned_ips_query = xtc_db_query($banned_ips_query_raw);
  while ($banned_ips = xtc_db_fetch_array($banned_ips_query)) {
    if (((!$_GET['bID']) || (@$_GET['bID'] == $banned_ips['bann_id'])) && (!$bannInfo) && (substr($_GET['action'], 0, 3) != 'new')) {

      $bannInfo_array = xtc_array_merge($banned_ips, $manufacturer_products);
      $bannInfo = new objectInfo($bannInfo_array);
    }

    if ( (is_object($bannInfo)) && ($banned_ips['bann_id'] == $bannInfo->bann_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_IP_BLOCK, '&bID=' . $banned_ips['bann_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $banned_ips['bann_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $banned_ips['bann_ip']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($bannInfo)) && ($banned_ips['bann_id'] == $bannInfo->bann_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $banned_ips['bann_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $banned_ips_split->display_count($banned_ips_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNED_IP); ?></td>
                    <td class="smallText" align="right"><?php echo $banned_ips_split->display_links($banned_ips_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if ($_GET['action'] != 'new') {
?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo xtc_button_link(BUTTON_INSERT, xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id . '&action=new')); ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_BANNED_IP . '</b>');

      $contents = array('form' => xtc_draw_form('banned_ips', FILENAME_IP_BLOCK, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_IP_ADRESS . '<br />' . xtc_draw_input_field('bann_ip'));

      $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_button(BUTTON_SAVE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID'])));
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_BANNED_IP . '</b>');

      $contents = array('form' => xtc_draw_form('banned_ips', FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_IP_ADRESS . '<br />' . xtc_draw_input_field('bann_ip', $bannInfo->bann_ip));

      $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_button(BUTTON_SAVE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id)));
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_BANNED_IP . '</b>');

      $contents = array('form' => xtc_draw_form('banned_ips', FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $bannInfo->bann_ip . '</b>');


      $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_button(BUTTON_DELETE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id)));
      break;

    default:
      if (is_object($bannInfo)) {
        $heading[] = array('text' => '<b>' . $bannInfo->bann_ip . '</b>');

        $contents[] = array('align' => 'center', 'text' => xtc_button_link(BUTTON_EDIT, xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id . '&action=edit')) . '&nbsp;' . xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_IP_BLOCK, 'page=' . $_GET['page'] . '&bID=' . $bannInfo->bann_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . TEXT_DATE_ADDED . ' ' . xtc_date_short($bannInfo->date_added));
        if (xtc_not_null($bannInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . xtc_date_short($bannInfo->last_modified));
      }
      break;
  }

  if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

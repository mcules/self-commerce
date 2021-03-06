<?php
/* --------------------------------------------------------------
   $Id: tax_rates.php 17 2012-06-04 20:33:29Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(tax_rates.php,v 1.28 2003/03/12); www.oscommerce.com 
   (c) 2003	 nextcommerce (tax_rates.php,v 1.9 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $tax_zone_id = xtc_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = xtc_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = xtc_db_prepare_input($_POST['tax_rate']);
        $tax_description = xtc_db_prepare_input($_POST['tax_description']);
        $tax_priority = xtc_db_prepare_input($_POST['tax_priority']);
        $date_added = xtc_db_prepare_input($_POST['date_added']);

        xtc_db_query("insert into " . TABLE_TAX_RATES . " (tax_zone_id, tax_class_id, tax_rate, tax_description, tax_priority, date_added) values ('" . xtc_db_input($tax_zone_id) . "', '" . xtc_db_input($tax_class_id) . "', '" . xtc_db_input($tax_rate) . "', '" . xtc_db_input($tax_description) . "', '" . xtc_db_input($tax_priority) . "', now())");
        xtc_redirect(xtc_href_link(FILENAME_TAX_RATES));
        break;

      case 'save':
        $tax_rates_id = xtc_db_prepare_input($_GET['tID']);
        $tax_zone_id = xtc_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = xtc_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = xtc_db_prepare_input($_POST['tax_rate']);
        $tax_description = xtc_db_prepare_input($_POST['tax_description']);
        $tax_priority = xtc_db_prepare_input($_POST['tax_priority']);
        $last_modified = xtc_db_prepare_input($_POST['last_modified']);

        xtc_db_query("update " . TABLE_TAX_RATES . " set tax_rates_id = '" . xtc_db_input($tax_rates_id) . "', tax_zone_id = '" . xtc_db_input($tax_zone_id) . "', tax_class_id = '" . xtc_db_input($tax_class_id) . "', tax_rate = '" . xtc_db_input($tax_rate) . "', tax_description = '" . xtc_db_input($tax_description) . "', tax_priority = '" . xtc_db_input($tax_priority) . "', last_modified = now() where tax_rates_id = '" . xtc_db_input($tax_rates_id) . "'");
        xtc_redirect(xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $tax_rates_id));
        break;

      case 'deleteconfirm':
        $tax_rates_id = xtc_db_prepare_input($_GET['tID']);

        xtc_db_query("delete from " . TABLE_TAX_RATES . " where tax_rates_id = '" . xtc_db_input($tax_rates_id) . "'");
        xtc_redirect(xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']));
        break;
    }
  }
require ('includes/application_top_1.php');
?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
<?php echo xtc_image(DIR_WS_ICONS.'heading_configuration.gif'); ?>
                    </td>
                    <td class="pageHeading">
<?php echo HEADING_TITLE; ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">
Configuration
                    </td>
                  </tr>
                </table></td>
            </tr>
          </table>
<!-- content -->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE_PRIORITY; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $rates_query_raw = "select r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified from " . TABLE_TAX_CLASS . " tc, " . TABLE_TAX_RATES . " r left join " . TABLE_GEO_ZONES . " z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id";
  $rates_split = new splitPageResults($_GET['page'], '20', $rates_query_raw, $rates_query_numrows);
  $rates_query = xtc_db_query($rates_query_raw);
  while ($rates = xtc_db_fetch_array($rates_query)) {
    if (((!$_GET['tID']) || (@$_GET['tID'] == $rates['tax_rates_id'])) && (!$trInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $trInfo = new objectInfo($rates);
    }

    if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $rates['tax_priority']; ?></td>
                <td class="dataTableContent"><?php echo $rates['tax_class_title']; ?></td>
                <td class="dataTableContent"><?php echo $rates['geo_zone_name']; ?></td>
                <td class="dataTableContent"><?php echo xtc_display_tax_value($rates['tax_rate']); ?>%</td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $rates_split->display_count($rates_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></td>
                    <td class="smallText" align="right"><?php echo $rates_split->display_links($rates_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="5" align="right"><?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=new') . '">' . BUTTON_NEW_TAX_RATE . '</a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAX_RATE . '</b>');

      $contents = array('form' => xtc_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . xtc_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . xtc_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE . '<br />' . xtc_draw_input_field('tax_rate'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . xtc_draw_input_field('tax_description'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE_PRIORITY . '<br />' . xtc_draw_input_field('tax_priority'));
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>&nbsp;<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAX_RATE . '</b>');

      $contents = array('form' => xtc_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . xtc_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $trInfo->tax_class_id));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . xtc_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"', $trInfo->geo_zone_id));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE . '<br />' . xtc_draw_input_field('tax_rate', $trInfo->tax_rate));
      $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . xtc_draw_input_field('tax_description', $trInfo->tax_description));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE_PRIORITY . '<br />' . xtc_draw_input_field('tax_priority', $trInfo->tax_priority));
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>&nbsp;<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '">' . BUTTON_CANCEL . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAX_RATE . '</b>');

      $contents = array('form' => xtc_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $trInfo->tax_class_title . ' ' . number_format($trInfo->tax_rate, TAX_DECIMAL_PLACES) . '%</b>');
      $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>&nbsp;<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '">' . BUTTON_CANCEL . '</a>');
      break;

    default:
      if (is_object($trInfo)) {
        $heading[] = array('text' => '<b>' . $trInfo->tax_class_title . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '">' . BUTTON_EDIT . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($trInfo->date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($trInfo->last_modified));
        $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . $trInfo->tax_description);
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
        </table>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

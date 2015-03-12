<?php
/* --------------------------------------------------------------
   $Id: imagesliders.php 001 2008-07-29 12:19:00Z Hetfield $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(imagesliders.php,v 1.52 2003/03/22); www.oscommerce.com
   (c) 2003	 nextcommerce (imagesliders.php,v 1.9 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');
  require_once(DIR_FS_INC . 'xtc_wysiwyg.inc.php');

	function xtc_get_imageslider_image($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_image from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_image'];
	}
	function xtc_get_imageslider_url($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_url from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_url'];
	}
	function xtc_get_imageslider_url_target($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_url_target from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_url_target'];
	}
	function xtc_get_imageslider_url_typ($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_url_typ from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_url_typ'];
	}
	function xtc_get_imageslider_title($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_title from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_title'];
	}
## meine
	function xtc_get_imageslider_caption_class($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_caption_class from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_caption_class'];
	}

	function xtc_get_imageslider_indicator_class($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_indicator_class from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_indicator_class'];
	}
## meine
	function xtc_get_imageslider_description($imageslider_id, $language_id) {
		$imageslider_query = xtc_db_query("select imagesliders_description from ".TABLE_IMAGESLIDERS_INFO." where imagesliders_id = '".$imageslider_id."' and languages_id = '".$language_id."'");
		$imageslider = xtc_db_fetch_array($imageslider_query);
		return $imageslider['imagesliders_description'];
	}

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $imagesliders_id = xtc_db_prepare_input($_GET['iID']);
      $imagesliders_name = xtc_db_prepare_input($_POST['imagesliders_name']);
	  $imagesliders_status = xtc_db_prepare_input($_POST['imagesliders_status']);
	  $imagesliders_sorting = xtc_db_prepare_input($_POST['imagesliders_sorting']);

      $sql_data_array = array('imagesliders_name' => $imagesliders_name,
	  						  'status' => $imagesliders_status,
							  'sorting' => $imagesliders_sorting
	  );

      if ($_GET['action'] == 'insert') {
        $insert_sql_data = array('date_added' => 'now()');
        $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
        xtc_db_perform(TABLE_IMAGESLIDERS, $sql_data_array);
        $imagesliders_id = xtc_db_insert_id();
      } elseif ($_GET['action'] == 'save') {
        $update_sql_data = array('last_modified' => 'now()');
        $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
        xtc_db_perform(TABLE_IMAGESLIDERS, $sql_data_array, 'update', "imagesliders_id = '" . xtc_db_input($imagesliders_id) . "'");
      }

      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	  	  if ($_POST['imagesliders_image_delete'. $i] == true) {
		  	 @unlink(DIR_FS_CATALOG_IMAGES.xtc_get_imageslider_image($imagesliders_id, $languages[$i]['id']));
			 $imagepfad = '';
		  }
	  	  if ($image = &xtc_try_upload('imagesliders_image'.$i, DIR_FS_CATALOG_IMAGES.'imagesliders/'.$languages[$i]['directory'].'/')) {
			 $imagepfad = 'imagesliders/'.$languages[$i]['directory'].'/'.$image->filename;
          } else {
		  	 if ($_POST['imagesliders_image_delete'. $i] == false) {
			 	$imagepfad = xtc_get_imageslider_image($imagesliders_id, $languages[$i]['id']);
			 }
		  }
		  $imagesliders_url_array = $_POST['imagesliders_url'];
		  $imagesliders_url_target_array = $_POST['imagesliders_url_target'];
		  $imagesliders_url_typ_array = $_POST['imagesliders_url_typ'];
		  $imagesliders_title_array = $_POST['imagesliders_title'];
# meine
		  $imagesliders_indicator_class_array = $_POST['imagesliders_indicator_class'];
		  $imagesliders_caption_class_array = $_POST['imagesliders_caption_class'];
# meine
		  $imagesliders_description_array = $_POST['imagesliders_description'];
          $language_id = $languages[$i]['id'];
          $sql_data_array = array('imagesliders_url' => xtc_db_prepare_input($imagesliders_url_array[$language_id]),
		  						  'imagesliders_url_target' => xtc_db_prepare_input($imagesliders_url_target_array[$language_id]),
								  'imagesliders_url_typ' => xtc_db_prepare_input($imagesliders_url_typ_array[$language_id]),
								  'imagesliders_image' => $imagepfad,
								  'imagesliders_title' => xtc_db_prepare_input($imagesliders_title_array[$language_id]),
# meine
								  'imagesliders_indicator_class' => xtc_db_prepare_input($imagesliders_indicator_class_array[$language_id]),
								  'imagesliders_caption_class' => xtc_db_prepare_input($imagesliders_caption_class_array[$language_id]),
# meine
								  'imagesliders_description' => xtc_db_prepare_input($imagesliders_description_array[$language_id]));

          if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('imagesliders_id' => $imagesliders_id,
                                     'languages_id' => $language_id);
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_IMAGESLIDERS_INFO, $sql_data_array);
          } elseif ($_GET['action'] == 'save') {
            xtc_db_perform(TABLE_IMAGESLIDERS_INFO, $sql_data_array, 'update', "imagesliders_id = '" . xtc_db_input($imagesliders_id) . "' and languages_id = '" . $language_id . "'");
          }
      }

      xtc_redirect(xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $imagesliders_id));
      break;

    case 'deleteconfirm':
      $imagesliders_id = xtc_db_prepare_input($_GET['iID']);

      if ($_POST['delete_image'] == 'on') {
		  $languages = xtc_get_languages();
		  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		     $image_location = DIR_FS_CATALOG_IMAGES.xtc_get_imageslider_image($imagesliders_id, $languages[$i]['id']);
			 if (file_exists($image_location)) {
			 	@unlink($image_location);
			 }
		  }
      }

      xtc_db_query("delete from " . TABLE_IMAGESLIDERS . " where imagesliders_id = '" . xtc_db_input($imagesliders_id) . "'");
      xtc_db_query("delete from " . TABLE_IMAGESLIDERS_INFO . " where imagesliders_id = '" . xtc_db_input($imagesliders_id) . "'");

      xtc_redirect(xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page']));
      break;

	  case 'setflag':
	  	$imagesliders_id = xtc_db_prepare_input((int)$_GET['iID']);
		$imagesliders_status = xtc_db_prepare_input((int)$_GET['flag']);
	  	xtc_db_query("UPDATE " . TABLE_IMAGESLIDERS . " SET status = ".xtc_db_input($imagesliders_status)." WHERE imagesliders_id = '" . xtc_db_input($imagesliders_id) . "'");
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
<?php
if (USE_WYSIWYG == 'true') {
	$query = xtc_db_query("SELECT code FROM ".TABLE_LANGUAGES." WHERE languages_id='".$_SESSION['languages_id']."'");
	$data = xtc_db_fetch_array($query);
	// generate editor for imagesliders
	$languages = xtc_get_languages();
?>
<script type="text/javascript" src="includes/modules/fckeditor/fckeditor.js"></script>
<script type="text/javascript">
	window.onload = function()
		{<?php
	// generate editor for categories
	if ($_GET['action'] == 'new' || $_GET['action'] == 'edit') {
		for ($i = 0; $i < sizeof($languages); $i ++) {
			echo xtc_wysiwyg('imagesliders_description', $data['code'], $languages[$i]['id']);
		}
	}
?>}
</script><?php
}
?>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
<?php
if (($_GET['action'] != 'new') && ($_GET['action'] != 'edit')) {
?>
            <td valign="top">
             <table border="0" width="100%" cellspacing="0" cellpadding="2">
			   <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_IMAGESLIDERS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SORTING; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
}
  $imagesliders_query_raw = "select imagesliders_id, imagesliders_name, status, sorting, date_added, last_modified from " . TABLE_IMAGESLIDERS . " order by sorting";
  $imagesliders_split = new splitPageResults($_GET['page'], '20', $imagesliders_query_raw, $imagesliders_query_numrows);
  $imagesliders_query = xtc_db_query($imagesliders_query_raw);
  while ($imagesliders = xtc_db_fetch_array($imagesliders_query)) {
    if (((!$_GET['iID']) || (@$_GET['iID'] == $imagesliders['imagesliders_id'])) && (!$iInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      	$iInfo = new objectInfo($imagesliders);
	  	$iInfo->imagesliders_image = xtc_get_imageslider_image($iInfo->imagesliders_id, $language_id);
    }
    if (($_GET['action'] != 'new') && ($_GET['action'] != 'edit')) {
		if ( (is_object($iInfo)) && ($imagesliders['imagesliders_id'] == $iInfo->imagesliders_id) ) {
			echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $imagesliders['imagesliders_id'] . '&action=edit') . '\'">' . "\n";
		} else {
			echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $imagesliders['imagesliders_id']) . '\'">' . "\n";
		}
?>
                <td class="dataTableContent"><?php echo $imagesliders['imagesliders_name']; ?></td>
                <td class="dataTableContent"><?php echo $imagesliders['sorting']; ?></td>
                <td class="dataTableContent">
<?php
                if ($imagesliders['status'] == '0') {
                     echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . xtc_href_link(FILENAME_IMAGESLIDERS, 'action=setflag&flag=1&iID=' . $imagesliders['imagesliders_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                } else {
                     echo '<a href="' . xtc_href_link(FILENAME_IMAGESLIDERS, 'action=setflag&flag=0&iID=' . $imagesliders['imagesliders_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                }
?>
                </td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($iInfo)) && ($imagesliders['imagesliders_id'] == $iInfo->imagesliders_id) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $imagesliders['imagesliders_id']) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
  }
if (($_GET['action'] != 'new') && ($_GET['action'] != 'edit')) {
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $imagesliders_split->display_count($imagesliders_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_IMAGESLIDERS); ?></td>
                    <td class="smallText" align="right"><?php echo $imagesliders_split->display_links($imagesliders_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="right" colspan="4" class="smallText"><?php echo xtc_button_link(BUTTON_INSERT, xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id . '&action=new')); ?></td>
              </tr>
            </table></td>
<?php
}
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_IMAGESLIDER . '</b>');

      $contents = array('form' => xtc_draw_form('imagesliders', FILENAME_IMAGESLIDERS, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => ''.TEXT_NEW_INTRO.'');
      $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="150px" valign="top">' . TEXT_IMAGESLIDERS_NAME . '</td><td class="infoBoxContent"  valign="top">' . xtc_draw_input_field('imagesliders_name', '', 'style="width:99%;"').'</td></tr></table>');
      $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="150px" valign="top">' . TABLE_HEADING_SORTING . ':</td><td class="infoBoxContent"  valign="top">' . xtc_draw_input_field('imagesliders_sorting', '', 'style="width:99%;"').'</td></tr></table>');
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="150px" valign="top">' . TABLE_HEADING_STATUS . ':</td><td class="infoBoxContent"  valign="top">' . xtc_draw_selection_field('imagesliders_status', 'radio', '0').ACTIVE.'&nbsp;&nbsp;&nbsp;'.xtc_draw_selection_field('imagesliders_status', 'radio', '1').NOTACTIVE.'</td></tr></table>');

      $languages = xtc_get_languages();

	  $imageslider_image_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	    $imageslider_image_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . xtc_draw_file_field('imagesliders_image'.$i) . '</td></tr></table>';
	  }
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_IMAGE . '</td></tr></table>' . $imageslider_image_string);

	  $imageslider_url_string = '';
	  $url_target_array   = array ();
	  $url_target_array[] = array ('id' => '0', 'text' => NONE_TARGET);
	  $url_target_array[] = array ('id' => '1', 'text' => TARGET_BLANK);
	  $url_target_array[] = array ('id' => '2', 'text' => TARGET_TOP);
	  $url_target_array[] = array ('id' => '3', 'text' => TARGET_SELF);
	  $url_target_array[] = array ('id' => '4', 'text' => TARGET_PARENT);
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_url_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . TEXT_TYP . '<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '0').TYP_EXTERN.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '1').TYP_INTERN.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '2').TYP_PRODUCT.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '3').TYP_CATEGORIE.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '4').TYP_CONTENT.'<br /><br />'.
									TEXT_URL . xtc_draw_input_field('imagesliders_url[' . $languages[$i]['id'] . ']', '', 'style="width:50%;"') . '&nbsp;' . TEXT_TARGET . '&nbsp;' . xtc_draw_pull_down_menu('imagesliders_url_target[' . $languages[$i]['id'] . ']', $url_target_array) . '<br /><br /></td></tr></table>';
	  }
      $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_URL .'</td></tr></table>' . $imageslider_url_string);

	  $imageslider_title_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_title_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td>' . xtc_draw_input_field('imagesliders_title[' . $languages[$i]['id'] . ']', '', 'style="width:99%;"').'</td></tr></table>';
	  }
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="30%" valign="top">' . TEXT_IMAGESLIDERS_TITLE .'</td></tr></table>' .  $imageslider_title_string);

# meine
	  $imageslider_indicator_class_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_indicator_class_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . TEXT_IMAGESLIDERS_INDICATOR_CLASS . '<br />'.
									xtc_draw_selection_field('imagesliders_indicator_class[' . $languages[$i]['id'] . ']', 'radio', '0').CLASS_NO.'<br />'.
									xtc_draw_selection_field('imagesliders_indicator_class[' . $languages[$i]['id'] . ']', 'radio', '1').CLASS_WHITE.'<br /></td></tr></table>';
	  }
	  $contents[] = array('text' =>  '<hr>' . $imageslider_indicator_class_string . '<hr>');
# meine

      $imageslider_description_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_description_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td>' . xtc_draw_textarea_field('imagesliders_description['.$languages[$i]['id'].']', 'soft', '70', '25', '', 'style="width: 99%;"').'</td></tr></table>';
# meine
        $imageslider_description_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . TEXT_IMAGESLIDERS_CAPTION_CLASS . '<br />'.
									xtc_draw_selection_field('imagesliders_caption_class[' . $languages[$i]['id'] . ']', 'radio', '0').CLASS_ABSOLUTE.'<br />'.
									xtc_draw_selection_field('imagesliders_caption_class[' . $languages[$i]['id'] . ']', 'radio', '1').CLASS_RELATIVE.'<br /></td></tr></table><hr>';
# meine
	  }
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_DESCRIPTION .'</td></tr></table>' .  $imageslider_description_string);

	  $contents[] = array('align' => 'right', 'text' => '<br />' . xtc_button(BUTTON_SAVE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $_GET['iID'])));
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_IMAGESLIDER . '</b>');

      $contents = array('form' => xtc_draw_form('imagesliders', FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="150px" valign="top">' . TEXT_IMAGESLIDERS_NAME . '</td><td class="infoBoxContent"  valign="top">' . xtc_draw_input_field('imagesliders_name', $iInfo->imagesliders_name, 'style="width:99%;"').'</td></tr></table>');
      $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="150px" valign="top">' . TABLE_HEADING_SORTING . ':</td><td class="infoBoxContent"  valign="top">' . xtc_draw_input_field('imagesliders_sorting', $iInfo->sorting, 'style="width:99%;"').'</td></tr></table>');
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="150px" valign="top">' . TABLE_HEADING_STATUS . ':</td><td class="infoBoxContent"  valign="top">' . xtc_draw_selection_field('imagesliders_status', 'radio', '0',$iInfo->status==0 ? true : false).ACTIVE.'&nbsp;&nbsp;&nbsp;'.xtc_draw_selection_field('imagesliders_status', 'radio', '1',$iInfo->status==1 ? true : false).NOTACTIVE.'</td></tr></table>');

	  $languages = xtc_get_languages();

      $imageslider_image_string = '';
	  $image = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
	    $imageslider_image_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . xtc_draw_file_field('imagesliders_image'.$i) . '<br />'.  xtc_info_image(xtc_get_imageslider_image($iInfo->imagesliders_id, $languages[$i]['id']), $iInfo->imagesliders_name) . '<br />' . xtc_draw_selection_field('imagesliders_image_delete'. $i, 'checkbox', 'imagesliders_image'. $i) .' '. TEXT_HEADING_DELETE_IMAGESLIDER .'</td></tr></table>';
	  }
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_IMAGE . '</td></tr></table>' . $imageslider_image_string);

	  $imageslider_url_string = '';
	  $url_target_array   = array ();
	  $url_target_array[] = array ('id' => '0', 'text' => NONE_TARGET);
	  $url_target_array[] = array ('id' => '1', 'text' => TARGET_BLANK);
	  $url_target_array[] = array ('id' => '2', 'text' => TARGET_TOP);
	  $url_target_array[] = array ('id' => '3', 'text' => TARGET_SELF);
	  $url_target_array[] = array ('id' => '4', 'text' => TARGET_PARENT);
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_url_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . TEXT_TYP . '<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '0',xtc_get_imageslider_url_typ($iInfo->imagesliders_id, $languages[$i]['id'])==0 ? true : false).TYP_EXTERN.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '1',xtc_get_imageslider_url_typ($iInfo->imagesliders_id, $languages[$i]['id'])==1 ? true : false).TYP_INTERN.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '2',xtc_get_imageslider_url_typ($iInfo->imagesliders_id, $languages[$i]['id'])==2 ? true : false).TYP_PRODUCT.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '3',xtc_get_imageslider_url_typ($iInfo->imagesliders_id, $languages[$i]['id'])==3 ? true : false).TYP_CATEGORIE.'<br />'.
									xtc_draw_selection_field('imagesliders_url_typ[' . $languages[$i]['id'] . ']', 'radio', '4',xtc_get_imageslider_url_typ($iInfo->imagesliders_id, $languages[$i]['id'])==4 ? true : false).TYP_CONTENT.'<br /><br />'.
									TEXT_URL . xtc_draw_input_field('imagesliders_url[' . $languages[$i]['id'] . ']', xtc_get_imageslider_url($iInfo->imagesliders_id, $languages[$i]['id']), 'style="width:50%;"') . '&nbsp;' . TEXT_TARGET . '&nbsp;' . xtc_draw_pull_down_menu('imagesliders_url_target[' . $languages[$i]['id'] . ']', $url_target_array, xtc_get_imageslider_url_target($iInfo->imagesliders_id, $languages[$i]['id'])) . '<br /><br /></td></tr></table>';
	  }
      $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_URL .'</td></tr></table>' . $imageslider_url_string);

	  $imageslider_title_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_title_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td>' . xtc_draw_input_field('imagesliders_title[' . $languages[$i]['id'] . ']', xtc_get_imageslider_title($iInfo->imagesliders_id, $languages[$i]['id']), 'style="width:99%;"').'</td></tr></table>';
	  }
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_TITLE .'</td></tr></table>' . $imageslider_title_string);

# meine
	  $imageslider_indicator_class_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_indicator_class_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . TEXT_IMAGESLIDERS_INDICATOR_CLASS . '<br />'.
									xtc_draw_selection_field('imagesliders_indicator_class[' . $languages[$i]['id'] . ']', 'radio', '0',xtc_get_imageslider_indicator_class($iInfo->imagesliders_id, $languages[$i]['id'])==0 ? true : false).CLASS_NO.'<br />'.
									xtc_draw_selection_field('imagesliders_indicator_class[' . $languages[$i]['id'] . ']', 'radio', '1',xtc_get_imageslider_indicator_class($iInfo->imagesliders_id, $languages[$i]['id'])==1 ? true : false).CLASS_WHITE.'<br /></td></tr></table>';
	  }
	  $contents[] = array('text' =>  '<hr>' . $imageslider_indicator_class_string . '<hr>');
# meine

	  $imageslider_description_string = '';
	  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $imageslider_description_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td>' . xtc_draw_textarea_field('imagesliders_description['.$languages[$i]['id'].']','soft','70','25',(($imageslider_description[$languages[$i]['id']]) ? stripslashes($imageslider_description[$languages[$i]['id']]) : xtc_get_imageslider_description($iInfo->imagesliders_id, $languages[$i]['id'])), 'style="width:99%;"').'</td></tr></table>';
# meine
        $imageslider_description_string .= '<table width="100%"><tr><td class="infoBoxContent" width="1%" valign="top">' . xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</td><td class="infoBoxContent">' . TEXT_IMAGESLIDERS_CAPTION_CLASS . '<br />'.
									xtc_draw_selection_field('imagesliders_caption_class[' . $languages[$i]['id'] . ']', 'radio', '0',xtc_get_imageslider_caption_class($iInfo->imagesliders_id, $languages[$i]['id'])==0 ? true : false).CLASS_ABSOLUTE.'<br />'.
									xtc_draw_selection_field('imagesliders_caption_class[' . $languages[$i]['id'] . ']', 'radio', '1',xtc_get_imageslider_caption_class($iInfo->imagesliders_id, $languages[$i]['id'])==1 ? true : false).CLASS_RELATIVE.'<br /></td></tr></table><hr>';
# meine
	  }
	  $contents[] = array('text' => '<table width="100%"><tr><td class="infoBoxContent" width="100%" valign="top">' . TEXT_IMAGESLIDERS_DESCRIPTION .'</td></tr></table>' . $imageslider_description_string);

      $contents[] = array('align' => 'right', 'text' => '<br />' . xtc_button(BUTTON_SAVE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id)));
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_IMAGESLIDER . '</b>');

      $contents = array('form' => xtc_draw_form('imagesliders', FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $iInfo->imagesliders_name . '</b>');
      $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('delete_image', '', true) . ' ' . TEXT_DELETE_IMAGE);
	  $contents[] = array('align' => 'left', 'text' => '<br />' . xtc_button(BUTTON_DELETE) . '&nbsp;' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id)));
      break;

    default:
      if (is_object($iInfo)) {
        $heading[] = array('text' => '<b>' . $iInfo->imagesliders_name . '</b>');

        $contents[] = array('align' => 'left', 'text' => xtc_button_link(BUTTON_EDIT, xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id . '&action=edit')) . '&nbsp;' . xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_IMAGESLIDERS, 'page=' . $_GET['page'] . '&iID=' . $iInfo->imagesliders_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . TEXT_DATE_ADDED . ' ' . xtc_date_short($iInfo->date_added));
        if (xtc_not_null($iInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . xtc_date_short($iInfo->last_modified));
        $contents[] = array('text' => '<br />' . xtc_get_imageslider_image($iInfo->imagesliders_id, $languages[$i]['id']));
      }
      break;
  }

  if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {

	  //if ($_GET['action'] == 'new' || $_GET['action'] == 'edit') {
      echo '           </tr><tr><td valign="top">' . "\n";
		//} else {
		//  echo '           <td width="25%" valign="top">' . "\n";
		//}

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
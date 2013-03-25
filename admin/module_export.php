<?php
/* --------------------------------------------------------------
   $Id: module_export.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(modules.php,v 1.45 2003/05/28); www.oscommerce.com
   (c) 2003	 nextcommerce (modules.php,v 1.23 2003/08/19); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
  require_once(DIR_WS_FUNCTIONS . 'export_functions.php');

      if (!is_writeable(DIR_FS_CATALOG . 'export/')) {
      		$messageStack->add(ERROR_EXPORT_FOLDER_NOT_WRITEABLE, 'error');
      }
      $module_type = 'export';
      $module_directory = DIR_WS_MODULES . 'export/';
      $module_key = 'MODULE_EXPORT_INSTALLED';
      $file_extension = '.php';
      define('HEADING_TITLE', HEADING_TITLE_MODULES_EXPORT);
      if (isset($_GET['error'])) {
      $map='error';
      if ($_GET['kind']=='success') $map='success';
      $messageStack->add($_GET['error'], $map);
      }

  switch ($_GET['action']) {
  	//BOF NEW IMAGE PROCESSING
      case 'image_processing_do':
        $class = basename($_GET['module']);
        include($module_directory . $class . $file_extension);
        if($class == 'image_processing_step'){
          $module = new $class;
          $module->process($file, $_GET['start']);
          $link = xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module='. $class. '&start=' . $limit.'&count='.$count.'&action=image_processing_do'.'&max='. $_GET['max']. '&miss='. $_GET['miss']);
        }
        break;
    //EOF NEW IMAGE PROCESSING
    case 'save':
    if (is_array($_POST['configuration'])) {
    if (count($_POST['configuration'])) {
      while (list($key, $value) = each($_POST['configuration'])) {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
        if (substr($key,'FILE')) $file=$value;
      }
    }
    }

      $class = basename($_GET['module']);
      include($module_directory . $class . $file_extension);
      //BOF NEW IMAGE PROCESSING
      if (isset($_POST['process']) && $_POST['process'] == 'image_processing_do') {
	      xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module='. $class. '&start=0'.'&count='.$count.'&action=image_processing_do'.'&max='. $_POST['max_images']. '&miss='. $_POST['only_missing_images']));
      } else {
      //EOF NEW IMAGE PROCESSING
	      $module = new $class;
	      $module->process($file);
	      xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class));
      //BOF NEW IMAGE PROCESSING
      }
      //EOF NEW IMAGE PROCESSING
      break;

    case 'install':
    case 'remove':
      $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
      $class = basename($_GET['module']);
      if (file_exists($module_directory . $class . $file_extension)) {
        include($module_directory . $class . $file_extension);
        $module = new $class;
        if ($_GET['action'] == 'install') {
          $module->install();
        } elseif ($_GET['action'] == 'remove') {
          $module->remove();
        }
      }
      xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class));
      break;
  }
require ('includes/application_top_1.php');
//BOF NEW IMAGE PROCESSING
echo '<form name="img_continue" id="img_continue" action="'.$link.'" method="POST"></form>';
if ($selbstaufruf != '') echo $selbstaufruf;
//EOF NEW IMAGE PROCESSING
?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
<?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?>
                    </td>
                    <td class="pageHeading">
<?php echo HEADING_TITLE; ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">
XT Modules
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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
  $directory_array = array();
  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  $installed_modules = array();
  for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
    $file = $directory_array[$i];

 //   include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $file);
    include($module_directory . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (xtc_class_exists($class)) {
      $module = new $class;
      if ($module->check() > 0) {
        if ($module->sort_order > 0) {
          $installed_modules[$module->sort_order] = $file;
        } else {
          $installed_modules[] = $file;
        }
      }

      if (((!$_GET['module']) || ($_GET['module'] == $class)) && (!$mInfo)) {
        $module_info = array('code' => $module->code,
                             'title' => $module->title,
                             'description' => $module->description,
                             'status' => $module->check());

        $module_keys = $module->keys();
        $keys_extra = array();
        for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
          $key_value_query = xtc_db_query("select configuration_key,configuration_value, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_keys[$j] . "'");
          $key_value = xtc_db_fetch_array($key_value_query);
          if ($key_value['configuration_key'] !='')  $keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] .'_TITLE'));
          $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
          if ($key_value['configuration_key'] !='')  $keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] .'_DESC'));
          $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
          $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
        }

        $module_info['keys'] = $keys_extra;

        $mInfo = new objectInfo($module_info);
      }

      if ( (is_object($mInfo)) && ($class == $mInfo->code) ) {
        if ($module->check() > 0) {
          echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class . '&action=edit') . '\'">' . "\n";
        } else {
          echo '              <tr class="dataTableRowSelected">' . "\n";
        }
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $module->title; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($mInfo)) && ($class == $mInfo->code) ) { echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class) . '">' . xtc_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
  }

  ksort($installed_modules);
  $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_key . "'");
  if (xtc_db_num_rows($check_query)) {
    $check = xtc_db_fetch_array($check_query);
    if ($check['configuration_value'] != implode(';', $installed_modules)) {
      xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode(';', $installed_modules) . "', last_modified = now() where configuration_key = '" . $module_key . "'");
    }
  } else {
    xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ( '" . $module_key . "', '" . implode(';', $installed_modules) . "','6', '0', now())");
  }
?>
              <tr>
                <td colspan="3" class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' admin/' . $module_directory; ?></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'edit':
      $keys = '';
      reset($mInfo->keys);
      while (list($key, $value) = each($mInfo->keys)) {
	 // if($value['description']!='_DESC' && $value['title']!='_TITLE'){
        $keys .= '<b>' . $value['title'] . '</b><br />' .  $value['description'].'<br />';
	//	}
        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
        } else {
          $keys .= xtc_draw_input_field('configuration[' . $key . ']', $value['value']);
        }
        $keys .= '<br /><br />';
      }
      $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');
      $class = substr($file, 0, strrpos($file, '.'));
      $module = new $_GET['module'];
      $contents = array('form' => xtc_draw_form('modules', FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save','post'));
      $contents[] = array('text' => $keys);
      // display module fields
      $contents[] = $module->display();

      break;

    default:
      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

      if ($mInfo->status == '1') {
        $keys = '';
        reset($mInfo->keys);
        while (list(, $value) = each($mInfo->keys)) {
          $keys .= '<b>' . $value['title'] . '</b><br />';
          if ($value['use_function']) {
            $use_function = $value['use_function'];
            if (preg_match('/->/', $use_function)) {
              $class_method = explode('->', $use_function);
              if (!is_object(${$class_method[0]})) {
                include(DIR_WS_CLASSES . $class_method[0] . '.php');
                ${$class_method[0]} = new $class_method[0]();
              }
              $keys .= xtc_call_function($class_method[1], $value['value'], ${$class_method[0]});
            } else {
              $keys .= xtc_call_function($use_function, $value['value']);
            }
          } else {
		  if(strlen($value['value']) > 30) {
		  $keys .=  substr($value['value'],0,30) . ' ...';
		  } else {
            $keys .=  $value['value'];
			}
          }
          $keys .= '<br /><br />';
        }
        $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=remove') . '">' . BUTTON_MODULE_REMOVE . '</a> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=edit') . '">' . BUTTON_START . '</a>');
        $contents[] = array('text' => '<br />' . $mInfo->description);
        $contents[] = array('text' => '<br />' . $keys);
      } else {
        $contents[] = array('align' => 'center', 'text' => '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=install') . '">' . BUTTON_MODULE_INSTALL . '</a>');
        $contents[] = array('text' => '<br />' . $mInfo->description);
      }
      break;
  }

  if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);
    //BOF NEW IMAGE PROCESSING
    if ($_GET['action']=='image_processing_do') {
      echo $infotext;
    }
    if (isset($_GET['infotext'])){
       echo '<div style="margin:10px; font-family:Verdana; font-size:15px; text-align:center;">'. urldecode($_GET['infotext']) .'</div>';
    }
    //EOF NEW IMAGE PROCESSING
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

<?php
/* --------------------------------------------------------------
   $Id$
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com
   Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
  require(DIR_WS_CLASSES . 'import.php');
  require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');
  define('FILENAME_CSV_BACKEND','csv_backend.php');
  switch ($_GET['action']) {
      case 'upload':
        $upload_file=xtc_db_prepare_input($_POST['file_upload']);
        if ($upload_file = &xtc_try_upload('file_upload',DIR_FS_CATALOG.'import/')) {
            $$upload_file_name=$upload_file->filename;
        }
      break;
      case 'import':
           $handler = new xtcImport($_POST['select_file']);
           $mapping=$handler->map_file($handler->generate_map());
           $import=$handler->import($mapping);
      break;
      case 'export':
            $handler = new xtcExport('export.csv');
            $import=$handler->exportProdFile();
      break;
      case 'save':
          $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");
          while ($configuration = xtc_db_fetch_array($configuration_query))
              xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");
               xtc_redirect(FILENAME_CSV_BACKEND);
        break;
  }
  $cfg_group_query = xtc_db_query("select configuration_group_title from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = '20'");
  $cfg_group = xtc_db_fetch_array($cfg_group_query);
require ('includes/application_top_1.php');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="80" rowspan="2">
            <?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></td>
          <td class="pageHeading"> CSV Import/Export</td>
        </tr>
        <tr>
          <td class="main" valign="top"> XT Tools</td>
        </tr>
      </table></td>
  </tr>
</table>
<!-- content -->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="main">
      <table class="infoBoxHeading" width="100%">
        <tr>
          <td>
            <?php echo xtc_draw_form('configuration', FILENAME_CSV_BACKEND, 'gID=20&action=save'); ?>
            <table width="100%"  border="0" cellspacing="0" cellpadding="4">
<?php
  $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");
  while ($configuration = xtc_db_fetch_array($configuration_query)) {
    if ($_GET['gID'] == 6) {
      switch ($configuration['configuration_key']) {
        case 'MODULE_PAYMENT_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $payment_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
            }
          }
          break;
        case 'MODULE_SHIPPING_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $shipping_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);
            }
          }
          break;
        case 'MODULE_ORDER_TOTAL_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $ot_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);
            }
          }
          break;
      }
    }
    if (xtc_not_null($configuration['use_function'])) {
      $use_function = $configuration['use_function'];
      if (ereg('->', $use_function)) {
        $class_method = explode('->', $use_function);
        if (!is_object(${$class_method[0]})) {
          include(DIR_WS_CLASSES . $class_method[0] . '.php');
          ${$class_method[0]} = new $class_method[0]();
        }
        $cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
      } else {
        $cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
      }
    } else {
      $cfgValue = $configuration['configuration_value'];
    }
    if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cfg_extra_query = xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
      $cfg_extra = xtc_db_fetch_array($cfg_extra_query);
      $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
      $cInfo = new objectInfo($cInfo_array);
    }
    if ($configuration['set_function']) {
        eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars($configuration['configuration_value']) . '");');
      } else {
        $value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=40');
      }
   // add
   if (strstr($value_field,'configuration_value')) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);
   echo '
  <tr>
    <td width="300" valign="top" class="dataTableContent"><b>'.constant(strtoupper($configuration['configuration_key'].'_TITLE')).'</b></td>
    <td valign="top" class="dataTableContent">
    <table width="100%"  border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td style="background-color:#FCF2CF ; border: 1px solid; border-color: #CCCCCC;" class="dataTableContent">'.$value_field.'</td>
      </tr>
    </table>
    <br />'.constant(strtoupper( $configuration['configuration_key'].'_DESC')).'</td>
  </tr>
  ';
  }
              ?>
            </table>
            <?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?>
            </form></td>
        </tr>
      </table>
<?php
  if ($import)
  {
     if ($import[0])
     {
      echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="messageStackSuccess"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    ';
                   if (isset($import[0]['prod_new'])) echo 'new products:'.$import[0]['prod_new'].'<br />';
                   if (isset($import[0]['cat_new'])) echo 'new categories:'.$import[0]['cat_new'].'<br />';
                   if (isset($import[0]['prod_upd'])) echo 'updated products:'.$import[0]['prod_upd'].'<br />';
                   if (isset($import[0]['cat_upd'])) echo 'updated categories:'.$import[0]['cat_upd'].'<br />';
                   if (isset($import[0]['cat_touched'])) echo 'touched categories:'.$import[0]['cat_touched'].'<br />';
                   if (isset($import[0]['prod_exp'])) echo 'products exported:'.$import[0]['prod_exp'].'<br />';
                   if (isset($import[2])) echo $import[2];
      echo '</font></td>
                </tr>
                </table>';
     }
     if (isset($import[1]) && $import[1][0]!='')
     {
      echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="messageStackError"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                    ';
                   for ($i=0;$i<count($import[1]);$i++)
                   {
                    echo $import[1][$i].'<br />';
                   }
      echo '</font></td>
                </tr>
                </table>';
     }
  }
      ?>
      <table width="100%"  border="0" cellspacing="5" cellpadding="0">
        <tr>
          <td class="pageHeading">
            IMPORT</td>
        </tr>
        <tr>
          <td class="dataTableHeadingContent">
            <?php echo TEXT_IMPORT; ?>
            <table width="100%"  border="0" cellspacing="2" cellpadding="0">
              <tr>
                <td width="93%" class="infoBoxHeading">
                  <?php echo UPLOAD; ?></td>
              </tr>
              <tr>
                <td>
<?php
echo xtc_draw_form('upload',FILENAME_CSV_BACKEND,'action=upload','POST','enctype="multipart/form-data"');
echo xtc_draw_file_field('file_upload');
echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPLOAD . '"/>';
                  ?>
                  </form></td>
              </tr>
              <tr>
                <td class="infoBoxHeading">
                  <?php echo SELECT; ?></td>
              </tr>
              <tr>
                <td>
<?php
          $files=array();
          echo xtc_draw_form('import',FILENAME_CSV_BACKEND,'action=import','POST','enctype="multipart/form-data"');
             if ($dir= opendir(DIR_FS_CATALOG.'import/')){
             while  (($file = readdir($dir)) !==false) {
                if (is_file(DIR_FS_CATALOG.'import/'.$file) and ($file !=".htaccess"))
                {
                    $size=filesize(DIR_FS_CATALOG.'import/'.$file);
                    $files[]=array(
                        'id' => $file,
                        'text' => $file.' | '.xtc_format_filesize($size));
                }
             }
             closedir($dir);
            }
          echo xtc_draw_pull_down_menu('select_file',$files,'');
          echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_IMPORT . '"/>';
                  ?>
                  </form></td>
              </tr>
            </table></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="5" cellpadding="0">
        <tr>
          <td class="pageHeading">
            Export</td>
        </tr>
        <tr>
          <td class="infoBoxHeading">
            <?php echo TEXT_EXPORT; ?>
            <table width="100%"  border="0" cellspacing="2" cellpadding="0">
              <tr>
                <td>
<?php
echo xtc_draw_form('export',FILENAME_CSV_BACKEND,'action=export','POST','enctype="multipart/form-data"');
$content=array();
$content[]=array('id'=>'products','text'=>TEXT_PRODUCTS);
echo xtc_draw_pull_down_menu('select_content',$content,'products');
echo '<br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EXPORT . '"/>';
                  ?>
                  </form></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

<?php
/* --------------------------------------------------------------
   $Id$   

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2008 Self-Commerce

   Released under the GNU General Public License
   
   Thx an SNCJansen http://spider-nc.de for the help 
   --------------------------------------------------------------*/

//$debug = true;
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
// include needed functions
require_once(DIR_FS_INC .'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC .'xtc_get_tax_class_id.inc.php');
require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
require('new_attributes_functions.php');

$xtPrice = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
// bereits genutzte options id's ($options_used_array)
$options_used = xtc_db_query("select
					options_id
				from
					" . TABLE_PRODUCTS_ATTRIBUTES . "
				where
					products_id = '" . $_POST['current_product_id'] . "'
				group by
				  options_id
        ");
$options_used_array = array();
while ($options_use = xtc_db_fetch_array($options_used)) {
  array_push($options_used_array,$options_use['options_id']);
}
$count_used = count($options_used_array);
// optionen mit werten
$options_not_empty = xtc_db_query("select
					products_options_id
				from
					" . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . "
				group by
				  products_options_id
        ");
$options_not_empty_array = array();
while ($not_empty = xtc_db_fetch_array($options_not_empty)) {
  array_push($options_not_empty_array,$not_empty['products_options_id']);
}

// optionsnamen sql
$options = xtc_db_query("select 
                            po.products_options_id, po.products_options_name 
                          from 
                            " . TABLE_PRODUCTS_OPTIONS . " po
                          LEFT JOIN 
                            " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po
                          ON 
                            po.products_options_id = pov2po.products_options_id 
                          where 
                            po.language_id = '" . $_SESSION['languages_id'] . "'
                          group by
                            po.products_options_id 
                          order by 
                            po.products_options_name");
$options_name_array = array();

while ($options_name = xtc_db_fetch_array($options)) {
  array_push($options_name_array, array('id' => $options_name['products_options_id'], 'name' => $options_name['products_options_name']));
}
$count_names = count($options_name_array);                            
############# Titel ($titel) #########################
$titel = '<p class="pageHeading">'.$pageTitle.'</p>';
############# end Titel ($titel) #########################
$options_value_th = '
      <TH class="dataTableHeadingContent"><B>'.TABLE_HEADING_OPT_VALUE.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.SORT_ORDER.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.ATTR_MODEL.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.ATTR_STOCK.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.ATTR_WEIGHT.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.ATTR_PREFIXWEIGHT.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.ATTR_PRICE.'</B></TH>
      <TH class="dataTableHeadingContent"><B>'.ATTR_PREFIXPRICE.'</B></TH>
';
############# optionen (tabelle = $options_table) #########################
$options_table = "\n" .'';
$options_table .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="SUBMIT_ATTRIBUTES" enctype="multipart/form-data">';
$options_table .= "\n\t" .xtc_draw_hidden_field('current_product_id', $_POST['current_product_id']);;
$options_table .= "\n\t" .xtc_draw_hidden_field('action', 'change');
$options_table .= "\n\t" .xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());;
if ($cPath) $options_table .= xtc_draw_hidden_field('cPathID', $cPath);
// die gewählte neue option anzeigen
$i = 0;
while ($i < $count_names) {
	if ($_POST['new_option'] == $options_name_array[$i]['id']){
	  $options_table .= '<div class="edit_attribute">'.$options_name_array[$i]['name'].'</div>';
	  // optionswerte
	  $options_table .= '<div class="edit_options"><div>';
	  $options_table .= '';
	  $options_table .= '<table width="100%"><TR class="dataTableHeadingRow">'.$options_value_th.'</tr><tr>';
	  // alle werte der option auslesen
	  $val_new_query = "SELECT products_options_values_id FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE products_options_id = '" . $_POST['new_option'] . "' ORDER BY products_options_values_id DESC";
    $val_new_result = xtc_db_query($val_new_query);
      while ($line = xtc_db_fetch_array($val_new_result)) {
      $j++;
      $rowClass = rowClass($j);
        $val_new_query1 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '" . $line['products_options_values_id'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'";
        $val_new_result1 = xtc_db_query($val_new_query1);
        while($line = xtc_db_fetch_array($val_new_result1)) {
          $options_table .= '<TR class="' . $rowClass . '">';
          $options_table .= '<td class="main">'.xtc_draw_checkbox_field('optionValues[]', $line['products_options_values_id']) . $line['products_options_values_name'].'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_sortorder', '', 'size="4"').'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_model', '', 'size="15"').'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_stock', '', 'size="4"').'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_weight', '', 'size="10"').'</td>';
          $options_table .= '<td class="main">';
          $options_table .= '<select name="'.$line['products_options_values_id'].'_weight_prefix'.'">';
          $options_table .= '<OPTION value="+">+</option>';
          $options_table .= '<OPTION value="-">-</option>';
          $options_table .= '</select>';
          $options_table .= '</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_price', '', 'size="10"').'</td>';
          $options_table .= '<td class="main">';
          $options_table .= '<select name="'.$line['products_options_values_id'].'_prefix'.'">';
          $options_table .= '<OPTION value="+">+</option>';
          $options_table .= '<OPTION value="-">-</option>';
          $options_table .= '</select>';          
          $options_table .= '</td></tr>';
          // Download function start
          if(strtoupper($options_name_array[$i]['name']) == 'DOWNLOADS') {
            $options_table .= '<tr><td colspan="2">'.xtc_draw_pull_down_menu($line['products_options_values_id'] . '_download_file', xtc_getDownloads(), '', '').'</td>';
            $options_table .= '<td class="main">'.DL_COUNT.' '.xtc_draw_input_field($line['products_options_values_id'].'_download_count', '', 'size="10"').'</td>';
            $options_table .= '<td class="main" colspan="5">'.DL_EXPIRE.' '.xtc_draw_input_field($line['products_options_values_id'].'_download_expire', '', 'size="10"').'</td></tr>';          
          }          
        }
      if ($j == $val_new_matches ) $j = 0;
      }
    $options_table .= '</tr></table>';
	  $options_table .= '</div></div>';
	  // optionswerte
  }
  $i++;
}
// end die gewählte neue option anzeigen
// bereits aktivierte optionen
$options_table .= '<hr />'.TEXT_AKTIV_OPTIONS.'<br />';
$i = 0;
while ($i < $count_names) {
	if (in_array($options_name_array[$i]['id'], $options_used_array)){
	  $options_table .= '<div class="dhtmlgoodies_attribute_checked">'.$options_name_array[$i]['name'].'</div>';
	  // optionswerte	  
	  $options_table .= '<div class="dhtmlgoodies_options">';
	  $options_table .= '<div>';
	  $options_table .= '<table width="100%"><TR class="dataTableHeadingRow">'.$options_value_th.'</tr><tr>';
	  // alle werte der option auslesen
	  $val_akt_query = "SELECT *  FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE products_options_id = '" . $options_name_array[$i]['id'] . "' ORDER BY products_options_values_id DESC";
    $val_akt_result = xtc_db_query($val_akt_query);
      while ($line = xtc_db_fetch_array($val_akt_result)) {
      $k++;
      $rowClass = rowClass($k);
        $val_akt_query1 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '" . $line['products_options_values_id'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'";
        $val_akt_result1 = xtc_db_query($val_akt_query1);
        while($line = xtc_db_fetch_array($val_akt_result1)) {
        $isSelected = checkAttribute($line['products_options_values_id'], $_POST['current_product_id'], $options_name_array[$i]['id']);
          if ($isSelected) {
            $CHECKED = true;
            $style = 'background-color:#cbffab;';
          } else {
            $CHECKED = false;
            $style = '';
          }        
          $options_table .= '<TR class="' . $rowClass . '" style="'.$style.'">';
          $options_table .= '<td class="main">'.xtc_draw_checkbox_field('optionValues[]', $line['products_options_values_id'], $CHECKED) . $line['products_options_values_name'].'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_sortorder', $sortorder, 'size="4"').'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_model', $attribute_value_model, 'size="15"').'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_stock', $attribute_value_stock, 'size="4"').'</td>';
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_weight', $attribute_value_weight, 'size="10"').'</td>';
          $options_table .= '<td class="main">';
          $options_table .= '<select name="'.$line['products_options_values_id'].'_weight_prefix'.'">';
          $options_table .= '<OPTION value="+"'.$posCheck_weight.'>+</option>';
          $options_table .= '<OPTION value="-"'.$negCheck_weight.'>-</option>';
          $options_table .= '</select>';
          $options_table .= '</td>';
            // brutto Admin
            if (PRICE_IS_BRUTTO=='true'){
              $attribute_value_price_calculate = $xtPrice->xtcFormat(xtc_round($attribute_value_price*((100+(xtc_get_tax_rate(xtc_get_tax_class_id($_POST['current_product_id']))))/100),PRICE_PRECISION),false);
            } else {
              $attribute_value_price_calculate = xtc_round($attribute_value_price,PRICE_PRECISION);
            }
          $options_table .= '<td class="main">'.xtc_draw_input_field($line['products_options_values_id'].'_price', $attribute_value_price_calculate, 'size="10"');          
            // brutto Admin
            if (PRICE_IS_BRUTTO=='true'){
             $options_table .= TEXT_NETTO .'<b>'.$xtPrice->xtcFormat(xtc_round($attribute_value_price,PRICE_PRECISION),true).'</b>  ';
            }          
          $options_table .= '</td><td class="main">';
          $options_table .= '<select name="'.$line['products_options_values_id'].'_prefix'.'">';
          $options_table .= '<OPTION value="+"'.$posCheck.'>+</option>';
          $options_table .= '<OPTION value="-"'.$negCheck.'>-</option>';
          $options_table .= '</select>';          
          $options_table .= '</td></tr>';
          // Download function start
          if(strtoupper($options_name_array[$i]['name']) == 'DOWNLOADS') {
            $options_table .= '<tr><td colspan="2">'.xtc_draw_pull_down_menu($line['products_options_values_id'] . '_download_file', xtc_getDownloads(), $attribute_value_download_filename, '').'</td>';
            $options_table .= '<td class="main">'.DL_COUNT.' '.xtc_draw_input_field($line['products_options_values_id'].'_download_count', $attribute_value_download_count, 'size="10"').'</td>';
            $options_table .= '<td class="main" colspan="5">'.DL_EXPIRE.' '.xtc_draw_input_field($line['products_options_values_id'].'_download_expire', $attribute_value_download_expire, 'size="10"').'</td>';                                    
            $options_table .= '</tr>';                      
          }          
        }
      }    
    $options_table .= '</tr></table>';        
	  $options_table .= '</div></div>';
	  // optionswerte	  
  }
  $i++;
}
// end bereits aktivierte optionen
if($count_used > 0 || $_GET['insert']){
  $options_table .= xtc_button(BUTTON_SAVE) . ' '. xtc_button_link(BUTTON_CANCEL,'javascript:history.back()');
}else{
  $options_table .= xtc_button_link(BUTTON_CANCEL,'javascript:history.back()');
}
$options_table .= "\n" .'</form>';

############# end genutzte optionen (tabelle = $options_table) #########################

############# neue option hinzufügen (dropdown = $new_option) #########################
$new_option = '';
if(!$_GET['insert']){ // ausblenden wenn bereits neue option gewählt wurde
$new_option .= "\n" . TEXT_INSERT_NEW_OPTION.'<br />';
$new_option .= "\n" . xtc_draw_form('new_insert', FILENAME_NEW_ATTRIBUTES, '&insert=new', 'post');
$new_option .= "\n" . '<select name="new_option">';

$i = 0;
while ($i < $count_names) {
	if (in_array($options_name_array[$i]['id'], $options_used_array)){
  }else{
    // nur optionen welche auch werte enthalten
    if(in_array($options_name_array[$i]['id'], $options_not_empty_array))
	   $new_option .= "\n\t" . '<option name="' . $options_name_array[$i]['name'] . '" value="' . $options_name_array[$i]['id'] . '" >' . $options_name_array[$i]['name'] . '</option>';    
    }
  $i++;
}
 
$new_option .= "\n" . '</select>';
$new_option .= "\n" . xtc_draw_hidden_field('current_product_id', $_POST['current_product_id']);
$new_option .= "\n" . xtc_draw_hidden_field('action', 'edit');
$new_option .= "\n" . xtc_button(BUTTON_INSERT);
$new_option .= "\n" . '</form>';
}
############# end neue option hinzufügen (dropdown = $new_option) #########################

/*############################ Ausgaben #################*/
?>
<script type="text/javascript" src="includes/javascript/attribut_slide.js"></script>
<?php
echo '<tr><td>';
echo $titel; // Titel ($titel)
echo '<br />';
echo $options_table; //genutzte optionen (tabelle = $options_table)
echo '<br />'; 
echo $new_option; // neue option hinzufügen (dropdown = $new_option)
echo '</tr></td>';
/*############################ENDE Ausgaben #################*/

// only debug follow
if($debug){
echo '<pre>';
print_r($options_not_empty_array);
echo '</pre>';
echo '<br />';
}
?>

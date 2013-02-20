<?php
/* --------------------------------------------------------------
   $Id: products_attributes.php 2008-09-28 kunigunde $   

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2008 Self-Commerce

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

//$debug = true;
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
$per_page = MAX_ROW_LISTS_OPTIONS;
$sort_by = 'products_options_name, products_options_id';
$languages = xtc_get_languages();

$message = '';
// functions
function prod_opt_count($opt_id){
  $prod_option_count_sql = "SELECT DISTINCT products_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE options_id = '" . $opt_id . "'";
  $prod_option_count_query = xtc_db_query($prod_option_count_sql);
  $prod_option_count = xtc_db_num_rows($prod_option_count_query);
  return $prod_option_count;
}
function prod_val_count($val_id){
    $prod_value_count_sql = "SELECT products_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE options_values_id = '" . $val_id . "'";
    $prod_value_count_query = xtc_db_query($prod_value_count_sql);
    $prod_value_count = xtc_db_num_rows($prod_value_count_query);
    return $prod_value_count;
}
// end functions
// switch action
if ($_GET['action']) {
  switch($_GET['action']) {
      // neue option hinzufügen
      case 'add_product_options':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = $_POST['option_name'];
          xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, products_options_name, language_id) values ('" . $_POST['products_options_id'] . "', '" . $option_name[$languages[$i]['id']] . "', '" . $languages[$i]['id'] . "')");
        }
        // neue option einfügen korrekt ausgeführt
        $message .= '<div class="ok">'. TEXT_INSERT_OK .'</div>';        
        break;
      // optionsnamen ändern
      case 'update_option_name':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $option_name = $_POST['option_name'];
          xtc_db_query("update " . TABLE_PRODUCTS_OPTIONS . " set products_options_name = '" . $option_name[$languages[$i]['id']] . "' where products_options_id = '" . $_POST['option_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
        }
        // optionsnamen korrekt geändert
        $message .= '<div class="ok">'. TEXT_UPDATE_OK .'</div>';        
        break;
      // option inkl. enthaltener werte löschen (nur möglich wenn nicht von produkten genutzt)
      case 'delete_product_option':
        $del_options = xtc_db_query("select products_options_values_id from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
        while($del_options_values = xtc_db_fetch_array($del_options)){  
    	   xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['option_id'] . "'");
       	}
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $_GET['option_id'] . "'");
        $message .= '<div class="ok">'. TEXT_DELETE_OK .'</div>';
        break;
      // anzeige von produkten welche die gewählte option nutzen
      case 'show_products_used_this':
            $products = xtc_db_query("select 
                                        pa.products_id, 
                                        pd.products_name
                                      from 
                                        " . TABLE_PRODUCTS_ATTRIBUTES . " pa,
                                        " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                                      where
                                        pa.options_id = '".$_GET['option_id']."'
                                      and
                                        pa.products_id = pd.products_id
                                      and
                                        pd.language_id = '".$_SESSION['languages_id']."'
                                      group by
                                        pa.products_id   
                                      order by 
                                        pd.products_name");
        // ausgabe der produkte welche die gewählte option nutzen (inkl. Link zur attributverwaltung)                                        
        $message .= '<div class="ok">'.USED_FROM_PRODUCTS.'<br />' ;
                                                
        while ($products_use_option = xtc_db_fetch_array($products)) {
          $message .= xtc_draw_form('SELECT_PRODUCT', FILENAME_NEW_ATTRIBUTES, '', 'post');
          $message .= '<input type="hidden" name="action" value="edit">';
          $message .= '<input type="hidden" name="current_product_id" value="'.$products_use_option['products_id'].'">';
          $message .= xtc_button(BUTTON_EDIT).' ';
          $message .= $products_use_option['products_name'].'<br />';
          $message .= '</form>';
        }
        $message .= '</div>';
        // end ausgabe der produkte welche die gewählte option nutzen (inkl. Link zur attributverwaltung)        
        break;
      case 'add_product_option_values':
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $value_name = $_POST['value_name'];
          xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name) values ('" . $_POST['value_id'] . "', '" . $languages[$i]['id'] . "', '" . $value_name[$languages[$i]['id']] . "')");
        }
        xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " (products_options_id, products_options_values_id) values ('" . $_POST['option_id'] . "', '" . $_POST['value_id'] . "')");
        // optionswert erfolgreich hinzugefügt
        $message .=  '<div class="ok">'. TEXT_INSERT_OK .'</div>';
        break;
      case 'update_value':
       $value_name = $_POST['value_name'];
       for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
         xtc_db_query("update " . TABLE_PRODUCTS_OPTIONS_VALUES . " set products_options_values_name = '" . $value_name[$languages[$i]['id']] . "' where products_options_values_id = '" . $_POST['value_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
       }
       // optionswert korrekt geändert
       $message .= '<div class="ok">'. TEXT_UPDATE_OK .'</div>';
       break;
      case 'delete_value':
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_GET['value_id'] . "'");
        xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " where products_options_values_id = '" . $_GET['value_id'] . "'");
        // optionswert erfolgreich gelöscht
        $message .= '<div class="ok">'. TEXT_DELETE_OK .'</div>';        
        break;
      case 'show_products_used_val':
            $products = xtc_db_query("select 
                                        pa.products_id, 
                                        pd.products_name
                                      from 
                                        " . TABLE_PRODUCTS_ATTRIBUTES . " pa,
                                        " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                                      where
                                        pa.options_values_id = '".$_GET['value_id']."'
                                      and
                                        pa.products_id = pd.products_id
                                      and
                                        pd.language_id = '".$_SESSION['languages_id']."'
                                      group by
                                        pa.products_id   
                                      order by 
                                        pd.products_name
                                      ");
        // ausgabe der produkte welche den gewählten optionswert nutzen (inkl. Link zur attributverwaltung)                                        
        $message .= '<div class="ok">'.USED_FROM_PRODUCTS.'<br />' ;
                                                
        while ($products_use_value = xtc_db_fetch_array($products)) {
          $message .= xtc_draw_form('SELECT_PRODUCT', FILENAME_NEW_ATTRIBUTES, '', 'post');
          $message .= '<input type="hidden" name="action" value="edit">';
          $message .= '<input type="hidden" name="current_product_id" value="'.$products_use_value['products_id'].'">';
          $message .= xtc_button(BUTTON_EDIT).' ';
          $message .= $products_use_value['products_name'].'<br />';
          $message .= '</form>';
        }
        $message .= '</div>';
        // end ausgabe der produkte welche den gewählten optionswert nutzen (inkl. Link zur attributverwaltung)
        break;                                       
  }        
}  
// end switch action

// Option und Values Suche, id rückwärts finden und an Navigation übergeben
if ($_GET['search']&& $_GET['search'] != ''){
  $search_sql = "select products_options_id as po_id from " . TABLE_PRODUCTS_OPTIONS . " where  products_options_name like '%" . $_GET['search'] . "%' order by $sort_by ";
  $search_query = xtc_db_query($search_sql);
  $searcharray = array();
// Value Suche
    $searchvalues_sql = "select 
                pov2po.products_options_id as po_id
              from 
                " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
              left join 
                " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " pov2po 
              on 
                pov.products_options_values_id = pov2po.products_options_values_id 
              where 
                pov.products_options_values_name like '%" . $_GET['search'] . "%'
              group by
                pov2po.products_options_id
              order by 
                pov2po.products_options_id ";
  $searchvalues_query = xtc_db_query($searchvalues_sql);
  while ($search_vids = xtc_db_fetch_array($searchvalues_query)) {
    array_push($searcharray,$search_vids['po_id']);
  }
// end Value Suche

  while ($search_ids = xtc_db_fetch_array($search_query)) {
    array_push($searcharray,$search_ids['po_id']);
  }
  // doppelte Funde ausfiltern
  $searcharray = array_unique($searcharray);
  // anzahl der funde zählen  
  $search_count = count($searcharray);
  // where string zusammen bauen für die übergabe an die navigation
  if($search_count>=1){
    $where = 'where ';
    $i=0;
    foreach($searcharray as $for_where){
      $i++;
      if($for_where != ''){// hatte bei einer bestimmten suchanfrage eine leere id (i dont know why ;-)
        if($i==$search_count){$or='';}else{$or=' or ';}
        $where .= 'products_options_id = '.$for_where.$or;
      }
    }
    // anzahl x gefunden
    $message .= '<div class="ok">'.FOUND.' '.$search_count.'</div>';
  }else{
    // nichts gefunden
    $message .= '<div class="warning">'.NOT_FOUND.'</div>';
  }
}
// end Option und Values Suche id rückwärts finden und an Navigation übergeben

$navigation = "select products_options_id as po_id from " . TABLE_PRODUCTS_OPTIONS . " ".$where." group by products_options_id order by $sort_by ";
$navigation_query = xtc_db_query($navigation);
$num_rows = xtc_db_num_rows($navigation_query);

// Navigation
    if (!$option_page) {
      $option_page = 1;
    }
    $prev_option_page = $option_page - 1;
    $next_option_page = $option_page + 1;

    $option_page_start = ($per_page * $option_page) - $per_page;

    if ($num_rows <= $per_page) {
      $num_pages = 1;
    } else if (($num_rows % $per_page) == 0) {
      $num_pages = ($num_rows / $per_page);
    } else {
      $num_pages = ($num_rows / $per_page) + 1;
    }
    $num_pages = (int) $num_pages;

    $limit = " LIMIT $option_page_start, $per_page";
    
    $navilinks = '';
    // Previous
    if ($prev_option_page)  {
      $navilinks .= '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&search='.$_GET['search'].'&option_page=' . $prev_option_page) . '">'. xtc_image('images/icons/control_rewind.png') .'</a> | ';
    }

    for ($i = 1; $i <= $num_pages; $i++) {
      if ($i != $option_page) {
        $navilinks .= '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&search='.$_GET['search'].'&option_page=' . $i) . '">' . $i . '</a> | ';
      } else {
        $navilinks .= '<strong><font color=red>' . $i . '</font></strong> | ';
      }
    }

    // Next
    if ($option_page != $num_pages) {
      $navilinks .= '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&search='.$_GET['search'].'&option_page=' . $next_option_page) . '">'. xtc_image('images/icons/control_fastforward.png') .'</a>';
    }
// end Navigation

// Such Option Formular
$search_form = '';
$search_form .= xtc_draw_form('search', FILENAME_PRODUCTS_ATTRIBUTES, '', 'get');
$search_form .= HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('search').'</form>'; 
// end Such Option Formular

$optiontable = '';
$options_sql = $navigation.$limit; 
$options_query = xtc_db_query($options_sql);
while ($options_id = xtc_db_fetch_array($options_query)) {
  $rows++;
  // Zählen der verwendung von optionen durch Produkte
  $opt_count = prod_opt_count($options_id['po_id']);
  // end Zählen der verwendung von optionen durch Produkte OPTION_COUNT.
  // class umschreiben wenn optionsname/wert editiert wird, da sonst bei jedem klick die werte öffnen/schließen
  if(($_GET['action'] == 'update_option' || $_GET['action'] == 'update_option_value') && $_GET['option_id'] == $options_id['po_id']){
    $class_a_c = 'edit_attribute_checked';
    $class_a = 'edit_attribute';
    $class_o = 'edit_options';
  }else{
    $class_a_c = 'dhtmlgoodies_attribute_checked';
    $class_a = 'dhtmlgoodies_attribute';
    $class_o = 'dhtmlgoodies_options';
  }
  // end class umschreiben wenn optionsname/wert editiert wird, da sonst bei jedem klick die werte öffnen/schließen
  if($opt_count >=1){// option wird bereits von produkten genutzt
    $optiontable .= "\n".'<div class="'.$class_a_c.'">'."\n\t";
    $optiontable .= '<div class="selected">'."\n\t\t";
    $optiontable .= xtc_button_link(xtc_image(DIR_WS_ICONS . 'icons/info.jpg').OPTION_COUNT.$opt_count, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=show_products_used_this&option_id=' . $options_id['po_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page, 'NONSSL'));
    if ($_GET['action'] != 'update_option'){
      $optiontable .= "\n\t\t".xtc_button_link(xtc_image(DIR_WS_ICONS . 'icon_edit.gif').BUTTON_EDIT, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option&option_id=' . $options_id['po_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page, 'NONSSL'));
    }
    $optiontable .= "\n\t".'</div>'."\n";  
  }else{
    $optiontable .= "\n".'<div class="'.$class_a.'">';
    $optiontable .= '<div class="unselected">';
    if ($_GET['action'] != 'update_option'){
      $optiontable .= "\n\t\t".xtc_button_link(xtc_image(DIR_WS_ICONS . 'cross.gif').BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_product_option&option_id=' . $options_id['po_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page, 'NONSSL').'" onClick="return confirm(\''.CONFIRM_DELETE.'\n\n'.CONFIRM_DELETE1.'\n'.CONFIRM_DELETE2.'\')');    
      $optiontable .= "\n\t\t".xtc_button_link(xtc_image(DIR_WS_ICONS . 'icon_edit.gif').BUTTON_EDIT, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option&option_id=' . $options_id['po_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page, 'NONSSL'));
    }
    $optiontable .= "\n\t".'</div>'."\n";  
  }
  // optionen inkl. Namen
  $lang_names = '';
  if (($_GET['action'] == 'update_option') && ($_GET['option_id'] == $options_id['po_id'])) {
    
    $lang_names .= xtc_draw_form('option', FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_name&search='.$_GET['search'].'&option_page='.$option_page, 'post');
    $lang_names .= '<input type="hidden" name="option_id" value="'.$options_id['po_id'].'">';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
      $option_name = xtc_db_query("select products_options_name as po_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $options_id['po_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
      $option_name = xtc_db_fetch_array($option_name);
      $lang_names .= $languages[$i]['code'] . ': <input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="20" value="' . $option_name['po_name'] . '"> <br />';
    }
    $lang_names .= xtc_button(BUTTON_UPDATE);
    $lang_names .= xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&search='.$_GET['search'].'&option_page='.$option_page));
    $lang_names .= '</form>';
  }else{
    for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
      $option_name = xtc_db_query("select products_options_name as po_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $options_id['po_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
      $option_name = xtc_db_fetch_array($option_name);
      $lang_names .= "\t".$languages[$i]['code'] . ': ' . $option_name['po_name'].'<br />'."\n";
    }
  }
  $optiontable .= $lang_names.'</div>'."\n";
  
  $optiontable .= '<div class="'.$class_o.'">'."\n\t".'<div>'."\n\t"; 
  // end optionen inkl. Namen
  // Werte der jeweiligen Attribute
    $values_id_sql = "select 
                products_options_values_id as pov_id
              from 
                " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " 
              where 
                products_options_id = '" . $options_id['po_id'] . "' 
              order by 
                products_options_values_id ";
    $values_id_query = xtc_db_query($values_id_sql);
// value tabelle    
    $values_table = '';
  // nicht anzeigen wenn optionsname bearbeitet wird
if ($_GET['action'] != 'update_option') {
  $num_rows_val = xtc_db_num_rows($values_id_query);
  if($num_rows_val >= 1){  
    $values_table .= '<table width="100%" id="values">';
    $values_table .= '<TR class="dataTableHeadingRow">';
    $values_table .= '<TD class="dataTableHeadingContent" align="center">'.TABLE_HEADING_ID.'</TD>';
    $values_table .= '<TD class="dataTableHeadingContent">'.TABLE_HEADING_OPT_VALUE.'</td>';
    $values_table .= '<TD class="dataTableHeadingContent" align="center">'.TABLE_HEADING_ACTION.'</td>';
    $values_table .= '</tr>';                
    while ($values_id = xtc_db_fetch_array($values_id_query)) {
  // Zählen der verwendung von values durch Produkte (ist jedoch nur nötig wenn auch bereits min. 1 option verwendet wird)
  if(prod_opt_count($options_id['po_id']) >=1){
    $val_count = prod_val_count($values_id['pov_id']);
  }else{$val_count=0;}
  // end Zählen der verwendung von values durch Produkte

    //$values_id['pov_id']

      $rows++;    
      $values_table .= '<tr class="'.(floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd').'">';
      $values_table .= '<td class="categories_view_data">'.$values_id['pov_id'].'</td>';            
      // Value bearbeiten
      if (($_GET['action'] == 'update_option_value') && ($_GET['value_id'] == $values_id['pov_id'])) {
        $values_table .= xtc_draw_form('values', FILENAME_PRODUCTS_ATTRIBUTES.'?&action=update_value&search='.$_GET['search'].'&option_page=' . $option_page, '', 'post')."\n";
        $values_table .= '<input type="hidden" name="value_id" value="'.$values_id['pov_id'].'">';
        $values_table .= '<td class="categories_view_data"  style="text-align: left;">';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $values_name = xtc_db_query("select products_options_values_name as pov_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $values_id['pov_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
          $values_name = xtc_db_fetch_array($values_name);
          $values_table .= $languages[$i]['code'] . ': <input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15" value="' . $values_name['pov_name'] . '"> <br />';
        }
        $values_table .='</td><td class="categories_view_data">';
        $values_table .= '';
        $values_table .= xtc_button(BUTTON_UPDATE);
        $values_table .= xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&search='.$_GET['search'].'&option_page='.$option_page));
        $values_table .= '</td>';
        $values_table .= '</form>';          
      }else{
      // Value normal ansicht
        $values_table .= '<td class="categories_view_data" style="text-align: left;">';
        for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
          $values_name = xtc_db_query("select products_options_values_name as pov_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $values_id['pov_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
          $values_name = xtc_db_fetch_array($values_name);
          $values_table .= $languages[$i]['code'] . ': ' . $values_name['pov_name'] . '<br />';
        }
        $values_table .= '</td>';
        $values_table .= '<td class="categories_view_data">';
        if ($_GET['action'] != 'update_option_value') {
          // löschen button nur wenn nicht bereits genutzt von produkt
          if($val_count == 0){
            $values_table .= xtc_button_link(xtc_image(DIR_WS_ICONS . 'cross.gif').BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_value&option_id='.$options_id['po_id'].'&value_id=' . $values_id['pov_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page).'" onClick="return confirm(\''.CONFIRM_DELETE.'\n\n'.CONFIRM_VAL_DELETE1.'\n'.CONFIRM_VAL_DELETE2.'\')');
          }else{
            $values_table .= xtc_button_link(xtc_image(DIR_WS_ICONS . 'icons/info.jpg').VALUE_COUNT.$val_count, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=show_products_used_val&value_id=' . $values_id['pov_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page, 'NONSSL'));            
          }
          $values_table .= xtc_button_link(xtc_image(DIR_WS_ICONS . 'icon_edit.gif').BUTTON_EDIT, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&option_id='.$options_id['po_id'].'&value_id=' . $values_id['pov_id'] . '&search='.$_GET['search'].'&option_page=' . $option_page));          
        }     
        $values_table .= '</td>';
     }
     $values_table .= '</tr>';
    }
  $values_table .= '</table>';
  }
  // neuen wert hinzufügen
  if ($_GET['action'] != 'update_option_value') {
      $next_vid = 1;
      $max_values_id_query = xtc_db_query("select max(products_options_values_id) + 1 as next_id from " . TABLE_PRODUCTS_OPTIONS_VALUES);
      $max_values_id_values = xtc_db_fetch_array($max_values_id_query);
      $next_vid = $max_values_id_values['next_id'];
  
      $new_value = xtc_draw_form('values', FILENAME_PRODUCTS_ATTRIBUTES.'?&action=add_product_option_values&search='.$_GET['search'].'&option_page=' . $option_page, '', 'post')."\n";
      $new_value .= INSERT_NEW_VALUE_TEXT.'<br />'."\n";
      $new_value .=  "\t".'<input type="hidden" name="option_id" value="' . $options_id['po_id'] . '">'."\n";
      $new_value .=  "\t".'<input type="hidden" name="value_id" value="' . $next_vid . '">'."\n";      
      $inputs = '';
      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
        $inputs .= "\t".$languages[$i]['code'] . ': <input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="20"> <br />'."\n";
      }
      $new_value .= $inputs;
      $new_value .= "\t".xtc_button(BUTTON_INSERT)."\n";
      $new_value .= '</form>'."\n";
      
      $values_table .= $new_value;
  }
  // end neuen wert hinzufügen
}  
// end value tabelle  
  $optiontable .= $values_table;                  
  // end Werte der jeweiligen Attribute
  $optiontable .= '</div>'."\n".'</div>'."\n";      
}
// Neue Option Hinzufügen
    if ($_GET['action'] != 'update_option' && $_GET['action'] != 'update_option_value') {
      $next_id = 1;
      $max_options_id_query = xtc_db_query("select max(products_options_id) + 1 as next_id from " . TABLE_PRODUCTS_OPTIONS);
      $max_options_id_values = xtc_db_fetch_array($max_options_id_query);
      $next_id = $max_options_id_values['next_id'];  
      
      $new_option = xtc_draw_form('options', FILENAME_PRODUCTS_ATTRIBUTES.'?&action=add_product_options&search='.$_GET['search'].'&option_page=' . $option_page, '', 'post')."\n";
      $new_option .= INSERT_NEW_OPTION_TEXT.'<br />'."\n";
      $new_option .=  "\t".'<input type="hidden" name="products_options_id" value="' . $next_id . '">'."\n";
      $inputs = '';
      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
        $inputs .= "\t".$languages[$i]['code'] . ': <input type="text" name="option_name[' . $languages[$i]['id'] . ']" size="20"> <br />'."\n";
      }
      $new_option .= $inputs;
      $new_option .= "\t".xtc_button(BUTTON_INSERT)."\n";
      $new_option .= '</form>'."\n";
    }
// end neue option hinzufügen  

/*############################ Ausgaben #################*/
?>
<script type="text/javascript" src="includes/javascript/attribut_slide.js"></script>
<?php
echo $message;
echo '<div class="dhtmlgoodies_schrift">'. $search_form .'</div>'; // Suchfeld (Option und Wert)
echo $navilinks; // Navigation << 1 2 ...>>
echo $optiontable; // Optionen inkl. Werte
echo $navilinks; // Navigation << 1 2 ...>>
echo '<br />';
echo '<div class="dhtmlgoodies_schrift">'. $new_option .'</div>'; // Neue Option hinzufügen
/*############################ENDE Ausgaben #################*/

// only debug follow
if($debug){
echo '<pre>';
print_r($array);
echo '</pre>';
}
?>

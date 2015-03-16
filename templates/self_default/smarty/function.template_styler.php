<?php

/*

 */
require_once (DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/inc/data.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');

function smarty_function_template_styler ($params, &$module_smarty) {
    global $current_category_id;

    // Daten übergeben an $data
    $data = $params['input'];
    $tpl =  $params['tpl'];

    // abhängig von der aufrufenden Datei, Dateinamen den Formularen zuweisen
    if (isset($tpl)){
      switch ($tpl) {
        case 'SEARCH':
          $tpl = FILENAME_ADVANCED_SEARCH_RESULT;
          break;
        case 'SPECIALS':
          $tpl = FILENAME_SPECIALS;
          break;
        case 'PRODUCTS_NEW':
          $tpl = FILENAME_PRODUCTS_NEW;
          break;
        default:
          $tpl = FILENAME_DEFAULT;
      }
    }

    // Produktpreis und Sonderangebotspreis splitten, damit Preis richtig sortiert wird
    foreach ($data as $k => $v){
        if (strpos($data[$k]['PRODUCTS_PRICE'],"productOldPrice")){
           preg_match_all('/<span.*?(<del\>.*?<\/del>)<\/span><br \/\>[a-zA-Z ]+(.*?)<br \/\><small.*?<\/small\>/',$data[$k]['PRODUCTS_PRICE'],$Ergebnis);
           $data[$k]['PRODUCTS_PRICE'] = $Ergebnis[2][0]; $data[$k]['PRODUCTS_OLDPRICE'] = "<br /><span class=\"productOldPrice\">".$Ergebnis[1][0]."</span>";
        }
    }

    // Sortierungsfunktion auswählen
    if (isset($_GET['sort'])){
      switch ((int)$_GET['sort']) {
        case 1:
          uasort($data, build_sorter_az(trim('PRODUCTS_NAME')));
          break;
        case 2:
          uasort($data, build_sorter_za(trim('PRODUCTS_NAME')));
          break;
        case 3:
          uasort($data, build_sorter_az('PRODUCTS_PRICE'));
          break;
        case 4:
          uasort($data, build_sorter_za('PRODUCTS_PRICE'));
          break;
      }
    }
  // Sortier-Dropdown an Smarty übergeben
  $sorting_dropdown = get_sorting_dropdown($current_category_id, $tpl);
  $module_smarty->assign('SORTING_DROPDOWN', $sorting_dropdown); 

  // Sessionvariable für Anzahl Artikel pro Seite setzen
  if (isset($_GET['artproseite'])) {
    $_SESSION['artproseite'] = intval($_GET['artproseite']);
   }
  if( $_SESSION['artproseite']==0 || $_SESSION['artproseite']=='') {
    $_SESSION['artproseite']=24; // hier wird der Standard definiert, wieviele Artikel auf einer Seite dargestellt werden sollen
  }
  // Sessionvariable für Ansichtenwahl Produktliste setzen auch in product_listing.php bei caching
  if (isset($_GET['ansicht'])) {
    $_SESSION['ansicht'] = intval($_GET['ansicht']);
   }
  if( $_SESSION['ansicht']==0 || $_SESSION['ansicht']=='') {
    $_SESSION['ansicht']=3; // hier wird der Standard der Ansichtenwahl definiert, 1 = einspaltig 3 = 3-spaltig 6 = 6-spaltig
  }
  // Sessionvariable für Ansichtenwahl Kategorieansicht setzen auch in default.php bei caching
  if (isset($_GET['ansicht_cat'])) {
    $_SESSION['ansicht_cat'] = intval($_GET['ansicht_cat']);
   }
  if( $_SESSION['ansicht_cat']==0 || $_SESSION['ansicht_cat']=='') {
    $_SESSION['ansicht_cat']=2; // hier wird der Standard der Ansichtenwahl definiert, 1 = einspaltig 2 = 2-spaltig 3 = 3-spaltig
  }
  // Pagination - Seitennavigation
  $navigation = '<div class="pagination pagination-right"><span class="pull-left"><small>'.get_display_count(count($data),(isset($_SESSION['artproseite']) ? (int)$_SESSION['artproseite'] : ''),(isset($_GET['page']) ? (int)$_GET['page'] : 1),TEXT_DISPLAY_NUMBER_OF_PRODUCTS).'</small></span>
                 <ul>'.get_display_links(count($data),(isset($_SESSION['artproseite']) ? (int)$_SESSION['artproseite'] : ''),MAX_DISPLAY_PAGE_LINKS,(isset($_GET['page']) ? (int)$_GET['page'] : 1), xtc_get_all_get_params(array ('page', 'info', 'x', 'y', 'keywords')).(isset($_GET['keywords'])?'&keywords='. urlencode($_GET['keywords']):'')).'</ul></div>';
  $module_smarty->assign('NAVIGATION', $navigation);                                                                

  // Wenn mehr Artikel als Anzahl pro Seite - dann $data splitten
  if (count($data) > $_SESSION['artproseite']){
  $data_chunked = array_chunk($data, (int)$_SESSION['artproseite'], true);
  $data = $data_chunked[(isset($_GET['page']) ? ((int)$_GET['page']-1) : 0)];
  }
  // Artikel pro Seite - Dropdown aufrufen und an Smarty übergeben
  $art_pro_seite_dropdown = get_art_pro_seite_dropdown($current_category_id, $tpl);
  $module_smarty->assign('ART_PRO_SEITE_DROPDOWN', $art_pro_seite_dropdown); 
                                             
  // Produktlistenansicht - Dropdown aufrufen und an Smarty übergeben
  $ansicht_buttons = get_ansicht_buttons($current_category_id, $tpl);
  $module_smarty->assign('ANSICHT_BUTTONS', $ansicht_buttons); 

  // Kategorieansicht - Dropdown aufrufen und an Smarty übergeben
  $ansicht_buttons_cat = get_ansicht_buttons_cat($current_category_id, $tpl);
  $module_smarty->assign('ANSICHT_BUTTONS_CAT', $ansicht_buttons_cat); 

  // Daten Styles auslesen
  $switcher = get_switcher_styles();
  $module_smarty->assign('switcher', $switcher); 

   // Übergabe sortierte Daten  
  $module_content = $data;
  $module_smarty->assign('module_content',$module_content);
}
?>

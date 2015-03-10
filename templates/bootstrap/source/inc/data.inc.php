<?php
/*
 * Smarty-plugIn in template path
 */
 // Styleswitcher Produktliste und Kategorieansicht
function get_switcher_styles() {
    if ($_SESSION['ansicht']==1) {$switcher1 = array('span' => 'span12',  'div1' => ' span2',         'div2' => 'span6',  'style' => '',        'name'=>'1000:"...":true',  'desc'=>'400:"...":true', 'style2' => '',           'div3' => 'span4',  'div3_1' => 'hidden', 'div4' => '', 'col' => '1');}
    if ($_SESSION['ansicht']==3) {$switcher1 = array('span' => 'span4',   'div1' => ' bottom-buffer', 'div2' => '',       'style' => 'style_3', 'name'=>'65:"...":true',    'desc'=>'90:"...":true',  'style2' => 'style_3_2',  'div3' => '',       'div3_1' => 'hidden', 'div4' => '', 'col' => '3');} 
    if ($_SESSION['ansicht']==6) {$switcher1 = array('span' => 'span2',   'div1' => ' bottom-buffer', 'div2' => '',       'style' => 'style_6', 'name'=>'45:"...":true',    'desc'=>'',               'style2' => 'style_3_2',  'div3' => '',       'div3_1' => 'hidden', 'div4' => '', 'col' => '6');}
    if ($_SESSION['ansicht_cat']==1) {$switcher2 = array('span_cat' => 'span12',  'style_cat' => '',        'name_cat'=>'1000:"...":true',  'desc_cat'=>'400:"...":true', 'style2_cat' => '',           'div5' => ' span4', 'div6' => 'span8',  'col_cat' => '1');}
    if ($_SESSION['ansicht_cat']==2) {$switcher2 = array('span_cat' => 'span6',   'style_cat' => 'style_3', 'name_cat'=>'65:"...":true',    'desc_cat'=>'90:"...":true',  'style2_cat' => 'style_3_2',  'div5' => '',       'div6' => '',       'col_cat' => '2');} 
    if ($_SESSION['ansicht_cat']==3) {$switcher2 = array('span_cat' => 'span4',   'style_cat' => 'style_6', 'name_cat'=>'45:"...":true',    'desc_cat'=>'',               'style2_cat' => 'style_3_2',  'div5' => '',       'div6' => '',       'col_cat' => '3');}
    $switcher = array_merge($switcher1,$switcher2);
  return $switcher;
}
 // Sortierfunktionen aufwärts / abwärts
function build_sorter_az($key) {
    return function ($a, $b) use ($key) {
        return strnatcasecmp(str_replace('.', '',$a[$key]), str_replace('.', '',$b[$key]));
    };
}
function build_sorter_za($key) {
    return function ($a, $b) use ($key) {
        return strnatcasecmp( str_replace('.', '',$b[$key]),str_replace('.', '',$a[$key]));
    };
}

// Hiddenfelder
function get_hidden_fields($current_category_id, $art='') {
    if (isset ($_GET['manufacturers_id']))
      $label .= xtc_draw_hidden_field('manufacturers_id', (int)$_GET['manufacturers_id']).PHP_EOL;
    if (isset ($_GET['filter_id']))
      $label .= xtc_draw_hidden_field('filter_id', (int)$_GET['filter_id']).PHP_EOL;
    if (isset ($_GET['keywords']))
      $label .= xtc_draw_hidden_field('keywords', $_GET['keywords']).PHP_EOL;
    if (isset($_GET['inc_subcat']))
     $label .= xtc_draw_hidden_field('inc_subcat', $_GET['inc_subcat']).PHP_EOL;
    if (isset($_GET['pfrom']))
      $label .= xtc_draw_hidden_field('pfrom', $_GET['pfrom']).PHP_EOL;
    if (isset($_GET['pto']))
      $label .= xtc_draw_hidden_field('pto', $_GET['pto']).PHP_EOL;
    if (isset($_GET['x']))
      $label .= xtc_draw_hidden_field('x', $_GET['x']).PHP_EOL;
    if (isset($_GET['y']))
      $label .= xtc_draw_hidden_field('y', $_GET['y']).PHP_EOL;
      $label .= xtc_draw_hidden_field('cat', $current_category_id).PHP_EOL;
    if (isset ($_GET['sort'])) 
      $label .= xtc_draw_hidden_field('sort', $_GET['sort']).PHP_EOL;
    if (isset ($_GET['ansicht'])) 
      $label .= xtc_draw_hidden_field('ansicht', $_GET['ansicht']).PHP_EOL;
    if (isset ($_GET['apros'])) 
      $label .= xtc_draw_hidden_field('artproseite', $_GET['artproseite']).PHP_EOL;
      $label .= xtc_hide_session_id() .PHP_EOL;
  return $label;
}
// Sortierdropdown
function get_sorting_dropdown($current_category_id, $tpl) {
    $sorting_dropdown = xtc_draw_form('sorting', DIR_WS_CATALOG . $tpl, 'get').PHP_EOL;
    $sorting_dropdown .= get_hidden_fields($current_category_id, 'sort');
      $options_sort = array(array('text' => SORTING_STANDARD));
      $options_sort[] = array('id' => '1', 'text' => SORTING_ABC_ASC);
      $options_sort[] = array('id' => '2', 'text' => SORTING_ABC_DESC);
      $options_sort[] = array('id' => '3', 'text' => SORTING_PRICE_ASC);
      $options_sort[] = array('id' => '4', 'text' => SORTING_PRICE_DESC);
      //$options_sort[] = array('id' => '5', 'text' => SORTING_DATE_DESC);
      //$options_sort[] = array('id' => '6', 'text' => SORTING_DATE_ASC);
      //$options_sort[] = array('id' => '7', 'text' => SORTING_ORDER_DESC);
      //$options_sort[] = array('id' => '8', 'text' => SORTING_MANUFACTURER_ASC);
      //$options_sort[] = array('id' => '9', 'text' => SORTING_MANUFACTURER_DESC);
    $sorting_dropdown.= xtc_draw_pull_down_menu('sort', $options_sort, $_GET['sort'], 'onchange="this.form.submit()" class="input-medium"').PHP_EOL;
    $sorting_dropdown .= '<noscript><button class="btn btn-mini" type="submit">'.SMALL_IMAGE_BUTTON_VIEW.'</button></noscript>'.PHP_EOL;
    $sorting_dropdown .= '</form>'."\n";
  return $sorting_dropdown;
}
// Ansichtenauswahl
function get_ansicht_buttons($current_category_id, $tpl) {
    $ansicht_buttons = xtc_draw_form('ansicht', DIR_WS_CATALOG . $tpl, 'get').PHP_EOL;
    $ansicht_buttons .= get_hidden_fields($current_category_id, 'ansicht');
    $ansicht_buttons .= '<div class="btn-group" data-toggle="buttons-radio">';
    $ansicht_buttons .= '<button class="btn disabled"><small style="line-height:14px">'.SELECT_VIEW.'</small></button>';
    if ($_SESSION['ansicht']==1) $a1 = ' active'; if ($_SESSION['ansicht']==3) $a3 = ' active'; if ($_SESSION['ansicht']==6) $a6 = ' active';
    $ansicht_buttons .= '<button class="btn'.$a1.'" onchange="this.form.submit()" name="ansicht" value="1" type="submit" title=" 1 "><i class="icon-stop"></i></button>';
    $ansicht_buttons .= '<button class="btn'.$a3.'" onchange="this.form.submit()" name="ansicht" value="3" type="submit" title=" 3 "><i class="icon-th-large"></i></button>';
    $ansicht_buttons .= '<button class="btn'.$a6.'" onchange="this.form.submit()" name="ansicht" value="6" type="submit" title=" 6 "><i class="icon-th"></i></button>';
    $ansicht_buttons .= '</div>';
    $ansicht_buttons .= '</form>';

  return $ansicht_buttons;
}
// Ansichtenauswahl Kategorieansicht
function get_ansicht_buttons_cat($current_category_id, $tpl) {
    $ansicht_buttons_cat = xtc_draw_form('ansicht_cat', DIR_WS_CATALOG . $tpl, 'get').PHP_EOL;
    $ansicht_buttons_cat .= get_hidden_fields($current_category_id, 'ansicht_cat');
    $ansicht_buttons_cat .= '<div class="btn-group" data-toggle="buttons-radio">';
    $ansicht_buttons_cat .= '<button class="btn disabled"><small style="line-height:14px">'.SELECT_VIEW.'</small></button>';
    if ($_SESSION['ansicht_cat']==1) $a1 = ' active'; if ($_SESSION['ansicht_cat']==2) $a2 = ' active'; if ($_SESSION['ansicht_cat']==3) $a3 = ' active';
    $ansicht_buttons_cat .= '<button class="btn'.$a1.'" onchange="this.form.submit()" name="ansicht_cat" value="1" type="submit" title=" 1 "><i class="icon-stop"></i></button>';
    $ansicht_buttons_cat .= '<button class="btn'.$a2.'" onchange="this.form.submit()" name="ansicht_cat" value="2" type="submit" title=" 2 "><i class="icon-th-large"></i></button>';
    $ansicht_buttons_cat .= '<button class="btn'.$a3.'" onchange="this.form.submit()" name="ansicht_cat" value="3" type="submit" title=" 3 "><i class="icon-th"></i></button>';
    $ansicht_buttons_cat .= '</div>';
    $ansicht_buttons_cat .= '</form>';

  return $ansicht_buttons_cat;
}
// Dropdown Artikel pro Seite
function get_art_pro_seite_dropdown($current_category_id, $tpl) {
    $art_pro_seite_dropdown = xtc_draw_form('apros', DIR_WS_CATALOG . $tpl, 'get').PHP_EOL;
    $art_pro_seite_dropdown .= get_hidden_fields($current_category_id, 'ansicht');
    $art_pro_seite_dropdown .= '<div class="input-prepend"><span class="add-on btn disabled"><small>'.APS_TEXT_STANDARD.'</small></span>';
      $options_aps[] = array('id' => '12', 'text' => 12);
      $options_aps[] = array('id' => '24', 'text' => 24);
      $options_aps[] = array('id' => '36', 'text' => 36);
      $options_aps[] = array('id' => '48', 'text' => 48);
    $art_pro_seite_dropdown.= xtc_draw_pull_down_menu('artproseite', $options_aps, isset($_SESSION['artproseite']) ? $_SESSION['artproseite'] : '', 'onchange="this.form.submit()" class="input-mini"').PHP_EOL;
    $art_pro_seite_dropdown .= '</div>'."\n";
    $art_pro_seite_dropdown .= '<noscript><input type="submit" value="'.SMALL_IMAGE_BUTTON_VIEW.'" /></noscript>'.PHP_EOL;
    $art_pro_seite_dropdown .= '</form>'."\n";
  return $art_pro_seite_dropdown;
}
// Navigation
    function get_display_links($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '', $page_name = 'page') {
      global $PHP_SELF, $request_type;

      $display_links_string = '';

      $class = 'class="pageResults"';

      $parameters = str_replace('&amp;', '&', $parameters);
      if (xtc_not_null($parameters) && (substr($parameters, -1) != '&')) {
        $parameters = ltrim($parameters,'&'); //remove left standing '&'
        $parameters .= '&'; //add '&' added to the right
      } 
      if ($max_rows_per_page > 0) {
      $number_of_pages = ceil($query_numrows / $max_rows_per_page);
      } else {
      $number_of_pages = 0;
      }

      // previous button - not displayed on first page
      if ($current_page_number == 1 && $number_of_pages > 1) $display_links_string .= '<li class="disabled"><span class="pageResults" title="' . PREVNEXT_TITLE_FIRST_PAGE . '">&iota;&laquo;</span></li>';
      if ($current_page_number == 1 && $number_of_pages > 1) $display_links_string .= '<li class="disabled"><span class="pageResults" title="' . PREVNEXT_TITLE_PREVIOUS_PAGE . '">&laquo;</span></li>';
      if ($current_page_number > 1) $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=1' . $request_type) . '" class="pageResults" title="' . PREVNEXT_TITLE_FIRST_PAGE . '">&iota;&laquo;</a></li>';
      if ($current_page_number > 1) $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=' . ($current_page_number - 1), $request_type) . '" class="pageResults" title="' . PREVNEXT_TITLE_PREVIOUS_PAGE . '">&laquo;</a></li>';

      // check if number_of_pages > $max_page_links
      $cur_window_num = (int)($current_page_number / $max_page_links);
      if ($current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = (int)($number_of_pages / $max_page_links);
      if ($number_of_pages % $max_page_links) $max_window_num++;

      // previous window of pages
      if ($cur_window_num > 1) $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" class="pageResults" title="' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . '">...</a></li>';

      // page nn button
      for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $number_of_pages); $jump_to_page++) {
        if ($jump_to_page == $current_page_number) {
          $display_links_string .= '<li class="active"><span title="' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . '">' . $jump_to_page . '</span></li>';
        } else {
          $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=' . $jump_to_page, $request_type) . '" class="pageResults" title="' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . '">' . $jump_to_page . '</a></li>';
        }
      }

      // next window of pages
      if ($cur_window_num < $max_window_num) $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" class="pageResults" title="' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . '">...</a></li>';

       // next button
      if (($current_page_number < $number_of_pages) && ($number_of_pages != 1)) $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=' . ($current_page_number + 1), $request_type) . '" class="pageResults" title="' . PREVNEXT_TITLE_NEXT_PAGE . '">&raquo;</a></li>';
      if (($current_page_number < $number_of_pages) && ($number_of_pages != 1)) $display_links_string .= '<li><a href="' . xtc_href_link(basename($PHP_SELF), $parameters . 'page=' . ($number_of_pages), $request_type) . '" class="pageResults" title="' . PREVNEXT_TITLE_LAST_PAGE . '">&raquo;&iota;</a></li>';
      if (($current_page_number == $number_of_pages) && ($number_of_pages != 1)) $display_links_string .= '<li class="disabled"><span class="pageResults" title="' . PREVNEXT_TITLE_NEXT_PAGE . '">&raquo;</span></li>';
      if (($current_page_number == $number_of_pages) && ($number_of_pages != 1)) $display_links_string .= '<li class="disabled"><span class="pageResults" title="' . PREVNEXT_TITLE_LAST_PAGE . '">&raquo;&iota;</span></li>';

      return $display_links_string;
    }

    function get_display_count($query_numrows, $max_rows_per_page, $current_page_number, $text_output) {
        $to_num = ($max_rows_per_page * $current_page_number);
        if ($to_num > $query_numrows) $to_num = $query_numrows;
        $from_num = ($max_rows_per_page * ($current_page_number - 1));
        if ($to_num == 0) {
            $from_num = 0;
        } else {
            $from_num++;
        }

        return sprintf($text_output, $from_num, $to_num, $query_numrows);
    }
?>

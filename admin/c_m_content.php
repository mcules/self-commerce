<?php
/* --------------------------------------------------------------
   $Id: c_m_content.php 2009-08-16 kunigunde $   

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2008 Self-Commerce

   Released under the GNU General Public License
   
   --------------------------------------------------------------*/
// zählen der installierten sprachen
  $count_languages = count($languages);

// 1. sprache setzen
$query_first_lang = "SELECT * FROM ".TABLE_LANGUAGES." WHERE languages_id = '".$_SESSION['languages_id']."'";
$sql_first_lang = xtc_db_query($query_first_lang);
$first_language = xtc_db_fetch_array($sql_first_lang);

// 2. sprache setzen 
if($count_languages==2){// wenn nur 2 vorhanden die andere wählen
  $query_sec_lang = "SELECT * FROM ".TABLE_LANGUAGES." WHERE languages_id <> '".$_SESSION['languages_id']."'";
}
if($count_languages>2){// wenn > 2 auswahl über formular
  if($_POST['second_languages_id'] =='' && $_SESSION['second_language_id'] == ''){// irgendeine sprache setzen
    $query_sec_lang = "SELECT * FROM ".TABLE_LANGUAGES." WHERE languages_id <> '".$_SESSION['languages_id']."'";
  }elseif($_SESSION['second_language_id'] != '' && $_POST['second_languages_id'] !=''){// formular sprache setzen
    $query_sec_lang = "SELECT * FROM ".TABLE_LANGUAGES." WHERE languages_id = '".$_POST['second_languages_id']."'";
  }else{// $_SESSION['second_language_id'] nutzen
    $query_sec_lang = "SELECT * FROM ".TABLE_LANGUAGES." WHERE languages_id = '".$_SESSION['second_language_id']."'";  
  }
}
$sql_sec_lang = xtc_db_query($query_sec_lang);
$second_language = xtc_db_fetch_array($sql_sec_lang);
$_SESSION['second_language_id'] = $second_language['languages_id'];
// end 2. sprache setzen

$sec_languages_select = '';
if($count_languages>2){// formular nur benötigt wenn > 2 sprachen
  $sec_languages_select .= TEXT_SELECT_LANG.' | ';
  $i=0;
  while($i<$count_languages){
    if($languages[$i]['id'] != $first_language['languages_id'] && $languages[$i]['id'] != $second_language['languages_id']){
      $sec_languages_select .= xtc_draw_form('lang_select_'.$i , FILENAME_CONTENT_MANAGER, '', 'post');
      $sec_languages_select .= xtc_draw_hidden_field('second_languages_id',$languages[$i]['id']);
      $sec_languages_select .= '<input type="image" src="'.DIR_WS_LANGUAGES.$languages[$i]['directory'].'/'.$languages[$i]['image'].'" alt="'.$languages[$i]['name'].'" title="'.$languages[$i]['name'].'" />';
      $sec_languages_select .= '</form> | ';
    }
  $i++;    
  }  
}
    
// first output
$first_output  = '';
$first_output .= '<img src="'.DIR_WS_LANGUAGES . $first_language['directory'].'/'.$first_language['image'].'" alt="'.$first_language['name'].'" /> '.$first_language['name'];
$first_output .= '<br />'.content_per_box($first_language['languages_id']);

// second output
$second_output  = '';
$second_output .= '<img src="'.DIR_WS_LANGUAGES . $second_language['directory'].'/'.$second_language['image'].'" alt="'.$second_language['name'].'" /> '.$second_language['name'];
$second_output .= '<br />'.content_per_box($second_language['languages_id']);

//######################################### Ausgaben #########################################
echo '<table width="100%">';
if($sec_languages_select != ''){// wird nur angezeigt wenn > 2 sprachen
  echo '<tr><td>&nbsp;</td><td>'.$sec_languages_select.'</td></tr>';
}
if($count_languages==1){// wenn nur 1 sprache keine 2 spalten benötigt
  echo '<tr><td>'.$first_output.'</td></tr>';
}else{
  echo '<tr>
          <td width="50%" valign="top">'.$first_output.'</td>
          <td width="50%" valign="top">'.$second_output.'</td>
        </tr>';
}
echo '</table><br />';
?>

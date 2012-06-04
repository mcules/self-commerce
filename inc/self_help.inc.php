<?php
// help function
/*
#
# <span title="header=[titeltext] body=[bodytext]" style="vertical-align:middle;font-family:arial;font-size:20px;font-weight:bold;color:#ABABAB;cursor:pointer">?</span>
# 
*/
function self_help($header, $body) {
  if (file_exists(DIR_FS_LANGUAGES . $_SESSION['language'] . '/help/help.php')){
    require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/help/help.php');
      $page = basename($_SERVER['PHP_SELF']);
      $develop = ''; // auf 1 setzen zum übersetzen fehlender texte
            
      if($develop != ''){
// ausgabe für übersetzer
        $help = '';
        // titel nicht vorhanden
        if( !isset($translation[$page.'_'.$header])){
          $help .= '<br />not found: $translation[\''.$page.'_'.$header.'\'] = \'\';';
        }
        //text nicht vorhanden
        if( !isset($translation[$page.'_'.$body])){
          $help .= '<br />not found: $translation[\''.$page.'_'.$body.'\'] = \'\';';
        }
        // titel vorhanden aber leer
        if( isset($translation[$page.'_'.$header]) && empty($translation[$page.'_'.$header])){
          $help .= '<br />empty: $translation[\''.$page.'_'.$header.'\'] = \'\';';
        }
        //text vorhanden aber leer
        if( isset($translation[$page.'_'.$body]) && empty($translation[$page.'_'.$body])){
          $help .= '<br />empty: $translation[\''.$page.'_'.$body.'\'] = \'\';';
        }
        if( isset($translation[$page.'_'.$header]) && !empty($translation[$page.'_'.$header]) && isset($translation[$page.'_'.$body]) && !empty($translation[$page.'_'.$body])){
          $help  = '<span title="header=['.$translation[$page.'_'.$header].'] body=['.$translation[$page.'_'.$body].']" style="cursor:pointer">'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').'</span>';        
        }        
// ende ausgabe übersetzer      
      }else{
// ausgabe für den user (Nur anzeigen was auch vorhanden ist)     
        if( isset($translation[$page.'_'.$header]) && !empty($translation[$page.'_'.$header]) && isset($translation[$page.'_'.$body]) && !empty($translation[$page.'_'.$body])){
          $help  = '<span title="header=['.$translation[$page.'_'.$header].'] body=['.$translation[$page.'_'.$body].']" style="cursor:pointer">'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').'</span>';        
        }else{
          $help = '';
        }
// ende ausgabe user      
      }
  }
return $help;
}
?>

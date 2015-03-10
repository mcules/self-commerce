<?php

/*
Buttons ins Bootstrapdesign umwandeln

Karl 06.02.2014
 */
  require_once ('templates/' . CURRENT_TEMPLATE . '/source/inc/button.inc.php');

  function smarty_modifier_button($Input) {
                    // Bild auslesen
                    preg_match("!src=\"([^\"]*)\"[^>]*>!",$Input,$src);
                    $image = basename($src[1]);
                    // Prüfen ob Input oder Image-Tag und Alt-Tag auslesen
                    if (strpos($Input,'input') == TRUE){
                      $submit=TRUE;
                      preg_match("!<input.*?alt=\"([^\"]*)\"[^>]*>!",$Input,$alt);
                    } else {
                      preg_match("!<img.*?alt=\"([^\"]*)\"[^>]*>!",$Input,$alt);
                    }
                    $alt = $alt[1];

        if ($buynow == 'ja') {$button = getBuyNow($pID, $p_name);}
       elseif ($submit==true) {$button = preg_replace("/<input type=\"image.*?alt=(.*?)>/i", get_bootstrap_button($image, $alt, '', $submit), $Input);}
      else {$button = preg_replace("/<img[^>]+\>/i", get_bootstrap_button($image, $alt), $Input);}
              
  return $button;
}
?>

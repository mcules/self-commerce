<?php

// Karl 08.02.2014
// Breadcrumbs anpassen an Bootstrap

function smarty_modifier_trail($Input) {

    preg_match_all('/<a.*?\>(.*?)<\/a>/',$Input,$Ergebnis); 
    $Links = $Ergebnis;
    array_pop($Links[0]);
    foreach ( $Links[0] as $wert) {
      $Output .= '<li>'.$wert.'<span class="divider"> &raquo; </span></li>';
    }
     $Output .= '<li class="active">'.end($Ergebnis[1]).'</li>';
	return $Output;

} ?>
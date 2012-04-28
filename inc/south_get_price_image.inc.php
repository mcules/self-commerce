  <?php 
  /* 
  Gibt einen Preis als Grafik aus 
  Copyright(C) 2006 Sergej Stroh. 
  
  www.southbridge.de 
  20.09.2006 
  
  modified by kunigunde (Maik Schmidt; www.self-commerce.de) 
  02.12.2007 
  */ 
  
  function south_get_price_image($price) 
  { 
  // Zahlen als Grafiken, bei Bedarf anpassen 

  $folder = 'templates/'.CURRENT_TEMPLATE.'/img/'; 
  $beg = '<img src="'.$folder; 
  $end = '">';    
  $images = array($beg.'0.gif'.$end,$beg.'1.gif'.$end,$beg.'2.gif'.$end,$beg.'3.gif'.$end,$beg.'4.gif'.$end,$beg.'5.gif'.$end,$beg.'6.gif'.$end,$beg.'7.gif'.$end,$beg.'8.gif'.$end,$beg.'9.gif'.$end); 
  $image_comma = $beg.'komma.gif'.$end; 
  
  $number = array(0,1,2,3,4,5,6,7,8,9); 
    
  if(is_numeric($price)){ 
  
    $dot = explode(".", $price); 
    if(!empty($dot[1])){ 
  
    $price_string = str_replace($number, $images, $dot[0]); 
    $price_string .= $image_comma; 
    $price_string .= str_replace($number, $images, $dot[1]); 
  
    }else{ 
  
      $price_string = str_replace($number, $images, $price); 
    } 
  } 
  
  return $price_string; 
  } 

?> 

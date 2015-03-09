<?php
function isUTF8($str) {
  if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
    return true;
  } else {
    return false;
  }
}

function auto_encode($str) {
  if (isUTF8($str)) {
    return $str;
  }
  return utf8_encode($str);
}

function auto_decode($str) {
  if ($_SESSION['language_charset'] == 'utf-8') {
    return $str;
  }
  return utf8_decode($str);
}

function auto_decode_recursive($array) {
  if ($_SESSION['language_charset'] == 'utf-8') {
    return $array;
  }
  
  foreach ($array as $key=>$value) {
    if (is_array($array[$key])) {
      $array[$key] = auto_decode_recursive($array[$key]);
    } else {
      $array[$key] = utf8_decode($array[$key]);
    }
  }
  return $array;
}

$_GET = auto_decode_recursive($_GET);
$_POST = auto_decode_recursive($_POST);
?>
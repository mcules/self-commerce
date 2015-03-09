<?php
# Some php versions don't have apache_response_headers implemented
# therefore we use PHP 5's headers_list, which unfortunately doesn't return
# an associative array
function apache_response_headers() {
  $headers = headers_list();
  $converted_headers = array();
  
  foreach ($headers as $value) {
    $pos_colon = strpos($value, ":");
    $key = substr($value, 0, $pos_colon);
    $converted_headers[$key] = trim(substr($value, $pos_colon + 1));
  }
  
  return $converted_headers;
}
?>
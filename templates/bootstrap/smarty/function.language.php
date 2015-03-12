<?php

/*
Sprachdatei einlesen für Rechnungsmails und -druck
 */
    define('DIR_FS_LANGXTRA',DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/langxtra/');
    define('LANGXTRA',DIR_FS_LANGXTRA  . $_SESSION['language'] . '.php');
    if(file_exists(LANGXTRA)) require(LANGXTRA);
  
  function smarty_function_language ($params) {

}
?>

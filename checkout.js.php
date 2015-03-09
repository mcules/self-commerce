<?php
/**
 * Concats all needed javascript files and serves them with strict http caching headers
 * for performance reasons
 */
$ASSETS = array(
  'lib/jquery/jquery-1.6.1.min.js',
  'lib/nyroModal/js/jquery.nyroModal-1.6.2.min.js',
  'lib/jquery.form/jquery.form-2.47.min.js',
  'templates/' . $_GET['tpl'] . '/javascript/ajax_checkout.js'
);

$max_age = 60 * 60 * 24 * 7;
header('Content-Type: text/javascript');
header('Cache-Control: public, max-age=' . $max_age);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');

ob_start('ob_gzhandler');
foreach ($ASSETS as $asset) {
  echo file_get_contents($asset) . "\n";
}
?>
<?php
/* define Shop-System */
if ( (stripos(PROJECT_VERSION, 'xtcModified')) !== false) { 
	define('TPL_CLIENT', 'xtcmod');
} else {
	define('TPL_CLIENT', 'xtc304');
}

/* define boxless checkout */
/* show boxless => $isBoxlesActiv = 'true' , show checkout with boxes => $isBoxlesActiv = 'false' or leave empty */
$isBoxlesActiv = 'true';
$showBoxless = array ('checkout_');/* add more sites to display sites boxless, e.g.: $showBoxless = array ('checkout_', 'login.php'); to add the login.php  */
foreach ($showBoxless as $displayedSite) {
    if ( (stristr ( $_SERVER['PHP_SELF'] ,  $displayedSite )) && ($isBoxlesActiv == 'true') ) {
        define("NO_BOXES", 'true' );
        break;
    }
}
if ( NO_BOXES != 'true'){
	define("NO_BOXES", 'false' );
}

define('MULTI_COL_LISTING_DIR', CURRENT_TEMPLATE."/module/multicol_listings");

//print_r(get_defined_constants(true));
/*********************************************************
* Einfügen der Conditional Comments zur anpassung des IE *  
* ********************************************************
* 3-columns fix / 3-Spalten fix - default:  patch_3col_fixed.css 
* 3-columns flexible / 3-Spalten flexibel:  patch_3col_flex.css
* 2-columns fix / 2-Spalten fix:            patch_2col_fixed_13.css
* 2-columns flexible / 2-Spalten flexibel : patch_2col_flex_13.css
*/
?>
<!--[if lte IE 7]>
<link href="<?php echo 'templates/'.CURRENT_TEMPLATE.'/'; ?>css/patches/patch_3col_fixed.css" rel="stylesheet" type="text/css" />
<![endif]-->
<script type="text/javascript" src="<?php echo 'templates/'.CURRENT_TEMPLATE.'/'; ?>javascript/jquery-1.4.4.min.js"></script>
<?php
/*************************************************************
* include javascript and jQuery Plugins in plugins directory *
*************************************************************/
$pluginDefineArray['css'] = array ('path' => '/css/plugins/');
$pluginDefineArray['js'] = array ('path' => '/javascript/plugins/');

echo inputPluginFiles($pluginDefineArray);

function inputPluginFiles($pluginDefineArray) {
  $incOutput = '';
  foreach ($pluginDefineArray as $currentPlugin) {
  	$pluginUrlPath = 'templates/'. CURRENT_TEMPLATE . $currentPlugin['path'] ;
  	$pluginSrvPath = DIR_FS_CATALOG . 'templates/'. CURRENT_TEMPLATE . $currentPlugin['path'];
    if ($dir = opendir($pluginSrvPath)) {
      while (($file = readdir($dir)) !== false) {
  		  $fileNamerev = strrev($file);
        $parts = explode(".",$fileNamerev);
        $endung = strrev($parts[0]);
  		  if (is_file($pluginSrvPath.$file) and ($endung == "js" ) and (substr($file, 0, 1) !=".")) {
  			   $incOutput .= '<script type="text/javascript" src="'.$pluginUrlPath.$file.'"></script>'."\n";
  		  } elseif (is_file($pluginSrvPath.$file) and ($endung == "css" ) and (substr($file, 0, 1) !=".")) {
			     $incOutput .= '<link rel="stylesheet" type="text/css" href="'.$pluginUrlPath.$file.'" />'."\n";
		    } //if
      } // while
  	  closedir($dir);
  	}
  }
  return $incOutput;
}

?>

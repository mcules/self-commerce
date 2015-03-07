<?php
/* define Shop-System */
if ( (stripos(PROJECT_VERSION, 'Self-Commerce')) !== false) {
	define('TPL_CLIENT', 'xtcmod');
}
else {
	define('TPL_CLIENT', 'xtc304');
}

/* define boxless checkout */
/* show boxless => $isBoxlesActiv = 'true' , show checkout with boxes => $isBoxlesActiv = 'false' or leave empty */
$isBoxlesActiv = 'true';
$showBoxless = array ('checkout_');/* add more sites to display sites boxless, e.g.: $showBoxless = array ('checkout_', 'login.php'); to add the login.php  */
foreach ($showBoxless as $displayedSite) {
	if ( (stristr( $_SERVER['PHP_SELF'] ,  $displayedSite )) && ($isBoxlesActiv == 'true') ) {
		define("NO_BOXES", 'true' );
		break;
	}
}
if ( NO_BOXES != 'true') {
	define("NO_BOXES", 'false' );
}

define('MULTI_COL_LISTING_DIR', CURRENT_TEMPLATE."/module/multicol_listings");
?>

<!--[if lt IE 9]>
	<script type="text/javascript" src="template/js/html5.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="template/css/ie.css">
<![endif]-->
<!--[if lt IE 7]>
	<div style=' clear: both; text-align:center; position: relative;'>
		<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
			<img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
		</a>
	</div>
<![endif]-->

<script type="text/javascript" src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/html5.js"></script>
<!--
<script type="text/javascript" src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/jquery-1.7.min.js" ></script>
-->
<!-- <script type="text/javascript" src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/FF-cash.js"></script> -->
<script type="text/javascript" src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/superfish.js"></script>
<script type="text/javascript" src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/easyTooltip.js"></script>

<?php
/*************************************************************
* include javascript and jQuery Plugins in plugins directory *
*************************************************************/
$pluginDefineArray['js'] = array ('path' => '/javascript/plugins/');
echo inputJsPluginFiles($pluginDefineArray);

function inputJsPluginFiles($pluginDefineArray) {
	$incOutput = '';
	foreach ($pluginDefineArray as $currentPlugin) {
		$pluginUrlPath = 'templates/'. CURRENT_TEMPLATE . $currentPlugin['path'] ;
		$pluginSrvPath = DIR_FS_CATALOG . 'templates/'. CURRENT_TEMPLATE . $currentPlugin['path'];
		if ($dir = opendir($pluginSrvPath)) {
			while (($file = readdir($dir)) !== false) {
				$fileNamerev = strrev($file);
				$parts = explode(".", $fileNamerev);
				$endung = strrev($parts[0]);
				if (is_file($pluginSrvPath.$file) and ($endung == "js" ) and (substr($file, 0, 1) !=".")) {
					$incOutput .= '<script type="text/javascript" src="'.$pluginUrlPath.$file.'"></script>'."\n";
				}
			}
			closedir($dir);
		}
	}
	return $incOutput;
}
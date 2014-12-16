<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/reset.css" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/style.css" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/css/grid.css" type="text/css" media="screen">
<link href='http://fonts.googleapis.com/css?family=Michroma' rel='stylesheet' type='text/css'>

<?php
/*************************************************************
* include css Plugins in plugins directory *
*************************************************************/
$pluginDefineArray['css'] = array ('path' => '/css/plugins/');
echo inputCssPluginFiles($pluginDefineArray);

function inputCssPluginFiles($pluginDefineArray) {
	$incOutput = '';
	foreach ($pluginDefineArray as $currentPlugin) {
		$pluginUrlPath = 'templates/'. CURRENT_TEMPLATE . $currentPlugin['path'] ;
		$pluginSrvPath = DIR_FS_CATALOG . 'templates/'. CURRENT_TEMPLATE . $currentPlugin['path'];
		if ($dir = opendir($pluginSrvPath)) {
			while (($file = readdir($dir)) !== false) {
				$fileNamerev = strrev($file);
				$parts = explode(".", $fileNamerev);
				$endung = strrev($parts[0]);
				if (is_file($pluginSrvPath.$file) and ($endung == "css" ) and (substr($file, 0, 1) !=".")) {
					$incOutput .= '<link rel="stylesheet" href="'.$pluginUrlPath.$file.'" />'."\n";
				}
			}
			closedir($dir);
		}
	}
	return $incOutput;
}
?>
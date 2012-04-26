<?php
/* example
$settings["unique_name"]["dir"]		= "./FileUpload/name"; //relative or absolute path for uploading, with the trailing slash .. this has to be seen from the web
$settings["unique_name"]["url_dir"] 	= "FileUpload/name/"; //how the web sees the path above, relative to your web site's address, with the trailing slash .. this could also be an absolute path .. 
$settings["unique_name"]["ext"]		= array("jpg"); //all allowed extensions .. this is only used in viewing, not uploading (eg. you can upload a png, but you won't be able to see it then)
$settings["unique_name"]["type"]	= 1; //this is the index used in $types arrays .. by default, 1 is file browser, 2 is image browser
*/

$settings["files"]["dir"] 	= "../../../../../../../tiny_upload/files/"; //with the trailing slash!
$settings["files"]["url_dir"] 	= "tiny_upload/files/"; //with the trailing slash!
$settings["files"]["ext"] 	= array("*");
$settings["files"]["type"] 	= 1; //1 => file browser, 2 => image browser
$settings["img"]["dir"] 	= "../../../../../../../tiny_upload/pics/"; //with the trailing slash!
$settings["img"]["url_dir"] 	= "tiny_upload/pics/"; //with the trailing slash!
$settings["img"]["ext"] 	= array("jpg", "bmp", "png", "gif");
$settings["img"]["type"] 	= 2; //1 => file browser, 2 => image browser
$settings["media"]["dir"] 	= "../../../../../../../tiny_upload/files/"; //with the trailing slash!
$settings["media"]["url_dir"] 	= "tiny_upload/files/"; //with the trailing slash!
$settings["media"]["ext"] 	= array("*");
$settings["media"]["type"] 	= 1; //1 => file browser, 2 => image browser

//what file to include for reading and displaying of directories and files
$types[1] = "file_manager/file_browser_include.php";
$types[2] = "file_manager/image_browser_include.php";

//which language file to use
include("file_manager/lang/lang_eng.php");

//which images to use
$delete_image 			= "file_manager/x.png";
$folder_small_image 	= "file_manager/folder_small.png";
$file_small_image 		= "file_manager/file_small.png";
$folder_large_image 	= "file_manager/folder_large.png";

//custom configuration from here on ..
//image browser configuration
$dir_width 		= "96px";
$file_width 	= "96px";
$pics_per_row 	= 2;
?>

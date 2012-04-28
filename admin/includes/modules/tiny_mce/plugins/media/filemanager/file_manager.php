<?php
//TODO it woudn't be bad to check for admin rights here ;)


require("file_manager_config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $strings["title"]; ?></title>

<link href="file_manager/styles.css" rel="stylesheet" type="text/css">
<?php
require("file_manager/utils.php");
if (isSet($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
}
else {
	$type = -2;
}
$default_dir = ".";
$ext = array("*");
$url_dir = "";
if ($type != -2) {
	if (isSet($settings[$type])) {
		$default_dir = $settings[$type]["dir"];
		$url_dir = $settings[$type]["url_dir"];
		$ext = $settings[$type]["ext"];
	}
}

if (!isSet($_REQUEST["dir"]) || strlen($_REQUEST["dir"]) == 0) {
	$dir = $default_dir;
	$requested_dir = "";
}
else {
	$requested_dir = $_REQUEST["dir"] . "/";
	$dir = $default_dir . "/" . $requested_dir;
}
if (strpos($dir, "..") > 0) //'..' in our path is a big no-no
	$dir = $default_dir;

if (isSet($_REQUEST["action"]))
{
	if ($_REQUEST["action"] == "upload_file")
	{
		$filename = $default_dir . $requested_dir . basename($_FILES["uploaded_file"]["name"]);
		if (file_exists($filename))
			$filename = $default_dir . "/" . $requested_dir . rand() . "_" . basename($_FILES["uploaded_file"]["name"]);
		if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $filename))
			echo "<!-- file upload successful -->\n";
	}
	else if ($_REQUEST["action"] == "create_dir")
	{
		if (@mkdir($default_dir . "/" . $requested_dir . $_REQUEST["dir_name"]) === FALSE)
			echo "<!-- creation of directory failed! -->\n";
		else
			echo "<!-- directory created successfully -->\n";
	}
	else if ($_REQUEST["action"] == "delete_folder" || $_REQUEST["action"] == "delete_file")
	{
		@rmdirr($_REQUEST["item_name"]);
	}
}
?>
<script>
  function kopieren(inhalt)
  {
    zwischenspeicher.innerText = inhalt.innerText;
    ablage = zwischenspeicher.createTextRange();
    ablage.execCommand("Copy");
  }

function fileSelected(filename) {
	//let our opener know what we want
	window.top.opener.my_win.document.getElementById(window.top.opener.my_field).value = "<?php echo $url_dir; ?>" + filename;
	window.top.opener.my_win.document.getElementById(window.top.opener.my_field).onchange();
	//we close ourself, cause we don't need us anymore ;)
	window.close();
}
function switchDivs() {
	document.getElementById("upload_div").style.display = "none";
	document.getElementById("uploading_div").style.display = "block";
	return true;
}
</script>
</head>
<body>
<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>

</tr>
<tr>
<td align="left" class="td_back">
<?php
if (strlen($requested_dir) > 0) {
	$last_pos = strrpos(substr($requested_dir, 0, strlen($requested_dir)-1), "/");
	$prev_dir = "";
	if ($last_pos !== FALSE && $last_pos > 0)
		$prev_dir = substr($requested_dir, 0, $last_pos);
	?>
	<a class="back" href="?type=<?php echo $type; ?>&dir=<?php echo $prev_dir; ?>"><< <?php echo $strings["back"]; ?></a>
	<?php
}
?>
</td>
<td align="right" class="td_close"><a class="close" href="javascript: window.close();"><?php echo $strings["close"]; ?></a></td>
</tr>
<tr>
<td colspan="2" id="td_main" class="td_main" height="100%" valign="top">
<?php
$dh  = opendir($dir);
while (false !== ($filename = readdir($dh))) {
	if ($filename != "." && $filename != "..") {
		if (is_dir($dir . "/" . $filename)) {
			$dirs[] = $filename;
		}
		else {
			if (sizeof($ext) > 0) {
				for ($i=0;$i<sizeof($ext);$i++) {
					if ($ext[$i] == "*" || (strtolower($ext[$i]) == strtolower(substr($filename, -strlen($ext[$i]))))) {
						$files[] = $filename;
						break;
					}
				}
			}
			else {
				$files[] = $filename;
			}
		}
	}
}

?>
<?php include($types[$settings[$type]["type"]]); ?>
</td>
</tr>
<tr>

<td valign="top" colspan="2">
	<div id="upload_div" style="display: block;">
	<form method="post" enctype="multipart/form-data" onSubmit="switchDivs();">
		<?php echo $strings["upload_file"]; ?>
		<input type="hidden" name="action" value="upload_file">
		<input type="hidden" name="MAX_FILE_SIZE" value="104857600" /> <!-- ~100mb -->
		<input type="file" name="uploaded_file">
		<input type="submit" value="<?php echo $strings["upload_file_submit"]; ?>">
	</form>
	</div>
	<div id="uploading_div" style="display: none;">
	<?php echo $strings["sending"]; ?>
	</div>
</td>
</tr>
</table>
<script>
function delete_folder(dir_name)
{
	document.getElementById("hidden_action").value = "delete_folder";
	document.getElementById("hidden_item_name").value = "<?php echo $dir . "/"; ?>" + dir_name;
	document.getElementById("hidden_form").submit();
}
function delete_file(file_name)
{
	document.getElementById("hidden_action").value = "delete_file";
	document.getElementById("hidden_item_name").value = "<?php echo $dir . "/"; ?>" + file_name;
	document.getElementById("hidden_form").submit();
}
</script>
<div style="display: none;">
	<form method="post" id="hidden_form">
	<input type="hidden" name="action" id="hidden_action" value="">
	<input type="hidden" name="item_name" id="hidden_item_name" value="">
	</form>
</div>
</body>
</html>

<table width="100%">
<?php
$count = 0;
for ($i=0;$i<sizeof($dirs);$i++) {
	$count++;
	if ($count == 1) echo "<tr>\n";
	echo "<td align='center'><a class='dir' href='?type=" . $type . "&dir=" . $requested_dir . $dirs[$i] . "'><img src='" . $folder_large_image . "' width='" . $dir_width . "' border='0px'></a><br>" . $dirs[$i] . "<br /><input type='button' value='Ordner l&ouml;schen'  onClick='delete_folder(\"" . $dirs[$i] . "\")'></td>\n";
	if ($count == $pics_per_row || $i == (sizeof($dirs)-1)) {
		$count = 0;
		echo "</tr>\n";
	}
}
$count = 0;
for ($i=0;$i<sizeof($files);$i++) {
	$count++;
	$id++;
	if ($count == 1) echo "<tr>\n";
	echo "<td align='center'><a class='file' href='#' onClick='fileSelected(\"" . $requested_dir . $files[$i] . "\");'><img src='" . $dir . "" . $files[$i] . "' width='" . $file_width . "' border='0px'></a><br><span id='pfad_".$id."'>" .$url_dir. $files[$i] . "</span><br /><input type='button' value='Pfad kopieren (nur IE)' onClick='kopieren(pfad_".$id.");'><br /><input type='button' value='Datei l&ouml;schen' onClick='delete_file(\"" . $files[$i] . "\")'></td>\n";
	if ($count == $pics_per_row || $i == (sizeof($files)-1)) {
		$count = 0;
		echo "</tr>\n";
	}
}
?>
</table>
<textarea id="zwischenspeicher" style="display:none;"></textarea>

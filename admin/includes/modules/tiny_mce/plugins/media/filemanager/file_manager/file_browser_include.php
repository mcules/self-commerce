<?php
echo '<table>';
for ($i=0;$i<sizeof($dirs);$i++) {
  $id++;
	echo "<tr><td><img src='" . $folder_small_image . "'></td><td> <a class='dir' href='?type=" . $type . "&dir=" . $requested_dir . $dirs[$i] . "'>" . $dirs[$i] . "</a></td><td> <input type='button' value='Ordner l&ouml;schen'  onClick='delete_folder(\"" . $dirs[$i] . "\")'></td></tr>\n";
}
for ($i=0;$i<sizeof($files);$i++) {
  $id++;
	echo "<tr><td><img src='" . $file_small_image . "'></td><td> <a class='file' href='#' onClick='fileSelected(\"" . $requested_dir . $files[$i] . "\");'><span id='pfad_".$id."'>" .$url_dir. $files[$i] . "</span></a></td><td> <input type='button' value='Pfad kopieren (nur IE)' onClick='kopieren(pfad_".$id.");'></td><td> <input type='button' value='Datei l&ouml;schen' onClick='delete_file(\"" . $files[$i] . "\")'></td></tr>\n";
}
echo '</table><textarea id="zwischenspeicher" style="display:none;"></textarea>';
?>

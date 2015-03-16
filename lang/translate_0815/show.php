<?php
$Source = $_POST['source_lang'];
$Target = $_POST['target_lang'];

function read_recursiv($path) {
	$result = array();
	$handle = opendir('../'.$path);
	if ($handle) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				$name = $path . "/" . $file;
				if (is_dir($name)) {
					$ar = read_recursiv($name);
					foreach ($ar as $value) {
						$result[] = $value;
					}
				}
				else {
					$result[] = $name;
				}
			}
		}
	}
	closedir($handle);
	return $result;
}
//#########################################################
if($_POST['modus']) {// Datei speichern
	$max = 0;
	$keys = array_keys($_POST['key']);
	rsort($keys);
	$max = $keys[0];
	$keys = array_keys($_POST['comment']);
	rsort($keys);
	if($keys[0] > $max) { $max = $keys[0]; }
	$content = array();
	for($i = 0; $i < $max; $i++) {
		if(array_key_exists($i, $_POST['key'])) { $content[] = 'define(\'' . $_POST['key'][$i] . '\', \'' . $_POST['val'][$i] . '\');'; }
		else { $content[] = $_POST['comment'][$i]; }
	}
	if($_POST['modus'] == 'check') {//bestehende Datei bearbeiten
		$new_file = $_POST['file'];
		$fp = fopen($new_file, 'w');
		fwrite($fp, implode("\n", $content));
		fclose($fp);
		echo $_POST['file'].' wurde gespeichert';
	}
	//############################
	if($_POST['modus'] == 'translate') {//neue Datei erstellen
		$new_file = str_replace($Source, $Target , $_POST['file']);
		$verzeichnise = explode('/', $new_file);
		$k = count($verzeichnise)-1;
		$new_pfad = '';
		for($l = 0; $l < $k; $l++) {
			$new_pfad .= $verzeichnise[$l].'/';
			if (!file_exists($new_pfad)) { mkdir(substr($new_pfad, 0, -1), 0777); }
		}
		$fp = fopen($new_pfad.$verzeichnise[$l], 'w');
		fwrite($fp, implode("\n", $content));
		fclose($fp);
		echo $new_pfad.$verzeichnise[$l].' wurde erstellt';
	}
}
//#########################################################

echo '<a href="index.php">Back to selection</a><br />
<h3>Now choosen</h3>
Source: ' . $Source . '<br />
Target: ' . $Target . '<br /><br />';

$SourceFolder = read_recursiv($Source);
$TargetFolder = read_recursiv($Target );
$fertig = 0;

echo '<table border="1">';
foreach($SourceFolder as $Original) {
	echo '<tr><td>';
	$pfad = '';
	$org = explode('/', $Original);
	$i = count($org);
	for($j = 0; $j < $i; $j++) {
		//hauptordner ausblenden
		if($j!=0) {
			$pfad .= str_replace($Source, $Target , $org[$j]);
			echo str_replace($Source, $Target , $org[$j]);
		}
		// nach jedem verzeichnis ein slash
		if($j<$i-1 && $j!=0) {
			$pfad .= '/';
			echo '/';
		}
	}
	$vergleich = array_search($Target .'/'.$pfad, $TargetFolder);
	// bereits vorhandene übersetzung
	if($vergleich !== false) {
		$fertig++;// hier formular zur kontrolle der übersetzung
		echo '</td><td BGCOLOR="green">
            <form method="post" action="translate.php">
            <input type="hidden" name="file" value="' . $TargetFolder[$vergleich] . '" />
            <input type="hidden" name="mode" value="check" />
            <input type="hidden" name="language" value="'.$_POST['target_lang'].'" />
            <input type="submit" value="check" />
            </form>
          </td>';
	}
	//übersetzung fehlt formular zum übersetzen
	else {
		echo '</td><td BGCOLOR="red" >
            <form method="post" action="translate.php">
            <input type="hidden" name="file" value="' . $Original . '" />
            <input type="hidden" name="mode" value="translate" />
            <input type="hidden" name="language" value="'.$_POST['target_lang'].'" />
            <input type="submit" value="translate" />
            </form>
          </td>';
	}
	echo '</tr>';
}
echo '</table>';
echo '<br /><b>' . count($SourceFolder) . ' Dateien gefunden</b><br />';
$prozent = $fertig / count($SourceFolder) * 100;
echo '<b>' . $fertig . ' Dateien bzw. '.round($prozent,2).'% bereits übersetzt</b>';
if($prozent < '100') {
	//echo '<br /><h2>sobald 100% übersetzt sind steht hier das zip Archiv zur Verfügung</h2>';
}
//#########################################################
//if($prozent == '100'){
include("pclzip.lib.php");
$verzeichniss_array=array($Target );
$mein_zip_file = 'zipfiles/'.date('j_m_y_G_i_s').'_'.$Target .'.zip';
$archive = new PclZip($mein_zip_file);
$archive->create($verzeichniss_array);
echo '<br /><font color="red"><b>Achtung es werden nur bereits übersetzte Dateien gepackt!</b></font><br /><h2>herunterladen: <a href="'.$mein_zip_file.'">'.$mein_zip_file.'</a></h2>';
//}
?>
<?php
function showLanguage($file) {
  $file = stripslashes($file);
  $f=fopen($file,"r");
  $content = fread($f, filesize($file));
  $content = htmlspecialchars($content);
    $regex = '#^define\(\'(.*)\'\s*,\s*\'(.*)\'\);#';
    $arr = file($file);
    $i = 0;
    if($arr !== false) { 
        foreach($arr as $line) { 
            $line = trim($line); 
            if(preg_match($regex, $line, $matches)) {  
	            $key = $matches[1]; 
	            $val = $matches[2]; 
				print '<tr>';
	            print '<td width="20%">' . htmlentities($key) . ' <input type="hidden" name="key[' . $i . ']" value="' . htmlentities($key) . '" /></td>'; 
	            print '<td><input size="100" type="text" name="val[' . $i . ']" value="' . htmlentities($val) . '" /></td>'; 
	            print '</tr>';
			}else{
			   	echo '<tr><td colspan="2">
           <input type="hidden" name="comment[' . $i . ']" value="' . $line . '" />
           <font color="red">'.$line.'</font></td></tr>';
			}
			$i++; 
        } 
    } 

}

if(!$_POST['modus']){
$file = $_POST['file'];

echo'<form method="post" action="show.php"><table>';
showLanguage($file);
echo '</table>
<input type="hidden" name="modus" value="'.$_POST['mode'].'" />
<input type="hidden" name="file" value="'.$_POST['file'].'" />
<input type="hidden" name="language" value="'.$_POST['language'].'" />
<input type="submit" value="speichern" />
<input type="reset" value="Abbrechen" onClick="history.back()" />
</form>';
}

//##############################################################

if($_POST['modus'] == 'check'){
    //  Maximum ermitteln 
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

	$new_file = $_POST['file'];
    $fp = fopen($new_file, 'w'); 
    fwrite($fp, implode("\n", $content)); 
    fclose($fp);
	echo $_POST['file'].' wurde gespeichert';    
}
?>
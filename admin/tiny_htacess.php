<?php
/* --------------------------------------------------------------
   $Id$
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
require ('includes/application_top_1.php');
?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
<?php echo xtc_image(DIR_WS_ICONS.'heading_configuration.gif'); ?>
                    </td>
                    <td class="pageHeading">
.htaccess
                    </td>
                  </tr>
                </table></td>
            </tr>
          </table>
<!-- content -->
<?php
extract($_REQUEST);

// Header
$cfg['header'] = "
<table border='0' width='95%' cellspacing='0'>
<tr><td width='25%' valign='top' >
<b>Links</b><br>
&nbsp;<a href='?action=uebersicht'>Übersicht</a><br>
&nbsp;<a href='?action=neuerBereich2'>Schutz erstellen</a><br>
&nbsp;<a href='?action=editBereich'>Bereich editieren</a><br>
&nbsp;<a href='?action=delBereich'>Bereich löschen</a><br>
</td>
<td valign='top' width='75%' >
";

// Footer
$cfg['footer'] = "
</td></tr>
</table>
";

$cfg['ad'] = "";

echo $cfg['header'];
$bereichName = 'safed';
$bereichName = str_replace("\n","",$bereichName);
$benutzerName = str_replace("\n","",$benutzerName);
$benutzerName = str_replace(":","",$benutzerName);
$benutzerPasswort = str_replace("\n","",$benutzerPasswort);

$ordner1 = DIR_FS_ADMIN.'includes/modules/tiny_mce/plugins/media/filemanager/';
$ordner = DIR_WS_MODULES.'tiny_mce/plugins/media/filemanager/';
$htaccess = $ordner.'.htaccess';
$htpasswd = $ordner.'.htpasswd';

// meldungen
$fileexist = 'Es existiert eine .htaccess-Datei. '.$htaccess.' Dieser Bereich sollte also passwortgeschützt sein. Sie können diesen editieren.';
$filenoexist = 'Es existiert keine .htaccess-Datei. Daher kann der passwortgeschützte Bereich auch nicht editiert werden.';
$filerights = '<br/>Die .htaccess-Datei kann nicht geschrieben werden. Ändern Sie die Rechte des Verzeichnisses: admin/'.$ordner.' auf 0777.';
$filerights1 = '<br/>Die .htpasswd-Datei kann nicht geschrieben werden. Ändern Sie die Rechte des Verzeichnisses: admin/'.$ordner.' auf 0777.';

if (!$action || $action == "uebersicht") {
	echo "<b>Passwortgeschützter Bereich</b><br><br>";
	if (file_exists($htaccess)) {
		echo $fileexist;
	}
	else
	{
		echo "Es existiert noch keine .htaccess-Datei. Sie können einen neuen passwortgeschützten Bereich anlegen.";
	}
}

if ($action == "neuerBereich3") {
	if (file_exists($htaccess)) {
		echo $fileexist;
	}
	else
	{
		$fehler = "";
    if (strlen($benutzerName) < 3 || strlen($benutzerName) > 20) {
			$fehler .= "Der Benutzername muss zwischen 3 und 20 Zeichen lang sein.";
			$action = "neuerBereich2";
		}
		elseif (strlen($benutzerPasswort) < 5 || strlen($benutzerPasswort) > 20) {
			$fehler .= "Das Passwort muss zwischen 5 und 20 Zeichen lang sein.";
			$action = "neuerBereich2";
		}
		elseif ($benutzerPasswort != $benutzerPasswort2) {
			$fehler .= "Die Wiederholung des Passwortes stimmt nicht.";
			$action = "neuerBereich2";
		}
		else
		{
			@unlink ($htpasswd);
			echo "<b>Passwortgeschützten Bereich erstellen</b><br><br>";
			echo "Bereich wird angelegt.<br><br>Benutzername: $benutzerName<br>Passwort: *****";

			$fp = @fopen($htpasswd, "w") or die($filerights);
			fwrite($fp,"$benutzerName:".crypt($benutzerPasswort)."\n");
			fclose($fp);

			$fp = @fopen($htaccess, "w") or die($filerights);
			fwrite($fp,"AuthType Basic\n");
			fwrite($fp,"AuthName \"$bereichName\"\n");
			fwrite($fp,"AuthUserFile $ordner1.htpasswd\n");
			fwrite($fp,"require valid-user\n");
			fclose($fp);
			
			echo "<br><br>Sie können nun weitere Benutzer hinzufügen (Editierung).";
		}
	}
}

if ($action == "neuerBereich2") {
	if (file_exists($htaccess)) {
		echo $fileexist;
	}
	else
	{
		
			echo "<b>Passwortgeschützten Bereich erstellen</b><br><br>";
			if ($fehler) {
				echo "<font color='#ff0000'>$fehler</font><br>";
			}
			echo "Benutzer hinzufügen:";
			echo "<form action='".$_SERVER['PHP_SELF'] ."' method='post'>
<INPUT TYPE='hidden' NAME='action' VALUE='neuerBereich3'>
<input TYPE='hidden' NAME='bereichName' size='12' class='input' value='".$bereichName."'><br><br>
Benutzername:<br>
<input TYPE='text' NAME='benutzerName' size='12' class='input' value='".$benutzerName."'><br><br>
Passwort:<br>
<input TYPE='password' NAME='benutzerPasswort' size='12' class='input' value='".$benutzerPasswort."'><br><br>
Passwort (Wiederholung):<br>
<input TYPE='password' NAME='benutzerPasswort2' size='12' class='input' value='".$benutzerPasswort2."'><br><br>
<input TYPE='submit' VALUE='Weiter &raquo;' class='button'></form>";
	}
}

if ($action == "editName2") {
	if (!file_exists($htaccess)) {
		echo $filenoexist;
	}
	else
	{
		if (strlen($bereichName) < 3 || strlen($bereichName) > 20) {
			$fehler = "Der Name muss zwischen 3 und 20 Zeichen lang sein.";
			$action = "editName";
		}
		else
		{
			echo "<b>Passwortgeschützten Bereich umbenennen</b><br><br>";
			unlink ($htaccess) or die("Die Datei .htaccess kann nicht geloescht werden.");

			$fp = @fopen($htaccess, "w") or die($filerights);
			fwrite($fp,"AuthType Basic\n");
			fwrite($fp,"AuthName \"$bereichName\"\n");
			fwrite($fp,"AuthUserFile $ordner1.htpasswd\n");
			fwrite($fp,"require valid-user\n");
			fclose($fp);
			
			echo "Der Bereich wurde umbenannt.";
		}
	}
}

if ($action == "editName") {
	echo "<b>Passwortgeschützten Bereich umbenennen</b><br><br>";
	if ($fehler) {
		echo "<font color='#ff0000'>$fehler</font><br>";
	}

	$datei = @file($htaccess, "r");
	foreach ($datei as $zeile) {
		$zeile = explode(" ", $zeile);
		$zeile = array_reverse($zeile);
		$bez = array_pop($zeile);
		$zeile = array_reverse($zeile);
		$zeile = implode(" ", $zeile);
		
		$typ[$bez] = $zeile;			
	}
	$typ['AuthName'] = str_replace($cfg['ad'],"",$typ['AuthName']);
	$typ['AuthName'] = str_replace("\"","",$typ['AuthName']);
	
	if (!$bereichName) $bereichName = $typ['AuthName'];
		
	if (file_exists($htaccess)) {
		echo "<form action='".$_SERVER['PHP_SELF'] ."' method='post'>
<INPUT TYPE='hidden' NAME='action' VALUE='editName2'>
Wie soll der Bereich heißen?<br>
<input TYPE='text' NAME='bereichName' size='12' class='input' value='".$bereichName."'><br><br>
<input TYPE='submit' VALUE='Weiter &raquo;' class='button'></form>";
	}
	else
	{
			echo $filenoexist;
	}
}

if ($action == "editBenutzer2") {
	if (!file_exists($htaccess)) {
		echo $filenoexist;
	}
	else
	{
		$fehler = "";
		if (!$name) {
			$fehler .= "Der Benutzername wurde nicht übergeben.";
			$action = "editBenutzer";
		}
		elseif (strlen($benutzerPasswort) < 5 || strlen($benutzerPasswort) > 20) {
			$fehler .= "Das Passwort muss zwischen 5 und 20 Zeichen lang sein.";
			$action = "editBenutzer";
		}
		elseif ($benutzerPasswort != $benutzerPasswort2) {
			$fehler .= "Die Wiederholung des Passwortes stimmt nicht.";
			$action = "editBenutzer";
		}
		else
		{
			echo "<b>Passwortgeschützten Bereich editieren</b><br><br>";
			echo "Benutzer wird geändert. Benutzername: <b>$name</b>";

			$neuesPW = crypt($benutzerPasswort);
			echo "<br><br>Das Passwort wird ersetzt.";

			$datei = @file($htpasswd, "r");
			unlink ($htpasswd) or die("Die Datei .htpasswd kann nicht geloescht werden.");
		 	$fp = @fopen($htpasswd, "w") or die($filerights1);
		 	$drin = array();
			foreach ($datei as $zeile) {
				$zeile = explode(":",$zeile);
				if ($zeile[0] != $name) {
					fwrite($fp,implode(":",$zeile));
				}
				else
				{
					$neueZeile=$name.":";
					$neueZeile .= $neuesPW;
					fwrite($fp,"$neueZeile\n");
				}
			}
			fclose($fp);
			echo "<br><br>Fertig.";
		}
	}
}

if ($action == "editBenutzer") {
	echo "<b>Passwortgeschützten Bereich editieren</b><br><br>";
	if ($fehler) {
		echo "<font color='#ff0000'>$fehler</font><br>";
	}
	if (file_exists($htaccess)) {
			echo "Benutzer editieren:";
			if (!$benutzerName) $benutzerName = $name;
			echo "<form action='".$_SERVER['PHP_SELF'] ."' method='post'>
<INPUT TYPE='hidden' NAME='action' VALUE='editBenutzer2'>
<INPUT TYPE='hidden' NAME='name' VALUE='$name'>
<br>
Benutzername: <b>$name</b><br><br>
Passwort:<br>
<input TYPE='password' NAME='benutzerPasswort' size='12' class='input' value='".$benutzerPasswort."'><br><br>
Passwort (Wiederholung):<br>
<input TYPE='password' NAME='benutzerPasswort2' size='12' class='input' value='".$benutzerPasswort2."'><br><br>
<input TYPE='submit' VALUE='Weiter &raquo;' class='button'></form>";
	}
	else
	{
			echo $filenoexist;
	}
}

if ($action == "hinzuBenutzer2") {
	if (!file_exists($htaccess)) {
		echo $filenoexist;
	}
	else
	{
		$fehler = "";
		$datei = @file($htpasswd, "r");
		foreach ($datei as $zeile) {
			$zeile = explode(":",$zeile);
			
			if ($zeile[0] == $benutzerName) {
				$fehler .= "Der Benutzername ist schon vorhanden.<br>";
			}
		}
		if (strlen($benutzerName) < 3 || strlen($benutzerName) > 20) {
			$fehler .= "Der Benutzername muss zwischen 3 und 20 Zeichen lang sein.";
			$action = "hinzuBenutzer";
		}
		elseif (strlen($benutzerName) < 3 || strlen($benutzerName) > 20) {
			$fehler .= "Das Passwort muss zwischen 5 und 20 Zeichen lang sein.";
			$action = "hinzuBenutzer";
		}
		elseif ($benutzerPasswort != $benutzerPasswort2) {
			$fehler .= "Die Wiederholung des Passwortes stimmt nicht.";
			$action = "hinzuBenutzer";
		}
		elseif ($fehler) {
			$action = "hinzuBenutzer";
		}
		else
		{
			echo "<b>Passwortgeschützten Bereich editieren</b><br><br>";
			echo "Benutzer wird hinzugefügt.<br><br>Benutzername: <b>$benutzerName</b>";
			$datei = @file($htpasswd, "r");
			unlink ($htpasswd) or die("Die Datei .htpasswd kann nicht geloescht werden.");
		 	$fp = @fopen($htpasswd, "w") or die($filerights1);
			foreach ($datei as $zeile) {
				fwrite($fp,$zeile);
			}
			fwrite($fp,"$benutzerName:".crypt($benutzerPasswort)."\n");
			fclose($fp);

			echo "<br><br>Fertig.";
		}
	}
}

if ($action == "hinzuBenutzer") {
	echo "<b>Passwortgeschützten Bereich editieren</b><br><br>";
	if ($fehler) {
		echo "<font color='#ff0000'>$fehler</font><br>";
	}

	if (file_exists($htaccess)) {
			echo "Benutzer hinzufügen:";
			echo "<form action='".$_SERVER['PHP_SELF'] ."' method='post'>
<INPUT TYPE='hidden' NAME='action' VALUE='hinzuBenutzer2'>
Benutzername:<br>
<input TYPE='text' NAME='benutzerName' size='12' class='input' value='".$benutzerName."'><br><br>
Passwort:<br>
<input TYPE='password' NAME='benutzerPasswort' size='12' class='input' value='".$benutzerPasswort."'><br><br>
Passwort (Wiederholung):<br>
<input TYPE='password' NAME='benutzerPasswort2' size='12' class='input' value='".$benutzerPasswort2."'><br><br>
<input TYPE='submit' VALUE='Weiter &raquo;' class='button'></form>";
			echo "Lassen Sie das Passwortfeld leer, falls das alte Passwort erhalten bleiben soll.";
	}
	else
	{
			echo $filenoexist;
	}
}

if ($action == "editBereich") {
	echo "<b>Passwortgeschützten Bereich editieren</b><br><br>";
	if ($fehler) {
		echo "<font color='#ff0000'>$fehler</font><br>";
	}
	if (file_exists($htaccess)) {
		$datei = @file($htaccess, "r");
		foreach ($datei as $zeile) {
			$zeile = explode(" ", $zeile);
			$zeile = array_reverse($zeile);
			$bez = array_pop($zeile);
			$zeile = array_reverse($zeile);
			$zeile = implode(" ", $zeile);
			$typ[$bez] = $zeile;			
		}
		$typ['AuthName'] = str_replace($cfg['ad'],"",$typ['AuthName']);
		$typ['AuthName'] = str_replace("\"","",$typ['AuthName']);
		
		echo "Bereichsname: <b>".$typ['AuthName']."</b> [<a href='?action=editName'>&raquo; Ändern</a>]<br><br>";
		echo "<b>Benutzer</b><br>";
		$datei = @file($htpasswd, "r");
		echo "<table border='0'>";
		foreach ($datei as $zeile) {
			$zeile = explode(":",$zeile);
			if ($zeile[0] != "\n") echo "<tr><td>".$zeile[0] . "</td><td>[<a href='?action=editBenutzer&name=$zeile[0]'>&raquo; Ändern</a>] [<a href='?action=delBenutzer&name=$zeile[0]'>&raquo; Löschen</a>]</td></tr>";
		}
		echo "</table>";
		echo "[<a href='?action=hinzuBenutzer'>&raquo; Hinzufügen</a>]";
	}
	else
	{
		echo $filenoexist;
	}
}

if ($action == "delBenutzer2") {
	echo "<b>Benutzer löschen</b><br><br>";
	$datei = @file($htpasswd, "r");
	if (sizeof($datei) <= 1) {
		echo "Der letzte Benutzer eines Bereiches kann nicht gelöscht werden.";
	}
	else
	{
		unlink ($htpasswd) or die("Die Datei .htpasswd kann nicht geloescht werden.");
	 	$fp = @fopen($htpasswd, "w") or die($filerights1);
		foreach ($datei as $zeile) {
			$zeile = explode(":",$zeile);
			
			if ($zeile[0] != $name)
				fwrite($fp,implode(":",$zeile));
		}
		fclose($fp);
		echo "Der Benutzer wurde entfernt.";
	}
}

if ($action == "delBenutzer") {
	echo "<b>Benutzer löschen</b><br><br>";
	if (file_exists($htaccess)) {
		echo "Möchten Sie den Benutzer <b>$name</b> wirklich entfernen?<br><br><a href='?action=delBenutzer2&name=$name'>Ja!</a>";
	}
	else
	{
		echo $filenoexist;
	}
}

if ($action == "delBereich2") {
	echo "<b>Passwortgeschützten Bereich löschen</b><br><br>";
	unlink ($htaccess) or die("Die Datei .htaccess kann nicht geloescht werden.");
	unlink ($htpasswd) or die("Die Datei .htpasswd kann nicht geloescht werden.");
	echo "Der Passwortschutz wurde aufgehoben.";
}

if ($action == "delBereich") {
	echo "<b>Passwortgeschützten Bereich löschen</b><br><br>";
	if (file_exists($htaccess)) {
		echo "Möchten Sie den Passwortschutz wirklich aufheben?<br><br><a href='?action=delBereich2'>Ja!</a>";
	}
	else
	{
		echo $filenoexist;
	}
}

echo $cfg['footer'];
exit;
?>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_show_category.inc.php 1262 2006-10-27 10:00:32Z mz $

   YAML für xt:Commerce - Tabellenfreie Templates
   http://www.zs-ecommerce.com

   Copyright (c) 2007 Björn Teßmann for Zerosoftware GbR zerosoft.de
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.23 2002/11/12); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_show_category.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2003-2007 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function xtc_show_category($counter, $levelNow = -1 , $getPrev = "-1" ) {
	global $foo, $categories_string, $id;

	// Kategoriennamen umwandeln, so dass eine id-Zuweisung für die CSS-Formatierung möglich wird
	// Thanks to Reinhard Hiebl (www.joomla-template-yaml.de/)
	// Umlaute Ersetzen
	$getId = $foo[$counter]['name'] ;
	$searchInId = array("ä" , "ö", "ü", "ß", "Ä", "Ö", "Ü", " ",);
	$replaceInId = array("ae" , "oe", "ue", "ss", "Ae", "Oe", "Ue", "");
	$getId = str_replace($searchInId, $replaceInId, $getId);
	// Sonderzeichen entfernen
	$getId = preg_replace("/[^a-zA-Z0-9_]/" , "" , $getId);
	// Alles in klein
	$getId = strtolower($getId);

	// Wenn das erste Element wird als Ebene -1 zugewiesen
	if ($getPrev == '-1') {
		$foo[$getPrev]['level'] = "-1";
	}
	// Nächste ID wird als Variable definiert
	$getNext = $foo[$counter]['next_id'];

	// Wenn das erste Element wird die Body-Box und eine float-Box geöffnet
    if ($foo[$counter]['level']=='') {
	   	if (strlen($categories_string)=='0') {
    		$categories_string .= '';
		}
	}

	// Öffne Liste wenn Elementebene des vorherigen Elements kleiner dem aktuellen ist
	if ($foo[$getPrev]['level'] < $foo[$counter]['level']) {
		$categories_string .= '<ul>';
	}

	// Überprüfung ob Elemnt aktiv, sowie öffnen des Listenelements
	if ( ($id) && (in_array($counter, $id)) ) {
    	$categories_string .= '<li class="activeCat" id="cid'.$getId.'">';
    } else {
		$categories_string .= '<li id="cid'.$getId.'">';
	}

	// Linkausgabe
	$categories_string .= '<a href="';
	$cPath_new=xtc_category_link($counter,$foo[$counter]['name']);
    $categories_string .= xtc_href_link(FILENAME_DEFAULT, $cPath_new);
    $categories_string .= '">';
    $categories_string .= $foo[$counter]['name'] ;

	// Gibt die Anzahl der Produkte in der Kategorie aus (wenn aktiviert)
    if (SHOW_COUNTS == 'true') {
		$products_in_category = xtc_count_products_in_category($counter);
		if ($products_in_category > 0) {
        	$categories_string .= '&nbsp;(' . $products_in_category . ')';
      	}
    }

	// Überprüfung ob Elemnt aktiv
	if ( ($id) && (in_array($counter, $id)) ) {
	// Wenn aktuelle Elementebene kleiner als die nächste, schließe Listenelement, sowie Beenden des Links
		if ($foo[$counter]['level'] < $foo[$getNext]['level']) {
	    	$categories_string .= '</a>';
		} else {
			$categories_string .= '</a></li>';
		}
    } else {
		if ($foo[$counter]['level'] < $foo[$getNext]['level']) {
	    	$categories_string .= '</a>';
		} else {
			$categories_string .= '</a></li>';
		}
	}

	// Wenn nächste Elementebene kleiner ist als die aktuelle, soviele Schließtags wie Differenz ist
	if ($foo[$getNext]['level'] < $foo[$counter]['level'] ) {
		$cul = $foo[$counter]['level'] - $foo[$getNext]['level'] ;
		for ($iul = 1; $iul <= $cul  ; $iul++ ) {
			$categories_string .= '</ul></li>';
		}
	}

	// Wenn weitere Elemente vorhanden sind, rufe Funktion mit nächstem Element auf, andernfalls schließe Ebene 1 und Boxen
    if ($foo[$counter]['next_id']) {
		xtc_show_category($foo[$counter]['next_id'], $foo[$counter]['level'], $counter );
    } else {
		$categories_string .= '</ul>';
    }
}

?>

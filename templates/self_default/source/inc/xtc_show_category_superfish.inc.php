<?php
/* 
// -----------------------------------------------------------------------------------
	GUNNART "SHOW_CATEGORY ADVANCED"
	
	erweiterte Kategorien-Navigation für xt:Commerce 3.04 SP1 / SP2.1
	
	Proudly togetherfummeled by Gunnar Tillmann
	http://www.gunnart.de
	Version 2.0 Beta / April 2008 
// -----------------------------------------------------------------------------------
	... ist noch Beta - Anleitung unter http://www.gunnart.de?p=360
// -----------------------------------------------------------------------------------
*/


// -----------------------------------------------------------------------------------
// 	KONFIGURATION
// -----------------------------------------------------------------------------------
	$CatConfig = array(
		
		// Bis zu welcher Ebene soll der Kategorien-Baum standardmäßig 
		// aufgeklappt sein? 
		// false, wenn er komplett ausgeklappt sein soll.
		'MaxLevel' 			=> 	false,
		
		// Leere Kategorien verstecken? true: ja, false: nein
		'HideEmpty' 		=> 	true,
		
		// Dürfen aktive Kategorien weitere Unterkategorien aufklappen lassen?
		'ShowAktSub' 		=> 	false,
		
		// Kategorien-Tiefe: Wie soll die CSS-Klasse benannt werden?
		'ListPrefix'		=>	'men',
		
		// Aktive Kategorie: Soll der Link markiert werden?
		'MarkAktivLink'		=> 	false,
		'LinkCurrent'		=> 	'active',
		'LinkCurrentParent'	=> 	'active',
		
		// Aktive Kategorie: Soll der Listenpunkt markiert werden?
		'MarkAktivList'		=> 	true,
		'ListCurrent'		=>	'active',
		'ListCurrentParent'	=>	'active',
		
		// Sollen Kategorien mit weiteren Unterkategorien gekennzeichnet werden?
		'MarkSubMenue'		=> 	true,
		'SubMenueCss'		=> 	'',

		// Automatische Zuteilung einer CSS-ID (für den Listenpunkt)
		'ShowCssIdList'		=> 	false,
		'CssPrefixList'		=> 	'MyCat',
		
		// Automatische Zuteilung einer CSS-ID (für den Link)
		'ShowCssIdLink'		=> 	false,
		'CssPrefixLink'		=> 	'MyCatLink',
		
		// Darstellung Produktzählung, falls eingeschaltet
		'CountPre'			=> 	'<em>(',	
		'CountAfter'		=>	')</em>',
		
		// Tags außerhalb des Links?
		'LinkPre'			=>	false,		// z.B. '<div>',
		'LinkAfter'			=>	false,		// z.B. '</div>',

		// Tags innerhalb des Links?
		'NamePre'			=>	false,		// z.B. '<span>',
		'NameAfter'			=>	false,		// z.B. '</span>',
		
		// Soll die Überschrift nach Css-Markern à la {#class:EinName#} 
		// durchsucht werden? So kann man z.B. einzelne 
		// Links speziell gestalten.
		'CssMarkersToList'	=>	false, 		// Gefundene Marker zur Liste?
		'CssMarkersToLink'	=>	false		// Gefundene Marker zum Link?
	
	);  
// -----------------------------------------------------------------------------------
	$CurrentURL = xtc_href_link(basename($_SERVER[SCRIPT_NAME]),xtc_get_all_get_params(array('XTCsid')));
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
//	Findet heraus, ob Kategorie $category_id aktive (und für die Kundengruppe 
//	zugelassene) Artikel enthält. 
// -----------------------------------------------------------------------------------
//	Im Gegensatz zu xtc_count_products_in_category()
// 	werden hierbei die Berechtigungen und der FSK-Status geprüft.
// -----------------------------------------------------------------------------------
	function countProductsInCat($category_id) {
	
		$products_count = 0;
	 	if ($_SESSION['customers_status']['customers_fsk18_display'] == '0')
			$fsk_lock = "AND \tp.products_fsk18!=1 ";
		if (GROUP_CHECK=='true') 
	   		$prod_group_check = "AND \tp.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		$products_query = xtDBquery("
			SELECT 	count(*) as total 
			FROM 	".TABLE_PRODUCTS." p, 
			".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
			WHERE 	p.products_id = p2c.products_id 
			".$prod_group_check."
			".$fsk_lock." 
			AND	 	p.products_status = '1' 
			AND 	p2c.categories_id = '".$category_id."'");
		$products = xtc_db_fetch_array($products_query,true);
		$products_count += $products['total'];
		
		if (GROUP_CHECK=='true') 
			$cat_group_check = "AND \tgroup_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		$child_categories_query = xtDBquery("
			SELECT 	categories_id 
			FROM 	".TABLE_CATEGORIES." 
			WHERE 	parent_id = '".$category_id."' 
			".$cat_group_check."
			AND 	categories_status = '1'");
		if (xtc_db_num_rows($child_categories_query,true)) {
			while ($child_categories = xtc_db_fetch_array($child_categories_query,true)) {
				$products_count += countProductsInCat($child_categories['categories_id']);
			}
		}
		
		return $products_count;
	}
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
//	... ist $CurrentURL im Kategorien-Pfad drin?
// -----------------------------------------------------------------------------------
	function isInPath($CurrentURL,$CatID=false) {
		global $foo;
		if($CatID) {
			if($CurrentURL == $foo[$CatID]['link']) {
				return true;
			} elseif(is_array($foo[$CatID]['subcats'])) {
				foreach($foo[$CatID]['subcats'] as $SubCatID) {
					if(isInPath($CurrentURL,$SubCatID))
						return true;
				}
			}
		}
		return false;
	}
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
//	Hauptfunktion
// -----------------------------------------------------------------------------------
	function xtc_show_category_superfish($cid, $level, $foo, $cpath) {
	
		global	$old_level, 
				$categories_string,
				$CatConfig,
				$CurrentURL;
		
		$CatConfig['MaxLevel'] = intval($CatConfig['MaxLevel']);
		
		// 1) Zählen ist nicht immer nötig
		if($CatConfig['HideEmpty'] || SHOW_COUNTS == 'true')
			$pInCat = countProductsInCat($cid);
	
		// 2) Überprüfen, ob Kategorie Produkte enthält
		if($CatConfig['HideEmpty']) {
			$Empty = true;
			if ($pInCat > 0)
				$Empty = false;
		} else {
			$Empty = false;
		}	
		
		// 3) Überprüfen, ob Kategorie gezeigt werden soll
		$Show = false;
		if($CatConfig['HideEmpty']) {
			if(!$Empty)
				$Show = true;
		} else {
			$Show = true;
		}
	
		// 3) Überprüfen, ob Unterkategorien gezeigt werden sollen
		$ShowSub = false;
		if($CatConfig['MaxLevel']) {
			if ($level < $CatConfig['MaxLevel'])
				$ShowSub = true;
		} else {
			$ShowSub = true;
		}
					
		if($Show) { // Wenn Kategorie gezeigt werden soll ....
		
			if($cid != 0) {
				
				$category_path 		= explode('_',$GLOBALS['cPath']); 
				$in_path 			= in_array($cid, $category_path);
				$this_category 		= array_pop($category_path);
				
				if(empty($this_category)) {
					if(isInPath($CurrentURL,$cid))
						$in_path = true;
				}
			
				for ($a = 0; $a < $level; $a++);
				
				$ProductsCount = false;
				if(SHOW_COUNTS == 'true') 
					$ProductsCount = ' '.$CatConfig['CountPre'].$pInCat.$CatConfig['CountAfter'];	
	                                                  
				// Aktiv - Nicht Aktiv usw.
				$Collapse 
				= $Expand 
				= $Aktiv 
				= $AktivList 
				= $AktivLink 
				= $CssClassMarker 
				= false;
				
				// Nach Collapse- bzw. Expand-Markern suchen
				if(strstr(strtolower($foo[$cid]['heading']),'{#collapse#}')) 
					$Collapse = true;
				if(strstr(strtolower($foo[$cid]['heading']),'{#expand#}')) 
					$Expand = true;
					
				$ListClass[] = $CatConfig['ListPrefix'];
				
				// Nach CSS-Markern suchen
				if($CatConfig['CssMarkersToList']||$CatConfig['CssMarkersToLink']) {
					if(preg_match("/\{\#class\:([^\#\}]+)\#\}/i",$foo[$cid]['heading'],$Treffer)) { 
						$CssClassMarker = trim($Treffer[1]);
						if($CatConfig['CssMarkersToList']&&!empty($CssClassMarker))
							$ListClass[] = $CssClassMarker;
						if($CatConfig['CssMarkersToLink']&&!empty($CssClassMarker))
							$LinkClass[] = $CssClassMarker;
					}
				}
				
				if($this_category == $cid || $foo[$cid]['link'] == $CurrentURL) {
					// Wenn Kategorie aktiv ist
					if($CatConfig['MarkAktivLink']) {
						$LinkClass[] = $CatConfig['LinkCurrent'];
					}
					if($CatConfig['MarkAktivList']) {
						$ListClass[] = $CatConfig['ListCurrent'];
					}
					$Aktiv = true;
				}elseif($in_path) { 
					// Wenn Oberkategorie aktiv ist
					if($CatConfig['MarkAktivLink']) {
						$LinkClass[] = $CatConfig['LinkCurrentParent'];
					} 
					if($CatConfig['MarkAktivList']) {
						$ListClass[] = $CatConfig['ListCurrentParent'];
					}
					$Aktiv = true;
				}
		
				// Hat ein SubMenue - hat kein SubMenue
				// CSS-Klasse festlegen
				if($CatConfig['MarkSubMenue'] && $foo[$cid]['subcats']) {
					$ListClass[] = $CatConfig['SubMenueCss'];
          $DropdownClose =	'</li>';
				}
				
				// Quelltext einrücken
				$Tabulator = str_repeat("\t",$level+1);

				if($CatConfig['ShowCssIdList']) {
					$ListID[] = $CatConfig['CssPrefixList'].$cid;
				}
				
				if($CatConfig['ShowCssIdLink']) {
					$LinkID[] = $CatConfig['CssPrefixLink'].$cid;
				}
		
				// Navigations-Liste hierarchisch ...
				if($old_level) { 
					if ($old_level < $level) {
//						$Pre = "\n<ul>";
 //         $UlLevel = $level-1;  
	//				$UlListClass = ' class=" level'.$UlLevel.'">';
	//					$Pre = str_replace("<ul>",$Tabulator."<ul".$UlListClass, $Pre)."\n";

					} else {
						$Pre = "</li>\n";
						if ($old_level > $level) {
							// Listenpunkte schließen
							// Quelltext einrücken
							for ($counter = 0; $counter < $old_level - $level; $counter++) {
								$Pre .= str_repeat("\t", $old_level - $counter+1 )."</ul>\n".str_repeat("\t", $old_level - $counter)."</li>\n";
							}
						}
					} 
				}
				
				if(is_array($ListClass)) {
					$ListClass = ' class="'.implode(' ',$ListClass).'"';
				}
				if(is_array($ListID)) {
					$ListID = ' id="'.implode(' ',$ListID).'"';
				}
				if(is_array($LinkClass)) {
					$LinkClass = ' class="'.implode(' ',$LinkClass).'"';
				}
				if(is_array($LinkID)) {
					$LinkID = ' id="'.implode(' ',$LinkID).'"';
				}
        
				if($CatConfig['MarkSubMenue'] && $foo[$cid]['subcats']) {
				// Listenpunkte zusammensetzen wenn Unterkategorie vorhanden ist
        if ($level > 1) {
        $DropdownClass = str_replace('class="','class="dropdown-submenu ',$ListClass);
        $linkhref = 'href="' . $foo[$cid]['link'] . '"';
        } else {
        $DropdownClass = str_replace('class="','class="dropdown ',$ListClass);
        $caret =  '<b class="caret"></b>';
        $linkhref = 'href="' . $foo[$cid]['link'] . '"';
        }
  			$categories_string .=	$Pre.$Tabulator.
										'<li'.$ListID.$DropdownClass.'><a '.$linkhref.' class="dropdown-toggle">'.$foo[$cid]['name'].' '.$caret.'</a>'."\n".str_repeat("\t",$level+2).'<button class="btn btn-navbar dropdown-toggle" data-toggle="dropdown" data-target="#">'."\n".str_repeat("\t",$level+2).'<span class="icon-bar"></span>'."\n".str_repeat("\t",$level+2).'<span class="icon-bar"></span>'."\n".str_repeat("\t",$level+2).'<span class="icon-bar"></span>'."\n".str_repeat("\t",$level+2).'</button>'."\n".str_repeat("\t",$level+2).'<ul class="dropdown-menu">'."\n";
			} else {
				// Listenpunkte zusammensetzen
				$categories_string .=	$Pre.
                    
										$Tabulator.
										'<li'.$ListID.$ListClass.'>'.$CatConfig['LinkPre'].
										'<a'.$LinkID.$LinkClass.' href="' . $foo[$cid]['link'] . '">'.
										$CatConfig['NamePre'].
										$foo[$cid]['name'].
										$ProductsCount.
										$CatConfig['NameAfter'].
										'</a>'.
										$CatConfig['LinkAfter'];  
        }
			}
			
			// für den nächsten Durchgang ...
			$old_level = $level;
		
			// Unterkategorien durchsteppen
			foreach ($foo as $key => $value) {
		
				if ($foo[$key]['parent'] == $cid) {
						
					// Sollen Unterkategorien gezeigt werden?
					if($CatConfig['ShowAktSub'] && $Aktiv)
						$ShowSub = true;
					
					// Wenn Collapse, dann immer eingeklappt
					if($ShowSub && $Collapse && !$Aktiv)
						$ShowSub = false;
					
					// Wenn Expand, dann ausgeklappt
					if($ShowSub || $Expand) 
						xtc_show_category_superfish($key, $level+1, $foo, ($level != 0 ? $cpath . $cid . '_' : ''));
				} 
			}
		} // Ende if($Show)
	} 	
// -----------------------------------------------------------------------------------
		
?>
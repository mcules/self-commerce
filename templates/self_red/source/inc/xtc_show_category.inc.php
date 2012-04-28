<?php 
/* 
    ----------------------------------------------------------------------------------------- 
    $Id$ 

    XT-Commerce - community made shopping 
    http://www.xt-commerce.com 

    Copyright (c) 2003 XT-Commerce 
    ----------------------------------------------------------------------------------------- 
    based on: 
    (c) 2000-2001 The Exchange Project  (earlier name of osCommerce) 
    (c) 2002-2003 osCommerce(categories.php,v 1.23 2002/11/12); www.oscommerce.com 
    (c) 2003     nextcommerce (xtc_show_category.inc.php,v 1.4 2003/08/13); www.nextcommerce.org 

    Released under the GNU General Public License 
    ----------------------------------------------------------------------------------------- 
    Modified to get a cleaner HTML-output without tables 
    Bugfix 
    Shows, which categories have subcategories 
    by Gunnar Tillmann 
    http://www.gunnart.de 
    ----------------------------------------------------------------------------------------- 
    Modified to get neatly nested unordered lists instead of marking sublevels with classes 
    by Matt Slovig 
    http://www.wibros.de 
    ----------------------------------------------------------------------------------------- 
*/ 

function xtc_show_category($counter) { 
    global $foo, $categories_string, $id, $current_level; 

    //start the loop 
    for ($a=0; $a<$foo[$counter]['level']; $a++) {}
    
    if(empty($categories_string)) {
        $categories_string = "<ul>";    
    } 

    $cPath_new = xtc_category_link($counter,$foo[$counter]['name']); 
    if (SHOW_COUNTS == 'true') { 
        $products_in_category = xtc_count_products_in_category($counter);
    } 
    if ($foo[$counter]['next_id']) { // There's another entry coming        
        if($foo[$foo[$counter]["next_id"]]["level"]>$foo[$counter]["level"]) { // next entry is one level below the current (ie submenu to current entry)
            $ul = "<ul>";    // open a new list
        }
        if($foo[$foo[$counter]["next_id"]]["level"]<$foo[$counter]["level"]) { // next entry is at least one level above the current (ie current submenu ends here)
            $ul = "</li>"; // close current entry
            for($x=$foo[$counter]["level"];$x>$foo[$foo[$counter]["next_id"]]["level"];$x--) {
                $ul .= "</ul></li>\r\n";    // close all remaining lists. As it is possible to jump from a deep level directly to root level, we have to do this by iterating from current level to next level
            }
        }
        if($foo[$foo[$counter]["next_id"]]["level"]==$foo[$counter]["level"]) { // next entry is on the same level
            $ul = "</li>\r\n"; // jut close the list entry
        }
    }
    $categories_string .= sprintf("\r\n<li class='%s %s'><a href='%s' class='%s'>%s</a>%s%s", $foo[$counter]['level']=='' ? "main" : "sub", xtc_has_category_subcategories($counter)==true ? "submenu" : "normal", xtc_href_link(FILENAME_DEFAULT, $cPath_new), isset($id) && is_array($id) && in_array($counter, $id) ? "active" : "normal", $foo[$counter]['name'], isset($products_in_category) && $products_in_category>0 ? sprintf(' <span class="catcount">(%s)</span>', $products_in_category) : "", $ul); // building it together

    $current_level = $foo[$counter]["level"]; // set current level, so we can iterate downwards when we are done 
    if ($foo[$counter]['next_id'])     {
        xtc_show_category($foo[$counter]['next_id']); 
    } else {
        // close remaining </ul>'s, iterating down from $current_level to 0        
        for($x=$current_level;$x>0;$x--) { 
            $categories_string .= '</li></ul>';
        }
        $categories_string .= '</li></ul>'; // Close the entire list
    } 
} 
?>

<?php
if (GROUP_CHECK == 'true') {
	$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}
$and_lang = "and cd.language_id = '".(int) $_SESSION['languages_id']."'";

		$category_query = "SELECT
		                        cd.categories_description,
		                        cd.categories_name,
		                        cd.categories_heading_title,
		                        c.categories_template,
		                        c.categories_image from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                            WHERE c.categories_id = '".$current_category_id."' AND cd.categories_id = '".$current_category_id."'
                            ".$group_check."
                            ".$and_lang;

		$category_query = xtDBquery($category_query);

		$category = xtc_db_fetch_array($category_query, true);
    
// build the select for categories_query
    $categories_select = "SELECT
                            cd.categories_description,
                            c.categories_id,
                            cd.categories_name,
                            cd.categories_heading_title,
                            c.categories_image,
                            c.parent_id
                          FROM
                            ".TABLE_CATEGORIES." c, 
                            ".TABLE_CATEGORIES_DESCRIPTION." cd
                          WHERE
                            c.categories_status = '1'";
    
    $categories_select_end = "AND
                                c.categories_id = cd.categories_id
                                ".$group_check."
                                ".$and_lang."
                                ORDER BY
                                    sort_order,
                                    cd.categories_name";
// end build the select for categories_query    
		if (isset ($cPath) && preg_match('/_/', $cPath)) {
			// check to see if there are deeper categories within the current category
			$category_links = array_reverse($cPath_array);
			for ($i = 0, $n = sizeof($category_links); $i < $n; $i ++) {

				$categories_query = $categories_select."
                          and 
                            c.parent_id = '".$category_links[$i]."'
                          ".$categories_select_end."
                          ";
				$categories_query = xtDBquery($categories_query);

				if (xtc_db_num_rows($categories_query, true) < 1) {
					// do nothing, go through the loop
				} else {
					break; // we've found the deepest category the customer is in
				}
			}
		} else {

			$categories_query = $categories_select."
                        and 
                          c.parent_id = '".$current_category_id."'
			                  ".$categories_select_end."
                        ";
			$categories_query = xtDBquery($categories_query);
		}

		$rows = 0;
		while ($categories = xtc_db_fetch_array($categories_query, true)) {
			$rows ++;
			
			$cPath_new = xtc_category_link($categories['categories_id'],$categories['categories_name']);
			
			$width = (int) (100 / MAX_DISPLAY_CATEGORIES_PER_ROW).'%';
			$image = '';
			if ($categories['categories_image'] != '') {
				$image = DIR_WS_IMAGES.'categories/'.$categories['categories_image'];
			}

			$categories_content[] = array ('CATEGORIES_NAME' => $categories['categories_name'], 'CATEGORIES_HEADING_TITLE' => $categories['categories_heading_title'], 'CATEGORIES_IMAGE' => $image, 'CATEGORIES_LINK' => xtc_href_link(FILENAME_DEFAULT, $cPath_new), 'CATEGORIES_DESCRIPTION' => $categories['categories_description']);

		}
		$new_products_category_id = $current_category_id;
		include (DIR_WS_MODULES.FILENAME_NEW_PRODUCTS);

		$image = '';
		if ($category['categories_image'] != '') {
			$image = DIR_WS_IMAGES.'categories/'.$category['categories_image'];
		}
		$default_smarty->assign('CATEGORIES_NAME', $category['categories_name']);
		$default_smarty->assign('CATEGORIES_HEADING_TITLE', $category['categories_heading_title']);

		$default_smarty->assign('CATEGORIES_IMAGE', $image);
		$default_smarty->assign('CATEGORIES_DESCRIPTION', $category['categories_description']);

		$default_smarty->assign('language', $_SESSION['language']);
		$default_smarty->assign('module_content', $categories_content);

		// get default template
		if ($category['categories_template'] == '' or $category['categories_template'] == 'default') {
			$files = array ();
			if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/')) {
				while (($file = readdir($dir)) !== false) {
					if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
						$files[] = array ('id' => $file, 'text' => $file);
					} //if
				} // while
				closedir($dir);
			}
			$category['categories_template'] = $files[0]['id'];
		}

		$default_smarty->caching = 0;
		$main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/categorie_listing/'.$category['categories_template']);
		$smarty->assign('main_content', $main_content);
?>		

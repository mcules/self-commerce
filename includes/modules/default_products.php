<?php
//fsk18 lock
$fsk_lock = '';
if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
  $fsk_lock = ' and p.products_fsk18!=1';
}
$group_check = '';
if (GROUP_CHECK == 'true') {
  $group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
}
$and_lang = "and pd.language_id = '".(int) $_SESSION['languages_id']."'";

$listing_fields = '
				                  p.products_fsk18,
                          p.products_shippingtime,
                          p.products_model,
                          p.products_ean,
                          pd.products_name,
                          p.products_id,
                          m.manufacturers_name,
                          p.products_quantity,
                          p.products_image,
                          p.products_weight,
                          pd.products_short_description,
                          pd.products_description,
                          p.manufacturers_id,
                          p.products_price,
                          p.products_vpe,
                          p.products_vpe_status,
                          p.products_vpe_value,
                          p.products_discount_allowed,
                          p.products_tax_class_id
';
		
		switch ((int)$_GET['sorting_id']) {
			case 1:
			   $sorting=' ORDER BY pd.products_name ASC';			   
			   break;
			case 2:
			   $sorting=' ORDER BY pd.products_name DESC';
			   break;
			case 3:
			   $sorting=' ORDER BY p.products_price ASC';
			   break;
			case 4:
			   $sorting=' ORDER BY p.products_price DESC';
			   break;
			case 5:
			   $sorting=' ORDER BY m.manufacturers_name ASC';
			   break;
			case 6:
			   $sorting=' ORDER BY m.manufacturers_name DESC';
			   break;
		}
		
		// show the products of a specified manufacturer
		if (isset ($_GET['manufacturers_id'])) {
			if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {

				// sorting query
				if (!isset ($_GET['sorting_id'])) {
				$sorting_query = xtDBquery("SELECT products_sorting,
				                                            products_sorting2 FROM ".TABLE_CATEGORIES."
				                                            where categories_id='".(int) $_GET['filter_id']."'");
				$sorting_data = xtc_db_fetch_array($sorting_query,true);
				if (!$sorting_data['products_sorting'])
					$sorting_data['products_sorting'] = 'pd.products_name';
				$sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
				}
				// We are asked to show only a specific category

				$listing_sql = "select DISTINCT 
				                  ".$listing_fields."
                        from 
                          ".TABLE_PRODUCTS_DESCRIPTION." pd, 
                          ".TABLE_MANUFACTURERS." m, 
                          ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, 
                          ".TABLE_PRODUCTS." p 
                        left join 
                          ".TABLE_SPECIALS." s 
                        on 
                          p.products_id = s.products_id
				                where 
                          p.products_status = '1'
				                and 
                          p.manufacturers_id = m.manufacturers_id
				                and 
                          m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'
				                and 
                          p.products_id = p2c.products_id
				                and 
                          pd.products_id = p2c.products_id
				                ".$group_check."
				                ".$fsk_lock."
				                ".$and_lang."
				                and 
                          p2c.categories_id = '".(int) $_GET['filter_id']."'
                        ".$sorting;
			} else {
				// We show them all

				$listing_sql = "select 
				                  ".$listing_fields."				
                        from 
                          ".TABLE_PRODUCTS_DESCRIPTION." pd, 
                          ".TABLE_MANUFACTURERS." m, 
                          ".TABLE_PRODUCTS." p 
                        left join 
                          ".TABLE_SPECIALS." s 
                        on 
                          p.products_id = s.products_id
				                where 
                          p.products_status = '1'
				                and 
                          pd.products_id = p.products_id
				                ".$group_check."
				                ".$fsk_lock."
				                ".$and_lang."
				                and 
                          p.manufacturers_id = m.manufacturers_id
				                and 
                          m.manufacturers_id = '".(int) $_GET['manufacturers_id']."'
                        ".$sorting;
			}
		} else {
			// show the products in a given categorie
			if (isset ($_GET['filter_id']) && xtc_not_null($_GET['filter_id'])) {
	
				// sorting query
				if (!isset ($_GET['sorting_id'])) {
				$sorting_query = xtDBquery("SELECT products_sorting,
				                                            products_sorting2 FROM ".TABLE_CATEGORIES."
				                                            where categories_id='".$current_category_id."'");
				$sorting_data = xtc_db_fetch_array($sorting_query,true);
				if (!$sorting_data['products_sorting'])
					$sorting_data['products_sorting'] = 'pd.products_name';
				$sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
				}
				// We are asked to show only specific catgeory

				$listing_sql = "select 
				                  ".$listing_fields."			
                        from  
                          ".TABLE_PRODUCTS_DESCRIPTION." pd, 
                          ".TABLE_MANUFACTURERS." m, 
                          ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, 
                          ".TABLE_PRODUCTS." p 
                        left join 
                          ".TABLE_SPECIALS." s 
                        on 
                          p.products_id = s.products_id
				                where 
                          p.products_status = '1'
				                and 
                          p.manufacturers_id = m.manufacturers_id
				                and 
                          m.manufacturers_id = '".(int) $_GET['filter_id']."'
				                and 
                          p.products_id = p2c.products_id
				                and 
                          pd.products_id = p2c.products_id
				                ".$group_check."
				                ".$fsk_lock."
				                ".$and_lang."
				                and 
                          p2c.categories_id = '".$current_category_id."'
                        ".$sorting;
			} else {

				// sorting query
				if (!isset ($_GET['sorting_id'])) {
				$sorting_query = xtDBquery("SELECT products_sorting,
				                                            products_sorting2 FROM ".TABLE_CATEGORIES."
				                                            where categories_id='".$current_category_id."'");
				$sorting_data = xtc_db_fetch_array($sorting_query,true);
				if (!$sorting_data['products_sorting'])
					$sorting_data['products_sorting'] = 'pd.products_name';
				$sorting = ' ORDER BY '.$sorting_data['products_sorting'].' '.$sorting_data['products_sorting2'].' ';
				}
				// We show them all

				$listing_sql = "select 
				                  ".$listing_fields."				
                        from  
                          ".TABLE_PRODUCTS_DESCRIPTION." pd, 
                          ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, 
                          ".TABLE_PRODUCTS." p 
                        left join 
                          ".TABLE_MANUFACTURERS." m 
                        on 
                          p.manufacturers_id = m.manufacturers_id
				                left join 
                          ".TABLE_SPECIALS." s 
                        on 
                          p.products_id = s.products_id
				                where 
                          p.products_status = '1'
				                and 
                          p.products_id = p2c.products_id
				                and 
                          pd.products_id = p2c.products_id
				                ".$group_check."
				                ".$fsk_lock."                               
				                ".$and_lang."
				                and 
                          p2c.categories_id = '".$current_category_id."'
                        ".$sorting;
			}
		}
		// optional Product List Filter
		if (PRODUCT_LIST_FILTER > 0) {
			if (isset ($_GET['manufacturers_id'])) {
				$filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '".(int) $_SESSION['languages_id']."' and p.manufacturers_id = '".(int) $_GET['manufacturers_id']."' order by cd.categories_name";
			} else {
				$filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_MANUFACTURERS." m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '".$current_category_id."' order by m.manufacturers_name";
			}
			$filterlist_query = xtDBquery($filterlist_sql);
			if (xtc_db_num_rows($filterlist_query, true) > 1) {
				$manufacturer_dropdown = xtc_draw_form('filter', FILENAME_DEFAULT, 'cPath');
				if (isset ($_GET['manufacturers_id'])) {
					$manufacturer_dropdown .= xtc_draw_hidden_field('manufacturers_id', $_GET['manufacturers_id']);
					$options = array (array ('text' => TEXT_ALL_CATEGORIES));
				} else {
					$manufacturer_dropdown .= xtc_draw_hidden_field('cPath', $cPath);
					$options = array (array ('text' => TEXT_ALL_MANUFACTURERS));
				}
				$manufacturer_dropdown .= xtc_draw_hidden_field('sort', $_GET['sort']);
				$manufacturer_dropdown.= xtc_draw_hidden_field('sorting_id', $_GET['sorting_id']);
				$manufacturer_dropdown .= xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
				while ($filterlist = xtc_db_fetch_array($filterlist_query, true)) {
					$options[] = array ('id' => $filterlist['id'], 'text' => $filterlist['name']);
				}
				$manufacturer_dropdown .= xtc_draw_pull_down_menu('filter_id', $options, $_GET['filter_id'], 'onchange="this.form.submit()"');
				$manufacturer_dropdown .= '</form>'."\n";
			}
			//sorting_dropdown BEGINS
			$sorting_dropdown = xtc_draw_form('sorting', FILENAME_DEFAULT, 'cPath') . '&nbsp;';
			if (isset($cPath))
				$sorting_dropdown.= xtc_draw_hidden_field('cPath', $cPath);
			if (isset($_GET['manufacturers_id'])) 
				$sorting_dropdown.= xtc_draw_hidden_field('manufacturers_id', $_GET['manufacturers_id']);				
			if (isset($_GET['filter_id'])) 
				$sorting_dropdown.= xtc_draw_hidden_field('filter_id', $_GET['filter_id']);
			if (isset($_GET['manufacturers_id'])) 
      $sorting_dropdown.= xtc_draw_hidden_field('keywords', $_GET['keywords']);
      $options_sort = array(array('text' => SORT_CHANGE));
      $options_sort[] = array('id' => '1', 'text' => A_Z); 
      $options_sort[] = array('id' => '2', 'text' => Z_A); 
      $options_sort[] = array('id' => '3', 'text' => PRICE_UP); 
      $options_sort[] = array('id' => '4', 'text' => PRICE_DOWN); 
if (xtc_db_num_rows($filterlist_query, true) > 1 && $_GET['filter_id'] != 1) { 
      $options_sort[] = array('id' => '5', 'text' => MANU_UP); 
      $options_sort[] = array('id' => '6', 'text' => MANU_DOWN); 
} 
      $sorting_dropdown.= xtc_draw_pull_down_menu('sorting_id', $options_sort, $_GET['sorting_id'], 'onchange="this.form.submit()"');
      $sorting_dropdown.= '</form>' . "\n";
      //sorting_dropdown END
		}

		// Get the right image for the top-right
		$image = DIR_WS_IMAGES.'table_background_list.gif';
		if (isset ($_GET['manufacturers_id'])) {
			$image = xtDBquery("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
			$image = xtc_db_fetch_array($image,true);
			$image = $image['manufacturers_image'];
		}
		elseif ($current_category_id) {
			$image = xtDBquery("select categories_image from ".TABLE_CATEGORIES." where categories_id = '".$current_category_id."'");
			$image = xtc_db_fetch_array($image,true);
			$image = $image['categories_image'];
		}

		include (DIR_WS_MODULES.FILENAME_PRODUCT_LISTING);
?>		

<?php
 // default page
		if (GROUP_CHECK == 'true') {
			$group_check = "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
		}
		$shop_content_query = xtDBquery("SELECT
		                     content_title,
		                     content_heading,
		                     content_text,
		                     content_file
		                     FROM ".TABLE_CONTENT_MANAGER."
		                     WHERE content_group='5'
		                     ".$group_check."
		                     AND languages_id='".$_SESSION['languages_id']."'");
		$shop_content_data = xtc_db_fetch_array($shop_content_query,true);

		$default_smarty->assign('title', $shop_content_data['content_heading']);
		include (DIR_WS_INCLUDES.FILENAME_CENTER_MODULES);

		if ($shop_content_data['content_file'] != '') {
			ob_start();
			if (strpos($shop_content_data['content_file'], '.txt'))
				echo '<pre>';
			include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
			if (strpos($shop_content_data['content_file'], '.txt'))
				echo '</pre>';
			$shop_content_data['content_text'] = ob_get_contents();
			ob_end_clean();
		}

		$default_smarty->assign('text', str_replace('{$greeting}', xtc_customer_greeting(), $shop_content_data['content_text']));
		$default_smarty->assign('language', $_SESSION['language']);

		// set cache ID
		 if (!CacheCheck()) {
			$default_smarty->caching = 0;
			$main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html');
		} else {
			$default_smarty->caching = 1;
			$default_smarty->cache_lifetime = CACHE_LIFETIME;
			$default_smarty->cache_modified_check = CACHE_CHECK;
			$cache_id = $_SESSION['language'].$_SESSION['currency'].$_SESSION['customer_id'];
			$main_content = $default_smarty->fetch(CURRENT_TEMPLATE.'/module/main_content.html', $cache_id);
		}

		$smarty->assign('main_content', $main_content);
?>		

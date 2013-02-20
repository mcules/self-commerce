<?php
/*
actions for content_manager
TODO:
  wenn content gelöscht wird, auch sämtliche childs mit löschen wenn vorhanden
  
  wenn content verschoben wird in eine andere box oder sprache, müssen die childs mit verschoben werden
  
  die dropdown auswahl: sprache->box->content muß sich an die vorhergehenden dropdown auswahlen anpassen
  (je auswahl box, nur die content seiten zur verfügung stellen, welche in der box sind [AJAX???])
*/
// 
// delete content
if ($_GET['special']=='delete') {
	xtc_db_query("DELETE FROM ".TABLE_CONTENT_MANAGER." where content_id='".(int)$_GET['coID']."'");
	xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
} // delete content

// delete products content
if ($_GET['special']=='delete_product') {
	xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." where content_id='".(int)$_GET['coID']."'");
	xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER,'pID='.(int)$_GET['pID']));
} // delete products content

if ($_GET['id']=='update' or $_GET['id']=='insert') {
	// set allowed c.groups
	$group_ids='';
	if(isset($_POST['groups'])) foreach($_POST['groups'] as $b){
		$group_ids .= 'c_'.$b."_group ,";
	}
	$customers_statuses_array=xtc_get_customers_statuses();
	if (strstr($group_ids,'c_all_group')) {
		$group_ids='c_all_group,';
		for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
			$group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
		}
	}

// <!-- LINK AS CONTENT - RENE JANSEN - 2008-06-01 -->
        $content_typ=xtc_db_prepare_input($_POST['typ']);
        $content_url=xtc_db_prepare_input($_POST['cont_url']);
        $link_target=xtc_db_prepare_input($_POST['link_target']);

        if ($content_typ=='yes'){
        $content_typ=1;
        } else{
        $content_typ=0;
        }  // if

// <!-- END LINK AS CONTENT - RENE JANSEN - 2008-06-01 -->
        
	$content_title=xtc_db_prepare_input($_POST['cont_title']);
	$content_header=xtc_db_prepare_input($_POST['cont_heading']);
	$content_text=xtc_db_prepare_input($_POST['content_manager']);
	$coID=xtc_db_prepare_input($_POST['coID']);
	$upload_file=xtc_db_prepare_input($_POST['file_upload']);
	$content_status=xtc_db_prepare_input($_POST['status']);
	$content_language=xtc_db_prepare_input($_POST['language']);
	$select_file=xtc_db_prepare_input($_POST['select_file']);
	$file_flag=xtc_db_prepare_input($_POST['file_flag']);
	$parent_check=xtc_db_prepare_input($_POST['parent_check']);
	$parent_id=xtc_db_prepare_input($_POST['parent']);
	$group_id=xtc_db_prepare_input($_POST['content_group']);

// <!-- CONTENT-MANAGER WITH CHILDREN BY RENE JANSEN -->
// WENN KEINE GRUPPEN-ID ÜBERGEBEN WIRD, NEHMEN WIR DEN NÄCHSTEN FREIEN WERT!
	if ( ($group_id == 0) || ($group_id == '') ) {
		$db_query = 'SELECT `content_group` FROM `' . TABLE_CONTENT_MANAGER . '` WHERE `content_group` IS NOT NULL ORDER BY `content_group` DESC LIMIT 1';
		$lastgroup = xtc_db_query($db_query);
    $lastknowngroup = xtc_db_fetch_array($lastgroup);
		$group_id = $lastknowngroup['content_group'] + 1;
	}

// WENN EIN PARENT VORHANDEN IST, AUTOMATISCH DAS AKTUELLE CHILD IN DIE SELBE BOX SPEICHERN (FILE_FLAG)
	if ( ($parent_id != 0) ) {
		$db_query = 'SELECT `file_flag` FROM `' . TABLE_CONTENT_MANAGER . '` WHERE `content_id` = ' . $parent_id;
		$lastgroup = xtc_db_query($db_query);
    $lastknowngroup = xtc_db_fetch_array($lastgroup);
		$file_flag = $lastknowngroup['file_flag'];
	}  // if
// ENDE DER ÄNDERUNG

	$group_ids = $group_ids;
	$sort_order=xtc_db_prepare_input($_POST['sort_order']);
	$content_meta_title = xtc_db_prepare_input($_POST['cont_meta_title']);
	$content_meta_description = xtc_db_prepare_input($_POST['cont_meta_description']);
	$content_meta_keywords = xtc_db_prepare_input($_POST['cont_meta_keywords']);
        
	for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if ($languages[$i]['code']==$content_language) $content_language=$languages[$i]['id'];
	} // for
        
	$error=false; // reset error flag
	if (strlen($content_title) < 1) {
		$error = true;
		$messageStack->add(ERROR_TITLE,'error');
	}  // if

	if ($content_status=='yes'){
		$content_status=1;
	} else{
		$content_status=0;
	}  // if

	if ($parent_check=='yes'){
		$parent_id=$parent_id;
	} else{
		$parent_id='0';
	}  // if
        
	if ($error == false) {
		// file upload
		if ($select_file!='default') $content_file_name=$select_file;
		if ($content_file = &xtc_try_upload('file_upload', DIR_FS_CATALOG.'media/content/')) {
			$content_file_name=$content_file->filename;
		}  // if


// <!-- LINK AS CONTENT - RENE JANSEN - 2008-06-01 - 3 NEW LINES ADDED IN QUERY BELOW -->
		// update data in table 
		$sql_data_array = array(

			'content_typ' => $content_typ,
			'content_url' => $content_url,
			'link_target' => $link_target,

			'languages_id' => $content_language,
			'content_title' => $content_title,
			'content_heading' => $content_header,
			'content_text' => $content_text,
			'content_file' => $content_file_name,
			'content_status' => $content_status,
			'parent_id' => $parent_id,
			'group_ids' => $group_ids,
			'content_group' => $group_id,
			'sort_order' => $sort_order,
			'file_flag' => $file_flag,
			'content_meta_title' => $content_meta_title,
			'content_meta_description' => $content_meta_description,
			'content_meta_keywords' => $content_meta_keywords);

		if ($_GET['id']=='update') {
			xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array, 'update', "content_id = '" . $coID . "'");
		} else {
			xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);
		} // if get id

		xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER));
	} // if error
} // if 
 
if ($_GET['id']=='update_product' or $_GET['id']=='insert_product') {
	// set allowed c.groups
	$group_ids='';
	if(isset($_POST['groups'])) foreach($_POST['groups'] as $b){
		$group_ids .= 'c_'.$b."_group ,";
	}

	$customers_statuses_array=xtc_get_customers_statuses();
	if (strstr($group_ids,'c_all_group')) {
		$group_ids='c_all_group,';
		for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
			$group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
		}
	}
        
	$content_title=xtc_db_prepare_input($_POST['cont_title']);
	$content_link=xtc_db_prepare_input($_POST['cont_link']);
	$content_language=xtc_db_prepare_input($_POST['language']);
	$product=xtc_db_prepare_input($_POST['product']);
	$upload_file=xtc_db_prepare_input($_POST['file_upload']);
	$filename=xtc_db_prepare_input($_POST['file_name']);
	$coID=xtc_db_prepare_input($_POST['coID']);
	$file_comment=xtc_db_prepare_input($_POST['file_comment']);
	$select_file=xtc_db_prepare_input($_POST['select_file']);
	$group_ids = $group_ids;
        
	$error=false; // reset error flag
        
	for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		if ($languages[$i]['code']==$content_language) $content_language=$languages[$i]['id'];
	} // for
        
	if (strlen($content_title) < 1) {
		$error = true;
		$messageStack->add(ERROR_TITLE,'error');
	}  // if

	if ($error == false) {
		/* mkdir() wont work with php in safe_mode
		if  (!is_dir(DIR_FS_CATALOG.'media/products/'.$product.'/')) {
			$old_umask = umask(0);
			xtc_mkdirs(DIR_FS_CATALOG.'media/products/'.$product.'/',0777);
			umask($old_umask);
	        }
		*/        

		if ($select_file=='default') {
			if ($content_file = &xtc_try_upload('file_upload', DIR_FS_CATALOG.'media/products/')) {
				$content_file_name=$content_file->filename;
				$old_filename=$content_file->filename;
				$timestamp=str_replace('.','',microtime());
				$timestamp=str_replace(' ','',$timestamp);
				$content_file_name=$timestamp.strstr($content_file_name,'.');
				$rename_string=DIR_FS_CATALOG.'media/products/'.$content_file_name;
				rename(DIR_FS_CATALOG.'media/products/'.$old_filename,$rename_string);
				copy($rename_string,DIR_FS_CATALOG.'media/products/backup/'.$content_file_name);
			} 
			if ($content_file_name=='') $content_file_name=$filename;
		} else {
			$content_file_name=$select_file;
		}
               
		// update data in table
		// set allowed c.groups
		$group_ids='';
		if(isset($_POST['groups'])) foreach($_POST['groups'] as $b){
			$group_ids .= 'c_'.$b."_group ,";
		}
		$customers_statuses_array=xtc_get_customers_statuses();
		if (strstr($group_ids,'c_all_group')) {
			$group_ids='c_all_group,';
			for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
				$group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
			}
		}
        
		$sql_data_array = array(
			'products_id' => $product,
			'group_ids' => $group_ids, 
			'content_name' => $content_title,
			'content_file' => $content_file_name,
			'content_link' => $content_link,
			'file_comment' => $file_comment,
			'languages_id' => $content_language);
        
		if ($_GET['id']=='update_product') {
			xtc_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array, 'update', "content_id = '" . $coID . "'");
			$content_id = xtc_db_insert_id();
		} else {
			xtc_db_perform(TABLE_PRODUCTS_CONTENT, $sql_data_array);
			$content_id = xtc_db_insert_id();        
		} // if get id
        
		// rename filename
		xtc_redirect(xtc_href_link(FILENAME_CONTENT_MANAGER,'pID='.$product));
	}// if error
}
?>

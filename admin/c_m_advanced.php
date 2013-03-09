<table width="100%" border="0">
									<tr>
										<td>



<?php
if (!$_GET['action']) {
?>
	<div class="pageHeading"><br /><?php echo HEADING_CONTENT; ?><br /></div>
	<div class="main"><?php echo CONTENT_NOTE; ?></div>
	<?php xtc_spaceUsed(DIR_FS_CATALOG.'media/content/'); echo '<div class="main">'.USED_SPACE.xtc_format_filesize($total).'</div>'; ?>

	<?php

// Display Content
  include('c_m_content.php');

} else {

	switch ($_GET['action']) {
	// Diplay Editmask
	case 'new':    
	case 'edit':
		if ($_GET['action']!='new') {

// <!-- LINK AS CONTENT - RENE JANSEN - 2008-06-01 - 3 NEW LINES ADDED IN QUERY BELOW -->

			$content_query=xtc_db_query("SELECT *
				                           FROM 
                                    ".TABLE_CONTENT_MANAGER."
				                           WHERE 
                                    content_id='".(int)$_GET['coID']."'
                                  ");

			$content=xtc_db_fetch_array($content_query);
		}

		$languages_array = array();

		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			if ($languages[$i]['id']==$content['languages_id']) {
				$languages_selected=$languages[$i]['code'];
				$languages_id=$languages[$i]['id'];
			}               
			$languages_array[] = array('id' => $languages[$i]['code'], 'text' => $languages[$i]['name']);
		} // for

		if ($languages_id!='') $query_string='languages_id='.$languages_id.' AND';

// NO WHERE - PARENT_ID=0
// <!-- CONTENT-MANAGER WITH CHILDREN BY RENE JANSEN -->
		$categories_query=xtc_db_query("SELECT
			content_id,
			content_title
			FROM ".TABLE_CONTENT_MANAGER."
			WHERE ".$query_string."
			content_id!='".(int)$_GET['coID']."'");

// NEWLINE HERE
// <!-- CONTENT-MANAGER WITH CHILDREN BY RENE JANSEN -->
		$categories_array[]=array( 'id'=>'0', 'text'=>'');

		while ($categories_data=xtc_db_fetch_array($categories_query)) {
			$categories_array[]=array( 'id'=>$categories_data['content_id'], 'text'=>$categories_data['content_title']);
		}   

		?>
		<br /><br />

		<?php
		if ($_GET['action']!='new') {
			echo xtc_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit&id=update&coID='.$_GET['coID'],'post','enctype="multipart/form-data"').xtc_draw_hidden_field('coID',$_GET['coID']);
		} else {
			echo xtc_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit&id=insert','post','enctype="multipart/form-data"').xtc_draw_hidden_field('coID',$_GET['coID']);
		} ?>


		<table class="main" width="100%" border="0">
      <?php
      if($_POST['lang'] != ''){
				echo xtc_draw_hidden_field('language',$_POST['lang']);      
      }else{
      ?>
			<tr> 
				<td width="10%"><?php echo TEXT_LANGUAGE; ?></td>
				<td width="90%"><?php echo xtc_draw_pull_down_menu('language',$languages_array,$languages_selected); ?></td>
			</tr>
      <?php
      }
			echo xtc_draw_hidden_field('content_group',$content['content_group']);
			
      if($_POST['box'] != ''){
			 echo xtc_draw_hidden_field('file_flag',$_POST['box']);
      }else{
			 $file_flag_sql = xtc_db_query("SELECT file_flag as id, file_flag_name as text FROM " . TABLE_CM_FILE_FLAGS);
        while($file_flag = xtc_db_fetch_array($file_flag_sql)) {
				  $file_flag_array[] = array('id' => $file_flag['id'], 'text' => $file_flag['text']);
			 }      
      ?>
			<tr> 
				<td width="10%"><?php echo TEXT_FILE_FLAG; ?></td>
				<td width="90%"><?php echo xtc_draw_pull_down_menu('file_flag',$file_flag_array,$content['file_flag']); ?></td>
			</tr>      
      <?php
      }
			if($_POST['parent_id'] != ''){
			 echo xtc_draw_hidden_field('parent',$_POST['parent_id']);
			 echo xtc_draw_hidden_field('parent_check','yes');
      }else{
      ?>
			<tr>
				<td width="10%"><?php echo TEXT_PARENT; ?></td>
				<td width="90%"><?php 
					echo xtc_draw_pull_down_menu('parent',$categories_array,$content['parent_id']); 
// <!-- CONTENT-MANAGER WITH CHILDREN BY RENE JANSEN -->
			 echo xtc_draw_hidden_field('parent_check','yes');
				?></td>
			</tr>      
      <?php
      }
      ?>	
			<tr>
				<td width="10%"><?php echo TEXT_SORT_ORDER; ?></td>
				<td width="90%"><?php echo xtc_draw_input_field('sort_order',$content['sort_order'],'size="5"'); ?></td>
			</tr>

			<tr> 
				<td valign="top" width="10%"><?php echo TEXT_STATUS; ?></td>
				<td width="90%">
					<?php if ($content['content_status']=='1') {
					      echo xtc_draw_checkbox_field('status', 'yes',true).' '.TEXT_STATUS_DESCRIPTION;
				      } else {
					      echo xtc_draw_checkbox_field('status', 'yes',false).' '.TEXT_STATUS_DESCRIPTION;
				      }
				      ?><br /><br />
				</td>
			</tr>
<!-- LINK AS CONTENT - RENE JANSEN - 2008-06-01 -->

    <tr>
      <td valign="top" width="10%"><?php echo TEXT_TYP; ?></td>
      <td width="90%">
      
      <?php
      if ($content['content_typ']=='1') {
      echo xtc_draw_checkbox_field('typ', 'yes',true).'</div> '.TEXT_TYP_DESCRIPTION;
      } else {
      echo xtc_draw_checkbox_field('typ', 'yes',false).'</div> '.TEXT_TYP_DESCRIPTION;
      }

      ?><br /><br /></td>
   </tr>
   <tr>
      <td width="15%"><?php echo TEXT_URL; ?></td>
      <td width="85%"><?php echo xtc_draw_input_field('cont_url',$content['content_url'],'size="60"'); ?></td>
   </tr>

   <tr>
      <td width="10%"><?php echo TEXT_LINK_TARGET; ?></td>
      <td width="90%">
        <?php
        $link_target_array[] = array('id' => '', 'text' => 'self');
        $link_target_array[] = array('id' => '_blank', 'text' => '_blank');        
        echo xtc_draw_pull_down_menu('link_target',$link_target_array,$content['link_target']); ?>
      </td>
   </tr>

<!-- END LINK AS CONTENT - RENE JANSEN - 2008-06-01 -->
			<?php
			if (GROUP_CHECK=='true') {
				$customers_statuses_array = xtc_get_customers_statuses();
				$customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
				?>
				<tr>
					<td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
					<td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-right: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-bottom: 1px solid; border-color: #ff0000;" bgcolor="#FFCC33" class="main">

						<?php
						for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
							if (strstr($content['group_ids'],'c_'.$customers_statuses_array[$i]['id'].'_group')) {
								$checked='checked ';
							} else {
								$checked='';
							}
							echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
						}
						?>
					</td>
				</tr>
			<?php
			}
			?>

			<tr>
				<td width="10%"><?php echo TEXT_TITLE; ?></td>
				<td width="90%"><?php echo xtc_draw_input_field('cont_title',$content['content_title'],'size="60"'); ?></td>
			</tr>

			<tr> 
				<td width="10%"><?php echo TEXT_HEADING; ?></td>
				<td width="90%"><?php echo xtc_draw_input_field('cont_heading',$content['content_heading'],'size="60"'); ?></td>
			</tr>
<?php
     if ($content['content_group']!='5'){
?>
			<tr>
				<td colspan="2"><hr><br /></td>
			</tr>

			<tr>
				<td width="10%">Meta Title</td>
				<td width="90%"><?php echo xtc_draw_input_field('cont_meta_title',$content['content_meta_title'],'size="60"'); ?></td>
			</tr>

			<tr> 
				<td width="10%">Meta Description</td>
				<td width="90%"><?php echo xtc_draw_input_field('cont_meta_description',$content['content_meta_description'],'size="60"'); ?></td>
			</tr>

			<tr> 
				<td width="10%">Meta Keywords</td>
				<td width="90%"><?php echo xtc_draw_input_field('cont_meta_keywords',$content['content_meta_keywords'],'size="60"'); ?></td>
			</tr>
<?php
      }
?>
			<tr>
				<td colspan="2"><hr></td>
			</tr>   

			<tr> 
				<td width="10%" valign="top"><?php echo TEXT_UPLOAD_FILE; ?></td>
				<td width="90%"><?php echo xtc_draw_file_field('file_upload').' '.TEXT_UPLOAD_FILE_LOCAL; ?></td>
			</tr> 

			<tr> 
				<td width="10%" valign="top"><?php echo TEXT_CHOOSE_FILE; ?></td>
				<td width="90%">
					<?php
					if ($dir= opendir(DIR_FS_CATALOG.'media/content/')){
						while  (($file = readdir($dir)) !==false) {
							if (is_file( DIR_FS_CATALOG.'media/content/'.$file) and ($file !="index.html")){
								$files[]=array( 'id' => $file, 'text' => $file);
							}//if
						} // while
						closedir($dir);
					}
					// set default value in dropdown!
					if ($content['content_file']=='') {
						$default_array[]=array('id' => 'default','text' => TEXT_SELECT);
						$default_value='default';
						if (count($files) == 0) {
							$files = $default_array;
						} else {
							$files=array_merge($default_array,$files);
						}
					} else {
						$default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
						$default_value=$content['content_file'];
						if (count($files) == 0) {
							$files = $default_array;
						} else {
							$files=array_merge($default_array,$files);
						}
					}

					echo '<br />'.TEXT_CHOOSE_FILE_SERVER.'<br />';
					echo xtc_draw_pull_down_menu('select_file',$files,$default_value);
					if ($content['content_file']!='') {
						echo TEXT_CURRENT_FILE.' <b>'.$content['content_file'].'</b><br />';
					}

					?>
				</td>
			</tr> 

			<tr> 
				<td width="10%" valign="top"></td>
				<td colspan="90%" valign="top"><br /><?php echo TEXT_FILE_DESCRIPTION; ?></td>
			</tr> 

			<tr> 
				<td width="10%" valign="top"><?php echo TEXT_CONTENT; ?></td>
				<td width="90%">
					<?php
					echo xtc_draw_textarea_field('content_manager','','100%','35',$content['content_text']);
					?>
				</td>
			</tr>

			<tr>
				<td colspan="2" align="right" class="main"><?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?><a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a></td>
			</tr>
		</table>

		</form>
		<?php
		break;
 
	case 'edit_products_content':
	case 'new_products_content':
 		if ($_GET['action']=='edit_products_content') {
			$content_query=xtc_db_query("SELECT
				content_id,
				products_id,
				group_ids,
				content_name,
				content_file,
				content_link,
				languages_id,
				file_comment,
				content_read
				FROM ".TABLE_PRODUCTS_CONTENT."
				WHERE content_id='".(int)$_GET['coID']."'");

			$content=xtc_db_fetch_array($content_query);
		}
 
		// get products names.
		$products_query=xtc_db_query("SELECT
			products_id,
			products_name
			FROM ".TABLE_PRODUCTS_DESCRIPTION."
			WHERE language_id='".(int)$_SESSION['languages_id']."'");
		$products_array=array();

		while ($products_data=xtc_db_fetch_array($products_query)) {
 			$products_array[]=array( 'id' => $products_data['products_id'], 'text' => $products_data['products_name']);
		}

		// get languages
		$languages_array = array();

		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			if ($languages[$i]['id']==$content['languages_id']) {
				$languages_selected=$languages[$i]['code'];
				$languages_id=$languages[$i]['id'];
			}               
			$languages_array[] = array('id' => $languages[$i]['code'], 'text' => $languages[$i]['name']);
		} // for
 
		// get used content files
		$content_files_query=xtc_db_query("SELECT DISTINCT
			content_name,
			content_file
			FROM ".TABLE_PRODUCTS_CONTENT."
			WHERE content_file!=''");
		$content_files=array();

		while ($content_files_data=xtc_db_fetch_array($content_files_query)) {
			$content_files[]=array( 'id' => $content_files_data['content_file'], 'text' => $content_files_data['content_name']);
		}

		// add default value to array
		$default_array[]=array('id' => 'default','text' => TEXT_SELECT);
		$default_value='default';
		$content_files=array_merge($default_array,$content_files);
		// mask for product content
 
		if ($_GET['action']!='new_products_content') {
			echo xtc_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit_products_content&id=update_product&coID='.$_GET['coID'],'post','enctype="multipart/form-data"').xtc_draw_hidden_field('coID',$_GET['coID']); 
		} else {
			echo xtc_draw_form('edit_content',FILENAME_CONTENT_MANAGER,'action=edit_products_content&id=insert_product','post','enctype="multipart/form-data"'); 
		}

		?>
		<div class="main"><?php echo TEXT_CONTENT_DESCRIPTION; ?></div>
		<table class="main" width="100%" border="0">
			<tr>
				<td width="10%"><?php echo TEXT_PRODUCT; ?></td>
				<td width="90%"><?php echo xtc_draw_pull_down_menu('product',$products_array,$content['products_id']); ?></td>
			</tr>
			<tr> 
				<td width="10%"><?php echo TEXT_LANGUAGE; ?></td>
				<td width="90%"><?php echo xtc_draw_pull_down_menu('language',$languages_array,$languages_selected); ?></td>
			</tr>

			<?php
			if (GROUP_CHECK=='true') {
				$customers_statuses_array = xtc_get_customers_statuses();
				$customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
			?>
				<tr>
					<td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
					<td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-right: 1px solid; border-color: #ff0000;" style="border-top: 1px solid; border-bottom: 1px solid; border-color: #ff0000;" bgcolor="#FFCC33" class="main">

						<?php
						for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
							if (strstr($content['group_ids'],'c_'.$customers_statuses_array[$i]['id'].'_group')) {
								$checked='checked ';
							} else {
								$checked='';
							}
							echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>

			<tr>
				<td width="10%"><?php echo TEXT_TITLE_FILE; ?></td>
				<td width="90%"><?php echo xtc_draw_input_field('cont_title',$content['content_name'],'size="60"'); ?></td>
			</tr>

			<tr> 
				<td width="10%"><?php echo TEXT_LINK; ?></td>
				<td width="90%"><?php  echo xtc_draw_input_field('cont_link',$content['content_link'],'size="60"'); ?></td>
			</tr>

			<tr>
				<td width="10%" valign="top"><?php echo TEXT_FILE_DESC; ?></td>
				<td width="90%"><?php echo xtc_draw_textarea_field('file_comment','','100','30',$content['file_comment']); ?></td>
			</tr>

			<tr>
				<td width="10%"><?php echo TEXT_CHOOSE_FILE; ?></td>
				<td width="90%"><?php echo xtc_draw_pull_down_menu('select_file',$content_files,$default_value); ?><?php echo ' '.TEXT_CHOOSE_FILE_DESC; ?></td>
			</tr>

			<tr> 
				<td width="10%" valign="top"><?php echo TEXT_UPLOAD_FILE; ?></td>
				<td width="90%"><?php echo xtc_draw_file_field('file_upload').' '.TEXT_UPLOAD_FILE_LOCAL; ?></td>
			</tr> 

			<?php
				if ($content['content_file']!='') {
					?>
					<tr> 
						<td width="10%"><?php echo TEXT_FILENAME; ?></td>
						<td width="90%" valign="top"><?php echo xtc_draw_hidden_field('file_name',$content['content_file']).xtc_image(DIR_WS_CATALOG.'admin/images/icons/icon_'.str_replace('.','',strstr($content['content_file'],'.')).'.gif').$content['content_file']; ?></td>
					</tr>
					<?php
				}
			?>

			<tr>
				<td colspan="2" align="right" class="main"><?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?><a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER); ?>"><?php echo BUTTON_BACK; ?></a></td>
			</tr>

			</form>
		</table>
 
		<?php
 		break;
	}
}

	if (!$_GET['action']) {
		// products content
		// load products_ids into array
 
		$products_id_query=xtc_db_query("SELECT DISTINCT
			pc.products_id,
			pd.products_name
			FROM ".TABLE_PRODUCTS_CONTENT." pc, ".TABLE_PRODUCTS_DESCRIPTION." pd
			WHERE pd.products_id=pc.products_id and pd.language_id='".(int)$_SESSION['languages_id']."'");
 
		$products_ids=array();

		while ($products_id_data=xtc_db_fetch_array($products_id_query)) {
			$products_ids[]=array( 'id'=>$products_id_data['products_id'], 'name'=>$products_id_data['products_name']);
		} // while
        
		?>
		<div class="pageHeading"><br /><?php echo HEADING_PRODUCTS_CONTENT; ?><br /></div>

		<?php
		xtc_spaceUsed(DIR_FS_CATALOG.'media/products/');
		echo '<div class="main">'.USED_SPACE.xtc_format_filesize($total).'</div><br />';
		?>      

		<table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tr class="dataTableHeadingRow">
				<td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_PRODUCTS_ID; ?></td>
				<td class="dataTableHeadingContent" width="95%" align="left"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
			</tr>

			<?php
			for ($i=0,$n=sizeof($products_ids); $i<$n; $i++) {
				echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";
				?>
					<td class="dataTableContent_products" align="left"><?php echo $products_ids[$i]['id']; ?></td>
					<td class="dataTableContent_products" align="left"><b><?php echo xtc_image(DIR_WS_CATALOG.'images/icons/arrow.gif'); ?><a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER,'pID='.$products_ids[$i]['id']);?>"><?php echo $products_ids[$i]['name']; ?></a></b></td>
				</tr>
				<?php

				if ($_GET['pID']) {
					// display content elements
					$content_query=xtc_db_query("SELECT
                                        content_id,	content_name,
                                        content_file,	content_link,
                                        languages_id,	file_comment,
                                        content_read
                                        FROM ".TABLE_PRODUCTS_CONTENT."
                                        WHERE products_id='".$_GET['pID']."' order by content_name");

					$content_array='';

					while ($content_data=xtc_db_fetch_array($content_query)) {
						$content_array[]=array(
							'id'=> $content_data['content_id'],
							'name'=> $content_data['content_name'],
							'file'=> $content_data['content_file'],
							'link'=> $content_data['content_link'],
							'comment'=> $content_data['file_comment'],
							'languages_id'=> $content_data['languages_id'],
							'read'=> $content_data['content_read']);
					} // while content data

					if ($_GET['pID']==$products_ids[$i]['id']){
						?>
						<tr>
							<td class="dataTableContent" align="left"></td>
							<td class="dataTableContent" align="left">
								<table border="0" width="100%" cellspacing="0" cellpadding="2">
									<tr class="dataTableHeadingRow">
										<td class="dataTableHeadingContent" nowrap width="2%" ><?php echo TABLE_HEADING_PRODUCTS_CONTENT_ID; ?></td>
										<td class="dataTableHeadingContent" nowrap width="2%" >&nbsp;</td>
										<td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_LANGUAGE; ?></td>
										<td class="dataTableHeadingContent" nowrap width="15%" ><?php echo TABLE_HEADING_CONTENT_NAME; ?></td>
										<td class="dataTableHeadingContent" nowrap width="30%" ><?php echo TABLE_HEADING_CONTENT_FILE; ?></td>
										<td class="dataTableHeadingContent" nowrap width="1%" ><?php echo TABLE_HEADING_CONTENT_FILESIZE; ?></td>
										<td class="dataTableHeadingContent" nowrap align="middle" width="20%" ><?php echo TABLE_HEADING_CONTENT_LINK; ?></td>
										<td class="dataTableHeadingContent" nowrap width="5%" ><?php echo TABLE_HEADING_CONTENT_HITS; ?></td>
										<td class="dataTableHeadingContent" nowrap width="20%" ><?php echo TABLE_HEADING_CONTENT_ACTION; ?></td>
									</tr>
                  <tr>  

									<?php
 
									for ($ii=0,$nn=sizeof($content_array); $ii<$nn; $ii++) {
										echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";
											?>
											<td class="dataTableContent" align="left"><?php echo  $content_array[$ii]['id']; ?> </td>
											<td class="dataTableContent" align="left"><?php
 												if ($content_array[$ii]['file']!='') {
													echo xtc_image(DIR_WS_CATALOG.'admin/images/icons/icon_'.str_replace('.','',strstr($content_array[$ii]['file'],'.')).'.gif');
												} else {
													echo xtc_image(DIR_WS_CATALOG.'admin/images/icons/icon_link.gif');
												}

												for ($xx=0,$zz=sizeof($languages); $xx<$zz;$xx++){
													if ($languages[$xx]['id']==$content_array[$ii]['languages_id']) {
														$lang_dir=$languages[$xx]['directory'];	
														break;
													}	
												}
												?>
											</td>

											<td class="dataTableContent" align="left"><?php echo xtc_image(DIR_WS_CATALOG.'lang/'.$lang_dir.'/admin/images/icon.gif'); ?></td>
											<td class="dataTableContent" align="left"><?php echo $content_array[$ii]['name']; ?></td>
											<td class="dataTableContent" align="left"><?php echo $content_array[$ii]['file']; ?></td>
											<td class="dataTableContent" align="left"><?php echo xtc_filesize($content_array[$ii]['file']); ?></td>
											<td class="dataTableContent" align="left" align="middle"><?php
												if ($content_array[$ii]['link']!='') {
													echo '<a href="'.$content_array[$ii]['link'].'" target="new">'.$content_array[$ii]['link'].'</a>';
												} 
												?>&nbsp;
											</td>
											<td class="dataTableContent" align="left"><?php echo $content_array[$ii]['read']; ?></td>
											<td class="dataTableContent" align="left">
 												<a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER,'special=delete_product&coID='.$content_array[$ii]['id']).'&pID='.$products_ids[$i]['id']; ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
												<?php echo xtc_image(DIR_WS_ICONS.'delete.gif','Delete','','','style="cursor:pointer" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>&nbsp;&nbsp;'; ?>
												<a href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER,'action=edit_products_content&coID='.$content_array[$ii]['id']); ?>">
												<?php echo xtc_image(DIR_WS_ICONS.'icon_edit.gif','Edit','','','style="cursor:pointer"').'  '.TEXT_EDIT.'</a>'; ?>

												<?php
												// display preview button if filetype 
												// .gif,.jpg,.png,.html,.htm,.txt,.tif,.bmp
												if (	preg_match('/.gif/i',$content_array[$ii]['file']) or
													preg_match('/.jpg/i',$content_array[$ii]['file']) or
													preg_match('/.png/i',$content_array[$ii]['file']) or
													preg_match('/.html/i',$content_array[$ii]['file']) or
													preg_match('/.htm/i',$content_array[$ii]['file']) or
													preg_match('/.txt/i',$content_array[$ii]['file']) or
													preg_match('/.bmp/i',$content_array[$ii]['file']) ) {
														?>
														<a style="cursor:pointer" onClick="javascript:window.open('<?php echo xtc_href_link(FILENAME_CONTENT_PREVIEW,'pID=media&coID='.$content_array[$ii]['id']); ?>', 'popup', 'toolbar=0, width=640, height=600')">
														<?php echo xtc_image(DIR_WS_ICONS.'preview.gif','Preview','','',' style="cursor:pointer"').'&nbsp;&nbsp;'.TEXT_PREVIEW.'</a>'; ?> 
														<?php
												}
												?> 
 											</td>
										</tr>
										<?php 
									} // for content_array ?>
								</table>
							</td>
						</tr>
					<?php }
				}
			} ?> 
		</table>
		<a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_CONTENT_MANAGER,'action=new_products_content'); ?>"><?php echo BUTTON_NEW_CONTENT; ?></a>                 

	<?php
	} // if !$_GET['action']
	?>       
        
</td>
</tr>
</table>

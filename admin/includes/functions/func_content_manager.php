<?php
/* --------------------------------------------------------------
   $Id: func_content_manager.php 2009-08-16 kunigunde $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2008 Self-Commerce

   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

function content($lang, $box, $parent)
{
    $query_content = "SELECT *
    				FROM ".TABLE_CONTENT_MANAGER."
    				WHERE languages_id = '".$lang."'
    					AND file_flag = '".$box."'
    					AND parent_id = '".$parent."'
    				ORDER BY sort_order";
    $sql_content = xtc_db_query($query_content);
    $content .= '<ul class="treeview">';
    while($content_data = xtc_db_fetch_array($sql_content))
    {
    	if($content_data['content_file'] == '')
    	{
    		$cont_file = '';
    	}else
    	{
    		$cont_file = xtc_image(DIR_WS_ICONS . 'file.gif', $content_data['content_file']);
    	}
    	if($content_data['content_typ'] == '1')
    	{
    		$cont_link = xtc_image(DIR_WS_ICONS . 'icon_link.gif', $content_data['content_url']);
    	}else
    	{
    		$cont_link = '';
    	}
    	if ($content_data['content_status']==0)
    	{
    		$status = xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', TEXT_NO);
    	}else
    	{
    		$status = xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', TEXT_YES);
    	}
    	if ($content_data['content_delete']=='1')
    	{
    		$delete = '<a href="'
    				.xtc_href_link(FILENAME_CONTENT_MANAGER,'special=delete&coID='.$content_data['content_id']).'" onClick="return confirm(\''.$content_data['content_title'].'\n\n'. CONFIRM_DELETE.'\')">'
    				.xtc_image(DIR_WS_ICONS.'delete.gif','Delete','','','style="cursor:pointer"').'</a> ';
    	}else
    	{
    		$delete = xtc_image(DIR_WS_ICONS . 'locked.gif');
    	}
    	$edit = '<a href="'
    				.xtc_href_link(FILENAME_CONTENT_MANAGER, 'action=edit&coID='.$content_data['content_id']).'">'
    				.xtc_image(DIR_WS_ICONS.'icon_edit.gif','Edit','','','style="cursor:pointer"').'</a>';
    	$preview = '<a style="cursor:pointer" onClick="javascript:window.open(\''
    				.xtc_href_link(FILENAME_CONTENT_PREVIEW,'coID='.$content_data['content_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')">'
    				.xtc_image(DIR_WS_ICONS.'preview.gif','Preview','','','style="cursor:pointer"').'</a>';
    	// symbole der gruppen ausgeben, für welche der content sichtbar ist
    	if (GROUP_CHECK == 'true')
    	{
    		$group_check = '';
    		$grp_id = '';
    		$groups = explode(',', $content_data['group_ids']);// gruppen auslesen
    		$count_groups = count($groups)-1;
    		for ($i = 0; $i <= $count_groups; $i++)
    		{
    			$grp_id = substr($groups[$i], -7, 1);
    			if(is_numeric($grp_id))
    			{
    				$query_cs_image = "SELECT customers_status_image, customers_status_name
    									FROM ".TABLE_CUSTOMERS_STATUS."
    									WHERE customers_status_id = '".$grp_id."'
    										AND language_id = '".$lang."'";
    				$sql_cs_image = xtc_db_query($query_cs_image);
    				$cs_image_data = xtc_db_fetch_array($sql_cs_image);
    				$group_check .= xtc_image(DIR_WS_ICONS . $cs_image_data['customers_status_image'], $cs_image_data['customers_status_name'],'16','16');
    			}
    		}
    	}else
    	{
    		$group_check = '';
    	}
    	$formular = xtc_draw_form('new_sub_content',FILENAME_CONTENT_MANAGER,'action=new','post','enctype="multipart/form-data"')
    				.xtc_draw_hidden_field('lang',$lang)
    				.xtc_draw_hidden_field('box',$content_data['file_flag'])
    				.xtc_draw_hidden_field('parent_id',$content_data['content_id'])
    				.'<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_NEW_SUB_CONTENT . '"/></form>';
    	// end formular
    	$more_info = ' '.$cont_file.' '.$cont_link.' '.$status.' '.$delete.' '.$edit.' '.$preview.' '.$group_check.' '.$formular;
    	$content .= '<li>';
    	$content .= $content_data['content_title'].' '.$more_info.'';
    	$content .= content($lang, $box, $content_data['content_id']).'</li>';
    }
    $content .= '</ul>';
    // string von leeren ul befreien (wird erstellt wenn keine weiteren subcontents vorhanden)
    $content = str_replace('<ul class="treeview"></ul>', '', $content);

    return $content;
}

function content_per_box($lang_id)
{
	// anzahl boxen welche verfügbar sind inkl. deren namen $boxes[]
	$query_boxes = "SELECT * FROM ". TABLE_CM_FILE_FLAGS ." order by file_flag ASC";
	$sql_boxes = xtc_db_query($query_boxes);
	while($boxes_data = xtc_db_fetch_array($sql_boxes)){ // Boxnamen ausgeben
		$boxes[] = $boxes_data['file_flag_name'];
	}
	// zählen
	$count_boxes = count($boxes);
	$flag_id=0;
	while($flag_id<$count_boxes)
	{
	    $box .= '<div class="contentsborder"><div class="contentsTopics">'.$boxes[$flag_id].':</div>';// boxname
	    $box .= '<div class="contentsBG">'.content($lang_id,$flag_id, 0);
	    $box .= xtc_draw_form('new_content',FILENAME_CONTENT_MANAGER,'action=new','post','enctype="multipart/form-data"')
        	    .xtc_draw_hidden_field('lang',$lang_id)
        	    .xtc_draw_hidden_field('box',$flag_id)
        	    .xtc_draw_hidden_field('parent_id','0')
        	    .'<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_NEW_CONTENT . '"/></form>';
        // end formular
        $box .= '</div></div>';
        $flag_id++;
    }
    return $box;
}
?>
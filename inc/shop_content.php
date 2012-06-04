<?php
/* --------------------------------------------------------------
   $Id: shop_content.php 2009-08-16 kunigunde $   

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2008 Self-Commerce

   Released under the GNU General Public License

   Thx an SNCJansen http://spider-nc.de for function get_id_from_group()   
   --------------------------------------------------------------*/
function shop_content($lang, $box, $parent, $class, $show_sub = 'true'){
				if (GROUP_CHECK == 'true') {
					$group_check 	= "and group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
				}
    $query_content = "SELECT * FROM 
                                ".TABLE_CONTENT_MANAGER." 
                              WHERE 
                                languages_id = '".$lang."' 
                              AND 
                                file_flag = '".$box."'
                              AND
                                parent_id = '".$parent."'
                              AND
                                content_status ='1'
                              $group_check
                              ORDER BY
                                sort_order 
                              ";
    $sql_content = xtc_db_query($query_content);
    $content_string .= '<ul class="'.$class.'">';
    while($content_data = xtc_db_fetch_array($sql_content)){
		  if ($content_data['content_typ'] == 1) {// content ist als link markiert
        $content_string .= '<li><a href="' . $content_data['content_url'] . '" target="'.$content_data['link_target'].'">' . $content_data['content_title'] . '</a></li>';    
      }else{// normaler shop content
        if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') $SEF_parameter = '&content='.xtc_cleanName($content_data['content_title']);
        $content_string .= '<li> <a href="'.xtc_href_link(FILENAME_CONTENT, 'coID='.$content_data['content_group'].$SEF_parameter).'">'.$content_data['content_title'].'</a>';
      }
      if($show_sub == 'true'){
        $content_string .= shop_content($lang, $box, $content_data['content_id'], $class).'</li>';      
      }else{
        $content_string .= '</li>';
      }
    }
    $content_string .= '</ul>';  
    // string von leeren ul befreien (wird erstellt, wenn keine weiteren untercontents enthalten sind)
    $content_string = str_replace('<ul class="'.$class.'"></ul>', '', $content_string);
  return $content_string;
}

// ------------------------------------------------------------------------------------------------------------------------
// DER AKTUELLE CONTENT WIRD IN DER URL NUR DURCH DIE CONTENT_GRUPPE BESTIMMT, WIR BRAUCHEN ABER DIE PASSENDE ID DAZU!
// ------------------------------------------------------------------------------------------------------------------------
	function get_id_from_group ($content_group, $language_id) {
		$shop_content_query = xtc_db_query("SELECT content_id
			FROM ".TABLE_CONTENT_MANAGER." WHERE content_group='". (int) $content_group ."' AND languages_id='".(int) $language_id."'");
		$shop_content_data = xtc_db_fetch_array($shop_content_query);

		return $shop_content_data['content_id'];
	}

function get_flag_from_id($content_id){
				$query_flag = "SELECT file_flag FROM " . TABLE_CONTENT_MANAGER . " WHERE content_id = " . $content_id;
				$sql_flag = xtc_db_query($query_flag);
        $flag_data = xtc_db_fetch_array($sql_flag);
				
        return $flag_data['file_flag'];
}
        	
?>

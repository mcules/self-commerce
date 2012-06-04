<?php
/* -----------------------------------------------------------------------------------------
   $Id: infobox.php 1262 2005-09-30 10:00:32Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com 
   (c) 2003	 nextcommerce (infobox.php,v 1.7 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Loginbox V1.0        	Aubrey Kilian <aubrey@mycon.co.za>

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$box_smarty = new smarty;
$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');

if (stristr($_SERVER['SCRIPT_NAME'], FILENAME_CONTENT) || stristr($_SERVER['SCRIPT_NAME'], 'index.php')) {

  if (stristr($_SERVER['SCRIPT_NAME'], FILENAME_CONTENT)) {
	  $content_id = get_id_from_group ( $_GET['coID'], (int) $_SESSION['languages_id'] );
    $flag = get_flag_from_id($content_id);
  }else{//default content 
	  $content_id = get_id_from_group ( 5, (int) $_SESSION['languages_id'] );
    $flag = get_flag_from_id($content_id); 
  }

  if(MODULE_CONTENT_MANAGER_CHILDREN_CENTERBOX == 'children'){$show_sub = '';}else{$show_sub = 'true';}
                 /* shop_content(lang, box_flag, parent, css_id, $show_sub) 
                    $show_sub is optional 
                    when you will see sub content let it empty, 
                    write 0 when subchilds not visible
                 */
  $centerboxcontent .= shop_content($_SESSION['languages_id'], $flag, $content_id, 'content_middle', $show_sub);  
}


    $box_smarty->assign('BOX_CONTENT', $centerboxcontent);
    $box_smarty->assign('language', $_SESSION['language']);

    $box_smarty->caching = 0;
    $box_centerbox= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_centerbox.html');
    
    $smarty->assign('box_CENTERBOX',$box_centerbox);

?>

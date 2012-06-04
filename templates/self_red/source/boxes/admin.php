<?php
/* -----------------------------------------------------------------------------------------
   $Id$   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com 
   (c) 2003	 nextcommerce (admin.php,v 1.12 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// reset var
$box_smarty = new smarty;
$box_content='';
$flag='';
$box_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_image_button.inc.php');

  $orders_contents = '';
  
  $orders_status_validating = xtc_db_num_rows(xtc_db_query("select orders_status from " . TABLE_ORDERS ." where orders_status ='0'"));
  $orders_contents .='<a href="' . xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=0', 'SSL') . '">' . TEXT_VALIDATING . '</a>: ' . $orders_status_validating . '<br />'; 
 
  
  $orders_status_query = xtc_db_query("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "'");
  while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
    $orders_pending_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . $orders_status['orders_status_id'] . "'");
    $orders_pending = xtc_db_fetch_array($orders_pending_query);
    $orders_contents .= '<a href="' . xtc_href_link_admin(FILENAME_ORDERS, 'selected_box=customers&status=' . $orders_status['orders_status_id'], 'SSL') . '">' . $orders_status['orders_status_name'] . '</a>: ' . $orders_pending['count'] . '<br />';
  }
  $orders_contents = substr($orders_contents, 0, -6);

  $customers_query = xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS);
  $customers = xtc_db_fetch_array($customers_query);
  $products_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'");
  $products = xtc_db_fetch_array($products_query);
  $reviews_query = xtc_db_query("select count(*) as count from " . TABLE_REVIEWS);
  $reviews = xtc_db_fetch_array($reviews_query);
  $admin_image = '<a href="' . xtc_href_link_admin(FILENAME_START,'', 'SSL').'">'.xtc_image_button('button_admin.gif', IMAGE_BUTTON_ADMIN).'</a>';
   if ($product->isProduct()) {
    $admin_link='<a href="' . xtc_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $product->data['products_id']) . '&action=new_product' . '" onclick="window.open(this.href); return false;">' . xtc_image_button('edit_product.gif', IMAGE_BUTTON_PRODUCT_EDIT) . '</a>';
		$admin_attributes = //"<br />\n".
		'<br /><form action="admin/new_attributes.php" name="edit_attributes" method="post">'."\n".
		'<input type="hidden" name="action" value="edit" />'."\n".
		'<input type="hidden" name="current_product_id" value="'.$product->data['products_id'].'" />'."\n".
		'<input type="hidden" name="cpath" value="'.$cPath.'" />'."\n".
		'<input type="submit" class="button" value="Attribute editieren" />'."\n".
		'</form>';

		$admin_cross_selling = //"<br />\n".
    '<br /><a href="' . xtc_href_link_admin(FILENAME_EDIT_PRODUCTS, 'current_product_id=' . $product->data['products_id']) . '&action=edit_crossselling&cpath='.$cPath . '" onclick="window.open(this.href); return false;">' . xtc_image_button('edit_cross_sell.gif', IMAGE_BUTTON_CROSS_EDIT) . '</a>';
		
    $special_query = "select specials_id from ".TABLE_SPECIALS." where products_id = '".$product->data['products_id']."' and status=1";
		$special_query = xtDBquery($special_query);
		$special = xtc_db_fetch_array($special_query);
    if($special){
    $admin_specials = //"<br />\n".
    '<br /><a href="' . xtc_href_link_admin('admin/'.FILENAME_SPECIALS, 'sID=' . $special['specials_id']) . '&action=edit' . '" onclick="window.open(this.href); return false;">' . xtc_image_button('edit_special.gif', IMAGE_BUTTON_SPECIAL_EDIT) . '</a>';
    } 
  }
// -----------------------------------------------------------------------------------
	
	if(basename($_SERVER[SCRIPT_NAME])=='index.php' && isset($_GET['cat'])) {

		global $current_category_id;
		$admin_category = //"<br />\n".
    '<a href="' . xtc_href_link_admin(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath) . '&action=edit_category&cID='.$current_category_id . '" onclick="window.open(this.href); return false;">' . xtc_image_button('edit_categorie.gif', IMAGE_BUTTON_CATEGORIE_EDIT) . '</a>';     
	}
	
// -----------------------------------------------------------------------------------

	if(basename($_SERVER[SCRIPT_NAME])=='shop_content.php' && isset($_GET['coID'])) {
		
		$dbQuery = xtDBquery("
			SELECT 	content_id   
		 	FROM 	".TABLE_CONTENT_MANAGER."
		 	WHERE 	content_group = '".intval($_GET['coID'])."'
		 	AND 	languages_id='".(int)$_SESSION['languages_id']."' "
		);
		
		$dbQuery = xtc_db_fetch_array($dbQuery);

		if(!empty($dbQuery)) {
	
		$admin_content = //"<br />\n".
    '<a href="' . xtc_href_link_admin('admin/content_manager.php', 'coID=' . intval($dbQuery['content_id'])) . '&action=edit' . '" onclick="window.open(this.href); return false;">' . xtc_image_button('edit_content.gif', IMAGE_BUTTON_CONTENT_EDIT) . '</a>';     
		}

	}
// -----------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------
// 	BoxContent zusammensetzen
// -----------------------------------------------------------------------------------
	$box_content = '<b>'.BOX_TITLE_STATISTICS."</b><br />\n".$orders_contents."<br />\n".
					BOX_ENTRY_CUSTOMERS.' '.$customers['count']."<br />\n".
					BOX_ENTRY_PRODUCTS.' '.$products['count']."<br />\n".
					BOX_ENTRY_REVIEWS.' '.$reviews['count']."<br />\n".
					$admin_image."<br />\n".$admin_link.
					$admin_cross_selling.
					$admin_specials.
					$admin_category.
					$admin_attributes.					
					$admin_content;
// -----------------------------------------------------------------------------------

    if ($flag==true) define('SEARCH_ENGINE_FRIENDLY_URLS',true);
    $box_smarty->assign('BOX_CONTENT', $box_content);

    $box_smarty->caching = 0;
    $box_smarty->assign('language', $_SESSION['language']);
    $box_admin= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_admin.html');
    $smarty->assign('box_ADMIN',$box_admin);

?>

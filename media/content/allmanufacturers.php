<?php

/* -----------------------------------------------------------------------------------------
   $Id$
   Copyright (c) 2004 IT eSolutions
   -----------------------------------------------------------------------------------------
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com
   (c) 2003	 nextcommerce; www.nextcommerce.org
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
$module_smarty = new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  
	$manufacturers_query = "select distinct m.manufacturers_id, m.manufacturers_name, m.manufacturers_image from ".TABLE_MANUFACTURERS." as m, ".TABLE_PRODUCTS." as p where m.manufacturers_id=p.manufacturers_id order by m.manufacturers_name";
	//db Cache
   $manufacturers_query = xtDBquery($manufacturers_query);
   $module_content = array();
   while ($manufacturers = xtc_db_fetch_array($manufacturers_query,true)) {
      $module_content[]=array(
                                'ID'		=> $manufacturers['manufacturers_id'],
                            	'MAN_NAME'  => $manufacturers['manufacturers_name'],
                            	'MAN_IMAGE' => DIR_WS_IMAGES . $manufacturers['manufacturers_image'],
                            	'MAN_LINK'  => DIR_WS_CATALOG . 'index.php?manufacturers_id=' . $manufacturers['manufacturers_id']);
  }
  // if there's sth -> assign it
  if (sizeof($module_content)>=1)
  {
  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('module_content',$module_content);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  echo $module_smarty->fetch(CURRENT_TEMPLATE.'/module/allmanufacturers.html');
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $GET['cPath'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
  echo $module_smarty->fetch(CURRENT_TEMPLATE.'/module/allmanufacturers.html',$cache_id);
  }
  }
?>

<?php
/* --------------------------------------------------------------
   $Id: xtc_count_modules.inc.php 17 2012-06-04 20:33:29Z deisold $
   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de
   Copyright (c) 2012 Self-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003		 nextcommerce (xtc_count_modules.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2005 	 XT-Commerce (xtc_count_modules.inc.php, 1235 2005/04/29); www.xt-commerce.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
function xtc_count_modules($modules = '') {
    $count = 0;

    if (empty($modules)) return $count;

    $modules_array = explode(';', $modules);

    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {
      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

      if (is_object($GLOBALS[$class])) {
        if ($GLOBALS[$class]->enabled) {
          $count++;
        }
      }
    }

    return $count;
  }
 ?>
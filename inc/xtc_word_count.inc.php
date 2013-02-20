<?php
/* --------------------------------------------------------------
   $Id: xtc_word_count.inc.php 17 2012-06-04 20:33:29Z deisold $
   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de
   Copyright (c) 2012 Self-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 	 nextcommerce (xtc_word_count.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2005      XT-Commerce (xtc_word_count.inc.php, 1235 2005/04/29); www.xt-commerce.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  // Get the number of times a word/character is present in a string
  function xtc_word_count($string, $needle) {
    $temp_array = explode($needle, $string);

    return sizeof($temp_array);
  }
?>
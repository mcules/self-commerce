<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_recalculate_price.inc.php 17 2012-06-04 20:33:29Z deisold $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   by Mario Zanier for XTcommerce
   
   based on:
   (c) 2003	 nextcommerce (xtc_recalculate_price.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
function xtc_recalculate_price($price, $discount) {
	$price=-100*$price/($discount-100)/100*$discount;
	return $price;
}
?>
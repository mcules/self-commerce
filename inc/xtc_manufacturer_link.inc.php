<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce

 
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_manufacturer_link($mID,$mName='') {
		$mName = xtc_cleanName($mName);
		$link = 'manu=m'.$mID.'_'.$mName.'.html';
		return $link;
}
?>
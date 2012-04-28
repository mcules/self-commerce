<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


 function xtc_cleanName($name) {
     $replace_param='/[^a-zA-Z0-9]/';
     $name=preg_replace($replace_param,'-',$name);    
     return $name;
 }

?>

<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_db_select_db.inc.php 899 2005-04-29 02:40:57Z hhgag $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2015 Self-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001	The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003	osCommerce(database.php,v 1.2 2002/03/02); www.oscommerce.com
   (c) 2003-2008	nextcommerce (xtc_db_select_db.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2008			Self-Commerce (xtc_db_select_db.inc.php) www.self-commerce.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function xtc_db_select_db($database) {
	global $$link;

	return mysql_select_db($database);
}
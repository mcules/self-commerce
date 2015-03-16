<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_db_query_installer.inc.php 899 2005-04-29 02:40:57Z hhgag $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2015 Self-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001	The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003	osCommerce(database.php,v 1.2 2002/03/02); www.oscommerce.com
   (c) 2003-2008	nextcommerce (xtc_db_query_installer.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2008			Self-Commerce (xtc_db_query_installer.inc.php) www.self-commerce.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
include_once(DIR_FS_INC . 'xtc_db_error.inc.php');

function xtc_db_query_installer($query, $link = 'db_link') {
	global $$link;

	return mysql_query($query) or xtc_db_error($query, mysql_errno(), mysql_error());
}
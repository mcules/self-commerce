<?php
/* --------------------------------------------------------------
   $Id: application.php 1119 2005-07-25 22:19:50Z novalis $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de

   Copyright (c) 2015 Self-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001	The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003	osCommerce(application.php,v 1.4 2002/11/29); www.oscommerce.com
   (c) 2003-2008	nextcommerce (application.php,v 1.16 2003/08/13); www.nextcommerce.org
   (c) 2008			Self-Commerce (application.php) www.self-commerce.de

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// Some FileSystem Directories
$Pfad = $_SERVER['DOCUMENT_ROOT'];
foreach (explode("/", $_SERVER['SCRIPT_NAME']) as $Var) {
	if ($Var == 'self_installer') { break; }
	else { $Pfad = $Pfad . $Var . "/"; }
}

if (!defined('DIR_FS_DOCUMENT_ROOT')) {
	define('DIR_FS_DOCUMENT_ROOT', $Pfad);
}
if (!defined('DIR_FS_INSTALLER_PATH')) {
	define('DIR_FS_INSTALLER_PATH', DIR_FS_DOCUMENT_ROOT . '/self_installer/');
}
if (!defined('DIR_FS_INC')) {
	define('DIR_FS_INC', DIR_FS_DOCUMENT_ROOT . '/inc/');
}
if (!defined('DIR_FS_CATALOG')) {
	define('DIR_FS_CATALOG', DIR_FS_DOCUMENT_ROOT);
}

// include
//require('../includes/functions/validations.php');
require(DIR_FS_CATALOG . '/includes/classes/boxes.php');
require(DIR_FS_CATALOG . '/includes/classes/message_stack.php');
require(DIR_FS_CATALOG . '/includes/filenames.php');
require(DIR_FS_CATALOG . '/includes/database_tables.php');
require_once(DIR_FS_CATALOG . '/inc/xtc_image.inc.php');

// Start the Install_Session
session_start();

// Set the level of error reporting
error_reporting(E_ALL & ~E_NOTICE);

define('CR', "\n");
define('BOX_BGCOLOR_HEADING', '#bbc3d3');
define('BOX_BGCOLOR_CONTENTS', '#f8f8f9');
define('BOX_SHADOW', '#b6b7cb');

// include General functions
require_once(DIR_FS_INC . 'xtc_set_time_limit.inc.php');
require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
require_once(DIR_FS_INC . 'xtc_in_array.inc.php');

// Include Database functions for installer
require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');
require_once(DIR_FS_INC . 'xtc_db_connect_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_db_select_db.inc.php');
require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
require_once(DIR_FS_INC . 'xtc_db_query_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
require_once(DIR_FS_INC . 'xtc_db_data_seek.inc.php');
require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
require_once(DIR_FS_INC . 'xtc_db_test_create_db_permission.inc.php');
require_once(DIR_FS_INC . 'xtc_db_test_connection.inc.php');
require_once(DIR_FS_INC . 'xtc_db_install.inc.php');

// include Html output functions
require_once(DIR_FS_INC . 'xtc_draw_input_field_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_password_field_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_hidden_field_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_checkbox_field_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_radio_field_installer.inc.php');

// iinclude check functions
require_once(DIR_FS_INC . 'xtc_gdlib_check.inc.php');

define('DIR_WS_ICONS', 'images/');

function xtc_check_version($mini='4.1.2') {
	$dummy=phpversion();
	sscanf($dummy,"%d.%d.%d%s",$v1,$v2,$v3,$v4);
	sscanf($mini,"%d.%d.%d%s",$m1,$m2,$m3,$m4);
	if($v1>$m1)
		return(1);
	elseif($v1<$m1)
		return(0);
	if($v2>$m2)
		return(1);
	elseif($v2<$m2)
		return(0);
	if($v3>$m3)
		return(1);
	elseif($v3<$m3)
		return(0);
	if((!$v4)&&(!$m4))
		return(1);
	if(($v4)&&(!$m4)) {
		$dummy=strpos($v4,"pl");
		if(is_integer($dummy))
			return(1);
		return(0);
	}
	elseif((!$v4)&&($m4)) {
		$dummy=strpos($m4,"rc");
		if(is_integer($dummy))
			return(1);
		return(0);
	}
	return(0);
}
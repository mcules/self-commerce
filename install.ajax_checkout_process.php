<?php
## Install Ajax Checkout Process Module ##

require ('includes/application_top.php');

$query0 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_AJAX_STAT', 'true', 333, 0, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query0_exe = xtc_db_query($query0);

$query1 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_AJAX_PRODUCTS', 'true', 333, 1, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query1_exe = xtc_db_query($query1);

$query2 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_AJAX_FULL_BOXES', 'false', 333, 2, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query2_exe = xtc_db_query($query2);

$query3 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_SHIPPING_MODULES', 'false', 333, 3, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query3_exe = xtc_db_query($query3);

$query4 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_SHIPPING_ADDRESS', 'false', 333, 4, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query4_exe = xtc_db_query($query4);

$query5 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_PAYMENT_MODULES', 'false', 333, 5, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query5_exe = xtc_db_query($query5);

$query6 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_PAYMENT_ADDRESS', 'false', 333, 6, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query6_exe = xtc_db_query($query6);

$query7 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_COMMENTS', 'false', 333, 7, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query7_exe = xtc_db_query($query7);

$query8 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_PRODUCTS', 'false', 333, 8, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query8_exe = xtc_db_query($query8);

$query9 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_AGB', 'false', 333, 9, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query9_exe = xtc_db_query($query9);

$query10 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_REVOCATION', 'false', 333, 10, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query10_exe = xtc_db_query($query10);

$query11 = "INSERT INTO `".TABLE_CONFIGURATION_GROUP."` (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (333, 'Checkout process', 'Customize the checkout process', 333, 1)";
$query11_exe = xtc_db_query($query11);

// CO EXTENDED
$query12 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order, set_function) VALUES ('CHECKOUT_SHOW_LOGIN', 'true', 333, 11, '".addslashes("xtc_cfg_select_option(array('true', 'false'),")."')";
$query12_exe = xtc_db_query($query12);

$query13 = "ALTER TABLE admin_access ADD cox_sort INT(1) DEFAULT 1";
$query13_exe = xtc_db_query($query13);

$query14 = "INSERT INTO `".TABLE_CONFIGURATION."` (configuration_key, configuration_value, configuration_group_id, sort_order) VALUES ('CHECKOUT_BOX_ORDER', 'modules|addresses|products|comments|legals', 333, 12)";
$query14_exe = xtc_db_query($query14);

if ($query0_exe && $query1_exe && $query2_exe && $query3_exe && $query4_exe && $query5_exe && $query6_exe && $query7_exe && $query8_exe && $query9_exe && $query10_exe && $query11_exe && $query12_exe && query13_exe && query14_exe) {
	echo '<b>Installation war erfolgreich.</b><br>Bitte l&ouml;schen Sie diese Datei von Ihrem Server.';
} else {
	echo '<b>Installation war nicht erfolgreich (Vielleicht ist das Script bereits ausgeführt worden?).</b><br>Bitte kontaktieren Sie den Hersteller.';
}
?>
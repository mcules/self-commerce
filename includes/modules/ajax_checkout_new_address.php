<?php
$module_smarty = new Smarty;
$module_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
// include needed functions

require_once (DIR_FS_INC.'xtc_get_country_list.inc.php');

$module_smarty->assign('gender', (ACCOUNT_GENDER == 'true'));
$module_smarty->assign('company', (ACCOUNT_COMPANY == 'true'));
$module_smarty->assign('suburb', (ACCOUNT_SUBURB == 'true'));
$module_smarty->assign('state', (ACCOUNT_STATE == 'true'));

$selected_country = $_POST['country'] ? $_POST['country'] : STORE_COUNTRY;
$zones_array = array();
$zones_query = xtc_db_query("select zone_name from ".TABLE_ZONES." where zone_country_id = '".xtc_db_input($selected_country)."' order by zone_name");
while ($zones_values = xtc_db_fetch_array($zones_query)) {
  $zones_array[] = array ('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
}
if (count($zones_array) > 0) {
  $entry_state = xtc_draw_pull_down_menuNote(array('name' => 'state'), $zones_array);
  $module_smarty->assign('INPUT_STATE', $entry_state);
}

$module_smarty->assign('SELECT_COUNTRY', xtc_get_country_list('country', $selected_country));
$module_smarty->assign('language', $_SESSION['language']);

$module_smarty->caching = 0;
$module = $module_smarty->fetch(CURRENT_TEMPLATE.'/module/ajax_checkout_new_address.html');

$smarty->assign('MODULE_new_address', $module);
?>
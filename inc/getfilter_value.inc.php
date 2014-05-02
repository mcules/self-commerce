<?php
function sc_getFilter_value($pID, $valueID){
	$values_query = xtc_db_query("SELECT pov.products_options_values_name,pf.products_options_id,pov.value_image
									FROM ".TABLE_PRODUCTS_FILTER." AS pf, ".TABLE_PRODUCTS_OPTIONS_VALUES." AS pov
									WHERE pf.products_id='" . (int)$pID . "'
									AND pf.products_options_id = '". (int)$valueID . "'
									AND pf.products_options_values_id = pov.products_options_values_id
									AND pov.language_id = '".(int)$_POST['languages_id']."'");
	while ($values_data = xtc_db_fetch_array($values_query)) {
		$value = $values_data['products_options_values_name'];
	}

	return $value;
}
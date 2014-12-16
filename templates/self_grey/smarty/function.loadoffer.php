<?php
/**
 * Ordner: templates/CURRENT_TEMPLATE/smarty/
 * Datei: function.content_id.php
 * erstellt am: 24.02.13
 * von: Dennis Eisold
 * eMail: support@eisold-edv.de
 */

function smarty_function_loadoffer($params, &$smarty)
{
    require_once (DIR_FS_INC.'xtc_random_select.inc.php');

	if (!isset($params['count'])) {
	    $Count = MAX_RANDOM_SELECT_SPECIALS;
    }
    else {
	    $Count = $params['count'];
    }
   	//fsk18 lock
	$fsk_lock = '';
	if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
		$fsk_lock = ' AND p.products_fsk18!=1';
	}
	if (GROUP_CHECK == 'true') {
		$group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
	}
    $Sql = "SELECT p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, p.products_vpe, p.products_vpe_status, p.products_vpe_value
			FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
			WHERE p.products_status = '1'
				AND pd.language_id = '".$_SESSION['languages_id']."'
				AND p.products_startpage = '1'
				$group_check
				$fsk_lock
			LIMIT $Count;";
    $ContentManagerQuery = xtDBquery($Sql);
	while ($ContentManagerData = xtc_db_fetch_array($ContentManagerQuery, true)) {
		$Offer_Product = new product($ContentManagerData['products_id']);
		$Offer_Product = $Offer_Product->buildDataArray($Offer_Product->data);
	}
	$smarty->assign('Offer', $Offer_Product);
}
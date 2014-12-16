<?php
/**
 * Ordner: templates/CURRENT_TEMPLATE/smarty/
 * Datei: function.content_id.php
 * erstellt am: 24.02.13
 * von: Dennis Eisold
 * eMail: support@eisold-edv.de
 */

function smarty_function_loadcontent($params, &$smarty)
{
    if (!isset($params['id'])) {
	    $smarty->trigger_error("function content_id: 'Content_Id' argument is missing!");
    }
    $Content_Id = $params['id'];
    $Sql = "SELECT content_title, content_text, content_heading
    		FROM ".TABLE_CONTENT_MANAGER."
    		WHERE content_group ='".$Content_Id."'
    		AND languages_id='".(int) $_SESSION['languages_id']."';";
    $ContentManagerQuery = xtDBquery($Sql);
	while ($ContentManagerData = xtc_db_fetch_array($ContentManagerQuery, true)) {
		$Content[$Content_Id]['heading'] = $ContentManagerData['content_heading'];
		$Content[$Content_Id]['text'] = $ContentManagerData['content_text'];
	}
	$smarty->assign('Content', $Content);
}
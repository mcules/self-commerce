<?php
/**
 * --------------------------------------------------------------

   --------------------------------------------------------------
 */
require ('includes/application_top.php');
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');
require_once(DIR_FS_INC . 'xtc_filesize.inc.php');
require(DIR_WS_FUNCTIONS . 'func_content_manager.php');

$languages = xtc_get_languages();

// actions ausgelagert
include('includes/actions_content_manager.php');
require ('includes/application_top_1.php');
require_once(DIR_FS_INC . 'xtc_wysiwyg.inc.php');

// Include WYSIWYG if is activated
if (USE_WYSIWYG=='true') {
	$query=xtc_db_query("SELECT code FROM ". TABLE_LANGUAGES ." WHERE languages_id='".$_SESSION['languages_id']."'");
	$data=xtc_db_fetch_array($query);
	echo xtc_wysiwyg('content_manager',$data['code']);
}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="80" rowspan="2">
			<?php echo xtc_image(DIR_WS_ICONS.'heading_content.gif'); ?>
		</td>
		<td class="pageHeading">
			<?php echo HEADING_TITLE; ?>
		</td>
	</tr>
</table>
<?php
/**
 * Content
 * Start
 */
include('c_m_advanced.php');
/**
 * Content
 * End
 */
require(DIR_WS_INCLUDES . 'application_bottom.php');
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>

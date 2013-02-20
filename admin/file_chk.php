<?php
/* --------------------------------------------------------------
   $Id: file_chk.php 2007-10-02 kunigunde $
   Self-Commerce 
   http://www.self-commerce.de
   Copyright (c) 2007 Self-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2005      xt:commerce (start.php,1235 2005-09-21 19:11:43Z mz) www.xt-commerce.com
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
require ('includes/application_top.php');
// self_file_check begin
include(DIR_WS_FUNCTIONS.'self_file_chk.php');
// self_file_chk end
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<style type="text/css">
			.h2 { font-family: Trebuchet MS,Palatino,Times New Roman,serif; font-size: 13pt; font-weight: bold; }.h3 { font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 9pt; font-weight: bold; }
			/* Tooltip */
			a.tooltip, a.tooltip:link, a.tooltip:visited, a.tooltip:active {
				position: relative;
				width: auto;
				border: 1px solid Black; 
				background-color: #F1F1F1;
				padding: 1px 0 2px 0;
				text-decoration: none;
				font-size: 10px;
				cursor: pointer;
				line-height: 24px;
			}
			a.tooltip:hover {
				background: transparent;
				z-index: 100;
			}
			a.tooltip span {
				display: none;
				text-decoration: none;
			}
			a.tooltip:hover span {
				display: block;
				position: absolute;
				top: 30px;
				left: 0;
				width: 200px;
				z-index: 100;
				color: #000000;
				border: 1px solid;
				border-color: #FFFFFF #D5D7DB #D5D7DB #FFFFFF;
				border-left: 4px solid #4dbcf3;
				padding: 2px 10px 2px 10px;
				background: #EEEEEE;
				font-family: Verdana, Arial, Helvetica, Sans-serif;
				font-style: Normal;
				text-align: left;
			}
			/* hilfe tooltip */
			a.tooltip_help, a.tooltip_help:link, a.tooltip_help:visited, a.tooltip_help:active {
				position: relative;
				width: auto;
				border: 1px solid Black;
				background-color: #F1F1F1;
				padding: 1px 0 2px 0;
				text-decoration: none;
				font-size: 10px;
				cursor: help;
				line-height: 24px;
			}
			a.tooltip_help:hover {
				background: transparent;
				z-index: 100;
			}
			a.tooltip_help span {
				display: none;
				text-decoration: none;
			}
			a.tooltip_help:hover span {
				display: block;
				position: absolute;
				top: 30px;
				left: -200;
				width: 350px;
				z-index: 100;
				color: #000000;
				border: 1px solid;
				border-color: #FFFFFF #D5D7DB #D5D7DB #FFFFFF;
				border-left: 4px solid #4dbcf3;
				padding: 2px 10px 2px 10px;
				background: #EEEEEE;
				font-family: Verdana, Arial, Helvetica, Sans-serif;
				font-style: Normal;
				text-align: left;
			}
		</style>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->
		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->
					</table>
				</td>
				<!-- body_text //-->
				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_modules.gif'); ?></td>
										<td class="pageHeading"><?php echo FILE_CHK_PAGE_HEADER; ?></td>
										<td rowspan="2" class="main"><?php echo '<a class="tooltip_help" onClick="this.blur();" href="#">&nbsp;'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').' ' . BUTTON_HELP . ' <span>'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').' '.TOOLTIP_HELP.'</span></a>'; ?></td>                    
									</tr>
									<tr>
										<td class="main" valign="top"><?php echo FILE_CHK_PAGE_HEADER_1; ?></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<hr>
					<!-- self_file_check begin -->
					<table class="contentTable" border="0" cellspacing="0" cellpadding="2">
						<tr class="infoBoxHeading">
							<th class="infoBoxHeading"><b><?php echo TH_HTML_FILE; ?></b></th>
							<th bgcolor="black"></th>
							<th class="infoBoxHeading"><b><?php echo TH_PHP_FILE; ?></b></th>
						</tr>
						<tr>
							<td class="infoBoxContent" valign="middle">
								<?php echo '<br />&nbsp;<a class="tooltip" onClick="this.blur();" href="' . xtc_href_link(FILENAME_FILE_CHK, '&action=control&type=html', 'NONSSL') . '">&nbsp;'.xtc_image(DIR_WS_ICONS.'icon_edit.gif').' ' . BUTTON_CONTROL . '<span>'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').' '.TOOLTIP_CONTROL.'</span>&nbsp;</a>&nbsp;<a class="tooltip" onClick="this.blur();" href="' . xtc_href_link(FILENAME_FILE_CHK, '&action=set_new&type=html', 'NONSSL') . '">&nbsp;'.xtc_image(DIR_WS_ICONS.'delete.gif').' ' . BUTTON_SET_NEW . '<span>'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').' '.TOOLTIP_SET_NEW.'</span>&nbsp;</a>&nbsp;<br /><br />'; ?>
							</td>
							<td bgcolor="black"></td>
							<td class="infoBoxContent">
								<?php echo '<br /> &nbsp;<a class="tooltip" onClick="this.blur();" href="' . xtc_href_link(FILENAME_FILE_CHK, '&action=control&type=php', 'NONSSL') . '">&nbsp;'.xtc_image(DIR_WS_ICONS.'icon_edit.gif').' ' . BUTTON_CONTROL . '<span>'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').' '.TOOLTIP_CONTROL.'</span>&nbsp;</a>&nbsp;<a class="tooltip" onClick="this.blur();" href="' . xtc_href_link(FILENAME_FILE_CHK, '&action=set_new&type=php', 'NONSSL') . '">&nbsp;'.xtc_image(DIR_WS_ICONS.'delete.gif').' ' . BUTTON_SET_NEW . '<span>'.xtc_image(DIR_WS_ICONS.'icons/info.jpg').' '.TOOLTIP_SET_NEW.'</span>&nbsp;</a>&nbsp;<br /><br />'; ?>
							</td>
						</tr>
					</table>
					<?php
					echo '<br /><br />';
					if ($_GET['action']) {
						switch ($_GET['action']) {
							case 'set_new':
								// Prüfsumme setzen
								do_filechk();
								echo FILE_CHK_SET_NEW_OK;
								break;
							case 'control':
								?>
								<table border="0" cellspacing="2" cellpadding="2">
									<tr class="dataTableHeadingRow">
										<th class="dataTableHeadingContent"><?php echo TH_STATUS; ?></th>
										<th class="dataTableHeadingContent"><?php echo TH_FILE; ?></th>
									</tr>
									<?php
									// Änderungen anzeigen
									$sql = 'SELECT filepath, hash FROM self_commerce_filechk_' . $_GET['type'];
									$check_query = xtDBquery($sql);
									while ($row = xtc_db_fetch_array($check_query, true)) {
										$current_hash 	= '';
										$current_hash = @filesize($row['filepath']) . '-' . count(@file($row['filepath']));
										if ( $current_hash == '-1' ) {
											$filestatus = STATUS_DELETE;
											$color 			= '#0300FF';
										}
										elseif( md5($current_hash) != $row['hash']) {
											$filestatus = STATUS_CHANGE;
											$color      = '#FF1200';
										}
										else {
											$filestatus = STATUS_OK;
											$color 			= '#269F00';
										}
										// Pfad lesbar machen
										$path_cleaned = str_replace(DIR_FS_DOCUMENT_ROOT, '', $row['filepath']);

										// Ausgabe
										echo '<tr>
											<td class="dataTableContent"><font color="'.$color.'">'.$filestatus.'</font></td>
											<td class="dataTableContent">'.$path_cleaned.'</td>';
									}
						break;
						?>
							</tr>
						</table>
						<?php
						}
					}
					else {
						echo MAKE_SELECTION.'<br /><br />';
					}
					?>
					<!-- self_file_chk end -->
				</td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->
		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
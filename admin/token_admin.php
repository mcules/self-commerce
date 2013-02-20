<?php
/* --------------------------------------------------------------
   $Id: customers.php 1295 2005-10-08 16:59:56Z mz $

   Self-Commerce
   http://www.self-commerce.de

   Copyright (c) 2012 Self-Commerce
   --------------------------------------------------------------
   Released under the GNU General Public License
   --------------------------------------------------------------*/
require 'includes/application_top.php';
require_once DIR_FS_INC.'xtc_js_lang.php';
require 'includes/application_top_1.php';

if ($_GET['action'])
{
	switch ($_GET['action'])
	{
		case 'add' :
			$sql_data_array = array (
				'customers_id'	=> xtc_db_prepare_input((int)$_POST['customers_id']),
				'username'		=> xtc_db_prepare_input($_POST['username'])
			);

			xtc_db_perform(TABLE_TOKEN_USER, $sql_data_array);
			break;
		case 'delete' :
			$username	= xtc_db_prepare_input($_GET['username']);
			$email		= xtc_db_prepare_input($_GET['email']);
			$delete_sql = "DELETE ".TABLE_TOKEN_USER."
							FROM ".TABLE_TOKEN_USER."
							INNER JOIN ".TABLE_CUSTOMERS." AS cust ON (cust.customers_email_address='$email')
							WHERE ".TABLE_TOKEN_USER.".username='$username' AND cust.customers_id=".TABLE_TOKEN_USER.".customers_id;";
			xtc_db_query($delete_sql);
	}
}

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td>
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS.'heading_customers.gif'); ?></td>
					<td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="75%" valign="top">
			<table border="0" cellspacing="0" cellpadding="2" class="dataTableHeadingRow">
				<tr>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TOKEN_USER ?></td>
					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHOPUSER ?></td>
					<td></td>
				</tr>
			<?php
			$token_user_sql = "SELECT ".TABLE_TOKEN_USER.".*, ".TABLE_CUSTOMERS.".customers_firstname, ".TABLE_CUSTOMERS.".customers_lastname, ".TABLE_CUSTOMERS.".customers_email_address
								FROM ".TABLE_TOKEN_USER."
								LEFT JOIN ".TABLE_CUSTOMERS." ON (".TABLE_TOKEN_USER.".customers_id=".TABLE_CUSTOMERS.".customers_id);";
			$token_user_query = xtc_db_query($token_user_sql);
			while ($row = xtc_db_fetch_array($token_user_query))
			{
				echo '<tr>'.
					'<td class="dataTableContent">'.$row['username'].'</td>'.
					'<td class="dataTableContent">'.$row['customers_email_address'].'</td>'.
					'<td><a href="'.FILENAME_TOKEN_ADMIN.'?action=delete&username='.$row['username'].'&email='.$row['customers_email_address'].'" title="delete">'.xtc_image("../".DIR_WS_ICONS."cancel.png").'</a></td>'.
				'</tr>';
			}
			?>
			</table>
		</td>
		<td width="25%" valign="top">
			<table class="contentTable" border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr class="infoBoxHeading">
					<td class="infoBoxHeading"><?php echo ADD_USER; ?></td>
				</tr>
			</table>
			<table class="contentTable" border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td align="center" class="infoBoxContent">
						<?php echo xtc_draw_form('token_admin_add', FILENAME_TOKEN_ADMIN, 'action=add', 'post'); ?>
							<table align="left">
								<tr>
									<td class="infoBoxContent"><?php echo USERNAME; ?></td>
									<td><?php echo xtc_draw_input_field('username', $username, 'maxlength="32"'); ?></td>
								</tr>
								<tr>
									<td class="infoBoxContent"><?php echo CUSTOMERS_ID; ?></td>
									<td>
										<?php
										$admins_sql = "SELECT customers_id, customers_email_address FROM ".TABLE_CUSTOMERS." WHERE customers_status=0 GROUP BY customers_email_address;";
										$admins_query = xtc_db_query($admins_sql);
										while ($admins_row = xtc_db_fetch_array($admins_query))
										{
											$admins_array[] = array(
												'id'	=> $admins_row['customers_id'],
												'text'	=> $admins_row['customers_email_address']
											);
											if(!isset($admins_default))
											{
												$admins_default = $admins_row['customers_id'];
											}
										}
										echo xtc_draw_pull_down_menu('customers_id', $admins_array, $admins_default);
										?>
									</td>
								</tr>
								<tr>
									<td></td>
									<td class="infoBoxContent">
										<?php
										#echo '<input type="submit" class="button" value="'. BUTTON_INSERT .'">';
										echo xtc_button(BUTTON_INSERT);
										?>
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
require DIR_WS_INCLUDES . 'application_bottom.php';
require DIR_WS_INCLUDES . 'application_bottom_0.php';
?>
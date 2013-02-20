<?php


// (c) 2006 Web4Business GmbH - Designs - Modules. www.web4business.ch

/* (c) 2008 kunigunde www.self-commerce.de */
// BUGFIX Steuer bei update special price behoben
// insert modified

defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");

function showSpecialsBox() {
			// include localized categories specials strings
			  require_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/categories_specials.php');


			// if editing an existing product

			if(isset($_GET['pID'])) {

				$specials_query = "select p.products_tax_class_id,
												p.products_id,
												pd.products_name,
												p.products_price,
												s.specials_id,
												s.specials_quantity,
												s.specials_new_products_price,
												s.specials_date_added,
												s.specials_last_modified,
												s.expires_date from
												" . TABLE_PRODUCTS . " p,
												" . TABLE_PRODUCTS_DESCRIPTION . " pd,
												" . TABLE_SPECIALS . "
												s where p.products_id = pd.products_id
												and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
												and p.products_id = s.products_id
												and s.products_id = '" . (int)$_GET['pID'] . "'";

				$specials_query = xtDBquery($specials_query);

				// if there exists already a special for this product

				if(xtc_db_num_rows($specials_query, true) > 0) {

					$special = xtc_db_fetch_array($specials_query, true);
					$sInfo = new objectInfo($special);
				}
			}

			$price=$sInfo->products_price;
			$new_price=$sInfo->specials_new_products_price;

			if (PRICE_IS_BRUTTO=='true') {

 				$price_netto=xtc_round($price,PRICE_PRECISION);
				$new_price_netto=xtc_round($new_price,PRICE_PRECISION);
				$price= ($price*(xtc_get_tax_rate($sInfo->products_tax_class_id)+100)/100);
				$new_price= ($new_price*(xtc_get_tax_rate($sInfo->products_tax_class_id)+100)/100);
			}

			$price=xtc_round($price,PRICE_PRECISION);
			$new_price=xtc_round($new_price,PRICE_PRECISION);

			// build the expires date in the format dd.mm.YYYY

			if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0 and $sInfo->expires_date != 0)

				$expires_date = substr($sInfo->expires_date, 0, 4)."-".
								substr($sInfo->expires_date, 5, 2)."-".
								substr($sInfo->expires_date, 8, 2);

			else
				$expires_date = "";

			// tell the storing script if to update existing special,
			// or to insert a new one

			echo xtc_draw_hidden_field('specials_action',
					((isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0)
						? "update"
						: "insert"
					)
				);

			if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0)
				echo xtc_draw_hidden_field('specials_id', $sInfo->specials_id);

		?>

<script type="text/javascript">
  var specialExpires = new ctlSpiffyCalendarBox("specialExpires", "new_product", "specials_expires","btnDate2","<?php echo $expires_date; ?>",scBTNMODE_CUSTOMBLUE);
</script>

      <span class="main"><br /></span>
		<fieldset style="width:350px;border: 1px solid #cccccc;background-color:#f3f3f3;">
		<legend class="main" style="color:black;"><strong><?php echo SPECIALS_TITLE; ?></strong></legend>
      <table width="350" border="0" style="border: 0px dotted black;">
          <tr>
            <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?>&nbsp;</td>
            <td class="main"><?php echo xtc_draw_input_field('specials_price', $new_price);?> </td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_QUANTITY; ?>&nbsp;</td>
            <td class="main"><?php echo xtc_draw_input_field('specials_quantity', $sInfo->specials_quantity);?> </td>
          </tr>
		<?php if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0) { ?>
			<tr>
	          <td class="main"><?php echo TEXT_INFO_DATE_ADDED; ?></td>
	          <td class="main"><?php echo xtc_date_short($sInfo->specials_date_added); ?></td>
        	</tr>
			<tr>
	          <td class="main"><?php echo TEXT_INFO_LAST_MODIFIED; ?></td>
	          <td class="main"><?php echo xtc_date_short($sInfo->specials_last_modified); ?></td>
        	</tr>
		<?php } ?>
          <tr>
          <td class="main" width="127"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></td>
          <td class="main"><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>
              <script type="text/javascript">specialExpires.writeControl(); specialExpires.dateFormat="yyyy-MM-dd";</script></td>
		</tr>
		<tr>
			<td class="main" colspan="2">
				<?php echo TEXT_SPECIALS_PRICE_TIP; ?>
			</td>
		</tr>
		<?php if(isset($_GET['pID']) and xtc_db_num_rows($specials_query, true) > 0) { ?>
			<tr>
          <td class="main" colspan="2"><input type="checkbox" name="specials_delete" value="true"
				id="input_specials_delete"
				onclick="if(this.checked==true)return confirm('<?php echo TEXT_INFO_DELETE_INTRO; ?>');"
				style="vertical-align:middle;"
				/><label for="input_specials_delete"
				>&nbsp;<?php echo TEXT_INFO_HEADING_DELETE_SPECIALS; ?></label></td>
        	</tr>
		<?php } ?>
      </table>
		</fieldset>
<?php
}


function saveSpecialsData($products_id) {

		// decide whether to insert a new special,
		// or to update an existing one

		if($_POST['specials_action'] == "insert"
			and isset($_POST['specials_price'])
			and !empty($_POST['specials_price'])) {

			// insert a new special, code taken from /admin/specials.php, and modified

			if(!isset($_POST['specials_quantity']) or empty($_POST['specials_quantity']))
				$_POST['specials_quantity'] = 0;

     if (PRICE_IS_BRUTTO=='true' && substr($_POST['specials_price'], -1) != '%'){
        $sql="select tr.tax_rate from " . TABLE_TAX_RATES . " tr, " . TABLE_PRODUCTS . " p  where tr.tax_class_id = p. products_tax_class_id  and p.products_id = '". $products_id . "' ";
        $tax_query = xtc_db_query($sql);
        $tax = xtc_db_fetch_array($tax_query);
        $_POST['specials_price'] = ($_POST['specials_price']/($tax['tax_rate']+100)*100);
     }
     
     
      if (substr($_POST['specials_price'], -1) == '%')  {
        $new_special_insert_query = xtc_db_query("select products_price from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
        $new_special_insert = xtc_db_fetch_array($new_special_insert_query);
        $_POST['specials_price'] = ($new_special_insert['products_price'] - (($_POST['specials_price'] / 100) * $new_special_insert['products_price']));
      }
     
     
      $expires_date = '';
      if ($_POST['specials_expires']) {
        $expires_date = str_replace("-", "", $_POST['specials_expires']);
      }
     
      xtc_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_quantity, specials_new_products_price, specials_date_added, expires_date, status) values ('" . $products_id . "', '" . $_POST['specials_quantity'] . "', '" . $_POST['specials_price'] . "', now(), '" . $expires_date . "', '1')");

		}

		elseif($_POST['specials_action'] == "update"
			and isset($_POST['specials_price']) and isset($_POST['specials_quantity'])) {

			// update the existing special for this product, code taken from /admin/specials.php, and modified

      if (PRICE_IS_BRUTTO=='true' && substr($_POST['specials_price'], -1) != '%'){
        $sql="select tr.tax_rate from " . TABLE_TAX_RATES . " tr, " . TABLE_PRODUCTS . " p  where tr.tax_class_id = p. products_tax_class_id  and p.products_id = '". $products_id . "' ";
        $tax_query = xtc_db_query($sql);
        $tax = xtc_db_fetch_array($tax_query);
        $_POST['specials_price'] = ($_POST['specials_price']/($tax[tax_rate]+100)*100);
     }

      if (substr($_POST['specials_price'], -1) == '%')  {
        $new_special_insert_query = xtc_db_query("select products_price from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
        $new_special_insert = xtc_db_fetch_array($new_special_insert_query);
        $_POST['specials_price'] = ($new_special_insert['products_price'] - (($_POST['specials_price'] / 100) * $new_special_insert['products_price']));
      }
      $expires_date = '';
      if ($_POST['specials_expires']) {
        $expires_date = str_replace("-", "", $_POST['specials_expires']);
      }

      xtc_db_query("update " . TABLE_SPECIALS . " set specials_quantity = '" . $_POST['specials_quantity'] . "', specials_new_products_price = '" . $_POST['specials_price'] . "', specials_last_modified = now(), expires_date = '" . $expires_date . "' where specials_id = '" . $_POST['specials_id'] . "'");

		}

		if(isset($_POST['specials_delete'])) {

			// delete existing special for this product, code taken from /admin/specials.php, and modified

			xtc_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . xtc_db_input($_POST['specials_id']) . "'");
		}


}
?>

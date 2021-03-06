<?php

/* --------------------------------------------------------------
   $Id: new_product.php 74 2012-11-17 21:57:55Z deisold $

   Self-Commerce‚
   http://www.self-commerce.de

   Copyright (c) 2012 Self-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
   (c) 2003  XT-Commerce
   (c) 2012  Self-Commerce

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

if (($_GET['pID']) && (!$_POST)) {
	$product_query = xtc_db_query("SELECT *, date_format(p.products_date_available, '%Y-%m-%d') AS products_date_available
									FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
									WHERE p.products_id = '".(int) $_GET['pID']."'
										AND p.products_id = pd.products_id
										AND pd.language_id = '".$_SESSION['languages_id']."'");

	$product = xtc_db_fetch_array($product_query);
	$pInfo = new objectInfo($product);

}
elseif ($_POST) {
	$pInfo = new objectInfo($_POST);
	$products_name = $_POST['products_name'];
	$products_description = $_POST['products_description'];
	$products_short_description = $_POST['products_short_description'];
	$products_keywords = $_POST['products_keywords'];
	$products_meta_title = $_POST['products_meta_title'];
	$products_meta_description = $_POST['products_meta_description'];
	$products_meta_keywords = $_POST['products_meta_keywords'];
	$products_url = $_POST['products_url'];
	$pInfo->products_startpage = $_POST['products_startpage'];
	$products_startpage_sort = $_POST['products_startpage_sort'];
}
else {
	$pInfo = new objectInfo(array ());
}

$Products_Details_Array = array();
$Products_Details_Sql = "SELECT *
						FROM ".TABLE_PRODUCTS_DETAILS.";";
$Products_Details_Query = xtc_db_query($Products_Details_Sql);
while($Products_Details = xtc_db_fetch_array($Products_Details_Query)) {
	$Products_Details_Array[] = $Products_Details;
}

$manufacturers_array = array (array ('id' => '', 'text' => TEXT_NONE));
$manufacturers_query = xtc_db_query("SELECT manufacturers_id, manufacturers_name
									FROM ".TABLE_MANUFACTURERS."
									ORDER BY manufacturers_name");
while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
	$manufacturers_array[] = array ('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
}

$vpe_array = array (array ('id' => '', 'text' => TEXT_NONE));
$vpe_query = xtc_db_query("SELECT products_vpe_id, products_vpe_name
							FROM ".TABLE_PRODUCTS_VPE."
							WHERE language_id='".$_SESSION['languages_id']."'
							ORDER BY products_vpe_name");
while ($vpe = xtc_db_fetch_array($vpe_query))
{
	$vpe_array[] = array ('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
$tax_class_query = xtc_db_query("SELECT tax_class_id, tax_class_title
								FROM ".TABLE_TAX_CLASS."
								ORDER BY tax_class_title");
while ($tax_class = xtc_db_fetch_array($tax_class_query))
{
	$tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
}
$shipping_statuses = array ();
$shipping_statuses = xtc_get_shipping_status();
$languages = xtc_get_languages();

switch ($pInfo->products_status)
{
case '0' :
	$status = false;
	$out_status = true;
	break;
case '1' :
default :
	$status = true;
	$out_status = false;
}

if ($pInfo->products_startpage == '1')
	{ $startpage_checked = true; } else
	{ $startpage_checked = false; }

?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script type="text/javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available", "btnDate1", "<?php echo $pInfo->products_date_available; ?>", scBTNMODE_CUSTOMBLUE);
</script>
<script>
  $(function() {
    $( "#tabs" ).tabs({
      collapsible: true
    });
  });
</script>
<tr><td>
<?php $form_action = ($_GET['pID']) ? 'update_product' : 'insert_product'; ?>
<?php $fsk18_array=array(array('id'=>0, 'text'=>NO), array('id'=>1, 'text'=>YES)); ?>
<?php echo xtc_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . '&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>
<span class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, xtc_output_generated_category_path($current_category_id)); ?></span><br />
<table width="100%"  border="0">
  <tr>
    <td>
<?php
require_once "includes/modules/categories_specials.php";
showSpecialsBox();
?>
    </td><td>
      <table border="0">
        <tr>
          <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
          <td class="main"><?php echo xtc_draw_radio_field('products_status', '1', $status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . xtc_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
        </tr>
        <tr>
          <td class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><br />
              <small>(YYYY-MM-DD)</small></td>
          <td class="main"><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>
              <script type="text/javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
        </tr>
      </table>

    <table bgcolor="f3f3f3" style="border: 1px solid; border-color: #cccccc;" width="100%"  border="0">
    <tr>
        <td><span class="main"><?php echo TEXT_PRODUCTS_STARTPAGE; ?> <?php echo TEXT_PRODUCTS_STARTPAGE_YES . xtc_draw_radio_field('products_startpage', '1', $startpage_checked) . '&nbsp;' . TEXT_PRODUCTS_STARTPAGE_NO . xtc_draw_radio_field('products_startpage', '0', !$startpage_checked) ?></span></td>
        <td><span class="main"><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?>&nbsp;<?php echo  xtc_draw_input_field('products_startpage_sort', $pInfo->products_startpage_sort , 'size=3'); ?></span></td>
    </tr>
    <tr>
        <td><span class="main"><?php echo TEXT_PRODUCTS_SORT; ?>&nbsp;<?php echo  xtc_draw_input_field('products_sort', $pInfo->products_sort, 'size=3'); ?></span></td>
        <td><span class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?>&nbsp;<?php echo xtc_draw_input_field('products_quantity', $pInfo->products_quantity, 'size=5'); ?></span></td>
      </tr>
      <tr>
        <td><span class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></span></td>
        <td><span class="main"><?php echo  xtc_draw_input_field('products_model', $pInfo->products_model); ?></span></td>
      </tr>
                    <tr>
        <td><span class="main"><?php echo TEXT_PRODUCTS_EAN; ?></span></td>
        <td><span class="main"><?php echo  xtc_draw_input_field('products_ean', $pInfo->products_ean); ?></span></td>
      </tr>
      <tr>
        <td><span class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></span></td>
        <td><span class="main"><?php echo xtc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?></span></td>
      </tr>
      <tr>
        <td><span class="main"><?php echo TEXT_PRODUCTS_VPE_VISIBLE.xtc_draw_selection_field('products_vpe_status', 'checkbox', '1', $pInfo->products_vpe_status==1 ? true : false); ?>
        <?php echo TEXT_PRODUCTS_VPE_VALUE.xtc_draw_input_field('products_vpe_value', $pInfo->products_vpe_value, 'size=4'); ?></span></td>
        <td><span class="main"><?php echo TEXT_PRODUCTS_VPE. xtc_draw_pull_down_menu('products_vpe', $vpe_array, $pInfo->products_vpe='' ?  DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe); ?></span></td>
      </tr>
      <tr>
        <td><span class="main"><?php echo TEXT_FSK18; ?>&nbsp;<?php echo xtc_draw_pull_down_menu('fsk18', $fsk18_array, $pInfo->products_fsk18); ?></span></td>
        <td><span class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?><?php echo xtc_draw_input_field('products_weight', $pInfo->products_weight, 'size=4'); ?><?php echo TEXT_PRODUCTS_WEIGHT_INFO; ?></span></td>
      </tr>
      <tr>
<?php if (ACTIVATE_SHIPPING_STATUS=='true')
	{ ?>
        <td><span class="main"><?php echo BOX_SHIPPING_STATUS.':'; ?></span></td>
        <td><span class="main"><?php echo xtc_draw_pull_down_menu('shipping_status', $shipping_statuses, $pInfo->products_shippingtime); ?></span></td>
      </tr>
<?php } ?>
      <tr>
          <?php

$files = array ();
if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'))
{
	while (($file = readdir($dir)) !== false)
	{
		if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and ($file != "index.html"))
		{
			$files[] = array ('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
$default_array = array ();
// set default value in dropdown!
if ($content['content_file'] == '')
{
	$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
	$default_value = $pInfo->product_template;
	$files = array_merge($default_array, $files);
} else
{
	$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
	$default_value = $pInfo->product_template;
	$files = array_merge($default_array, $files);
}
echo '<td class="main">'.TEXT_CHOOSE_INFO_TEMPLATE.':</td>';
echo '<td><span class="main">'.xtc_draw_pull_down_menu('info_template', $files, $default_value);
?>
        </span></td>
      </tr>
      <tr>


          <?php

$files = array ();
if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'))
{
	while (($file = readdir($dir)) !== false)
	{
		if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and ($file != "index.html"))
		{
			$files[] = array ('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
// set default value in dropdown!
$default_array = array ();
if ($content['content_file'] == '')
{
	$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
	$default_value = $pInfo->options_template;
	$files = array_merge($default_array, $files);
} else
{
	$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
	$default_value = $pInfo->options_template;
	$files = array_merge($default_array, $files);
}
echo '<td class="main">'.TEXT_CHOOSE_OPTIONS_TEMPLATE.':'.'</td>';
echo '<td><span class="main">'.xtc_draw_pull_down_menu('options_template', $files, $default_value);
?>
        </span></td>
		</tr>
      <tr>
        <td>
            <span class="main">
                <?php require DIR_FS_CATALOG.DIR_WS_CLASSES.'xtcPrice.php';
                $xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']); ?>
            </span>
        </td>
      </tr>
    </table><br />
    </td>
  </tr>
</table>
  <br /><br />
  <div id="tabs">
  <ul>
  	<?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
	    <li><a href="#tabs-<?php echo $languages[$i]['id']; ?>"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image'], $languages[$i]['name']) . ' <b>' . $languages[$i]['name'] . '</b>'; ?></a></li>
    <?php } ?>
  </ul>
  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
  <div id="tabs-<?php echo $languages[$i]['id']; ?>">
  <table width="100%" border="0">
  <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_NAME; ?><?php echo xtc_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : xtc_get_products_name($pInfo->products_id, $languages[$i]['id'])), 'size=60'); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_URL . '&nbsp;<small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?><?php echo xtc_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : xtc_get_products_url($pInfo->products_id, $languages[$i]['id'])), 'size=60'); ?></td>
  </tr>
</table>

<!-- input boxes desc, meta etc -->
<table width="100%" border="0">
  <tr>
    <td class="main">
    	<table>
    		<tr>
    			<td width="60%" valign="top" class="main">
    				<STRONG><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></STRONG><br />
    				<?php echo xtc_draw_textarea_field('products_description_' . $languages[$i]['id'], 'soft', '103', '30', (($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : xtc_get_products_description($pInfo->products_id, $languages[$i]['id']))); ?>
    			</td>
    			<td class="main" valign="top" style="padding: 15px;">
    				<table>
    					<?php
	    					foreach($Products_Details_Array as $Product_Detail) {
	    						if($pInfo->products_id) {
					    			$Detail_Sql = "SELECT ".TABLE_PRODUCTS_DETAILS_VALUES.".products_details_value, products_details_id
					    							FROM ".TABLE_PRODUCTS_DETAILS_VALUES."
					    							WHERE ".TABLE_PRODUCTS_DETAILS_VALUES.".products_details_id=".$Product_Detail['products_details_id']."
					    								AND ".TABLE_PRODUCTS_DETAILS_VALUES.".language_id=".$languages[$i]['id']."
					    								AND ".TABLE_PRODUCTS_DETAILS_VALUES.".products_id=".$pInfo->products_id."
					    							LIMIT 1;";
					    			$Detail = xtc_db_fetch_array(xtc_db_query($Detail_Sql));
					    		}
				    		?>
	    						<tr>
	    							<td class="main">
	    								<?php
	    								echo $Product_Detail['products_details_name'].'<br />';
	    								?>
	    							</td>
	    							<td>
					    				<?php
					    				echo xtc_draw_input_field('products_details_id[' . $Product_Detail['products_details_id'] . '.'.$languages[$i]['id'].']', $Detail['products_details_value'], ' size=25 maxlenght=255').'<br />';
					    				?>
				    				</td>
	    						</tr>
	    					<?php
	    					}
			    		?>
    				</table>
    			</td>
    		</tr>
    	</table>
    </td>
  </tr>
  <tr>
    <td class="main" valign="top">
    <table>
    <tr>
     <td width="60%" valign="top" class="main">
        <STRONG><?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></STRONG><br />
        <?php echo xtc_draw_textarea_field('products_short_description_' . $languages[$i]['id'], 'soft', '103', '20', (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : xtc_get_products_short_description($pInfo->products_id, $languages[$i]['id']))); ?>
     </td>
     <td class="main" valign="top" style="padding: 15px;">
        <?php echo TEXT_PRODUCTS_KEYWORDS; ?><br />
        <?php echo xtc_draw_input_field('products_keywords[' . $languages[$i]['id'] . ']', (($products_keywords[$languages[$i]['id']]) ? stripslashes($products_keywords[$languages[$i]['id']]) : xtc_get_products_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=255'); ?><br />
        <?php echo TEXT_META_TITLE; ?><br />
        <?php echo xtc_draw_input_field('products_meta_title[' . $languages[$i]['id'] . ']', (($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : xtc_get_products_meta_title($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=50'); ?><br />
        <?php echo TEXT_META_DESCRIPTION; ?><br />
        <?php echo xtc_draw_input_field('products_meta_description[' . $languages[$i]['id'] . ']', (($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : xtc_get_products_meta_description($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=50'); ?><br />
        <?php echo TEXT_META_KEYWORDS; ?><br />
        <?php echo xtc_draw_input_field('products_meta_keywords[' . $languages[$i]['id'] . ']', (($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : xtc_get_products_meta_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=25 maxlenght=50'); ?>
     </td>
    </tr>
    </table>
   </td>
  </tr>
</table>
</div>
<?php } ?>
</div>
<table width="100%"><tr><td style="border-bottom: thin dashed Gray;">&nbsp;</td></tr></table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td><span class="main" style="padding-left: 10px;"><?php echo HEADING_PRODUCT_IMAGES; ?></span></td></tr>
<tr><td><br />
<table width="100%" border="0" bgcolor="f3f3f3" style="border: 1px solid; border-color: #cccccc;">

<?php

include DIR_WS_MODULES.'products_images.php';
?>
    <tr><td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
<?php

if (GROUP_CHECK == 'true') {
	$customers_statuses_array = xtc_get_customers_statuses();
	$customers_statuses_array = array_merge(array (array ('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
	?>
	<tr>
	<td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
	<td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;"  bgcolor="#FFCC33" class="main">
	<?php
		for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
			$code = '$id=$pInfo->group_permission_'.$customers_statuses_array[$i]['id'].';';
			eval ($code);
			if ($id==1) {
				$checked = 'checked ';
			}
			else {
				$checked = '';
			}
			echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
		}
	?>
	</td>
	</tr>
	<?php
}
?>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0">
		        <tr>
			        <td colspan="4"><?php include DIR_WS_MODULES.'group_prices.php'; ?></td>
		        </tr>
		        <tr>
		        	<td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		        </tr>
        	</table>
		</td>
	</tr>
    <tr>
		<td class="main" align="right">
			<?php
			echo xtc_draw_hidden_field('products_date_added', (($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
			echo xtc_draw_hidden_field('products_id', $pInfo->products_id);
			?>
			<input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
			&nbsp;&nbsp;
			<?php echo '<a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
		 </td>
  	 </tr>
</form>
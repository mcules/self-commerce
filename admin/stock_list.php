<?php
/* --------------------------------------------------------------
  $Id: stock_list.php,v 1.00 2006/05/21 GERMANN.NEWMEDIA

  XTC - Contribution for XT-Commerce http://www.xt-commerce.com
  -----------------------------------------------------------------------------

  Copyright (c) 2006 GNM
  http://www.internetauftritte.ch

  Released under the GNU General Public License
   --------------------------------------------------------------*/
require ('includes/application_top.php');
require ('includes/application_top_1.php');
?>
<!-- content -->
<link rel="stylesheet" type="text/css" href="includes/stylesheet_stocklist.css" media="screen">
<link rel="stylesheet" type="text/css" href="includes/stylesheet_stocklist_print.css" media="print">

<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
             
			 <td class="smallText" align="right">
			 	<?php 
				// how many products on the page
				echo xtc_draw_form('formpaging', FILENAME_STOCK_LIST, '', 'get');
				echo xtc_draw_pull_down_menu('paging', $pages_array, $_GET['paging'], 'onChange="this.form.submit();"'); 
				echo xtc_draw_hidden_field('search', $_GET['search']);
				echo xtc_draw_hidden_field('page', $_GET['page']);
				echo xtc_draw_hidden_field('sorting', $_GET['sorting']);
			 
				if ($_GET['paging']) {
				$showpage = $_GET['paging'];
				} else {
				$showpage = SHOW_PAGE_STANDARD;
				}

			 	?></form></td>

			 <td class="smallText" align="right">
                <?php 
			    // product search field
				echo xtc_draw_form('search', FILENAME_STOCK_LIST, '', 'get'); 
				echo HEADING_TITLE_SEARCH . ' ' . xtc_draw_input_field('search', $_GET['search']);
				echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
				echo xtc_draw_hidden_field('paging', $showpage);
				echo xtc_draw_hidden_field('sorting', $_GET['sorting']);					 
                ?>
                </form>
             </td>          
		   </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
			
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STOCKLIST_ID.xtc_sorting(FILENAME_STOCK_LIST,'id'); ?></td>
				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STOCKLIST_STATUS.xtc_sorting(FILENAME_STOCK_LIST,'status'); ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STOCKLIST_NUMBER.xtc_sorting(FILENAME_STOCK_LIST,'model'); ?></td>
				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STOCKLIST_DESCR.xtc_sorting(FILENAME_STOCK_LIST,'name'); ?>
					<table border="0" width="100%" cellspacing="0" cellpadding="2">
					<tr class="dataTableRow3">
					<td class="dataTableContent3" width="20%"><?php echo TABLE_HEADING_STOCKLIST_OPTION; ?>:</td>
					<td class="dataTableContent3" width="50%"><?php echo TABLE_HEADING_STOCKLIST_OPTIONVALUE; ?></td>
					<td class="dataTableContent3" align="right"><?php echo TABLE_HEADING_STOCKLIST_OPTIONSTOCK; ?></td>			
					</tr>
					</table>
				</td>
				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STOCKLIST_STOCK.xtc_sorting(FILENAME_STOCK_LIST,'quantity'); ?></td>
              </tr>	  
		<?
		$rows = 0;
    	$products_count = 0;
	
	

		// get sorting option and switch accordingly        
		if ($_GET['sorting']) {
		switch ($_GET['sorting']){
			case 'id'         : 
				$prodsort   = 'p.products_id ASC';
				break;
			case 'id-desc'    :
				$prodsort   = 'p.products_id DESC';
				break;			
			case 'status'         :
				$prodsort   = 'p.products_status ASC, p.products_model ASC';
				break;
			case 'status-desc'    :
				$prodsort   = 'p.products_status DESC, p.products_model ASC';
				break;                  
			case 'model'       :
				$prodsort   = 'p.products_model ASC';
				break;
			case 'model-desc'  :
				$prodsort   = 'p.products_model DESC';
				break;             
			case 'name'        :
				$prodsort   = 'pd.products_name ASC';            
				break;
			case 'name-desc'   :
				$prodsort   = 'pd.products_name DESC';            
				break;            
			case 'quantity'        :
				$prodsort   = 'p.products_quantity ASC';            
				break;
			case 'quantity-desc'   :
				$prodsort   = 'p.products_quantity DESC';            
				break;                                              
			default             :
				$prodsort   = 'p.products_quantity ASC';
				break;
		}
		} else {
				$prodsort   = 'p.products_quantity ASC';
		}       
			
			
// init paging
  $products_query_raw = "select p.products_id, p.products_model, pd.products_name, p.products_sort, p.products_quantity, p.products_price, p.products_status from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and (pd.products_name like '%" . $_GET['search'] . "%' or p.products_model like '%" . $_GET['search'] . "%') order by " .$prodsort. "";
  $products_split = new splitPageResults($_GET['page'], $showpage, $products_query_raw, $products_query_numrows);
  $products_query = xtc_db_query($products_query_raw);

// product loop
 while ($products = xtc_db_fetch_array($products_query)) {
    $products_count++;
    $rows++;


// product attributes
		$attributessort   = 'po.products_options_name ASC, pov.products_options_values_name ASC';	
		
		$attributes_query = xtc_db_query("
        SELECT
		pa.products_attributes_id,
        pa.products_id,	
		pa.attributes_stock, 
		pa.options_values_id,
		pa.options_id,
		pa.options_id,
		po.products_options_id,
		po.products_options_name, 
		po.language_id, 
		pov.products_options_values_id,
		pov.products_options_values_name  
			
			FROM 	" . TABLE_PRODUCTS_ATTRIBUTES . " pa, 
					" . TABLE_PRODUCTS_OPTIONS . " po, 
					" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov  	
			
			WHERE pa.products_id = ".$products['products_id']."  
				AND po.language_id = '" . $_SESSION['languages_id'] . "'  
				AND pov.language_id = '" . $_SESSION['languages_id'] . "' 
				AND pa.options_values_id = pov.products_options_values_id 
				AND pa.options_id = po.products_options_id 
			
			ORDER BY " . $attributessort);

	  ?>    
		<tr class="dataTableRow2" onMouseOver="this.className='dataTableRowOver2'" onMouseOut="this.className='dataTableRow2'" valign="top">
			<td class="dataTableContent2"><?php echo $products['products_id']; ?></td>
			<td class="dataTableContent2"><?php
			if ($products['products_status'] == '1') {
                echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10);
            } else {
                echo xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
            } ?>
		  	</td>
			<td class="dataTableContent2"><?php echo $products['products_model']; ?>&nbsp;</td>
			<td class="dataTableContent2"><a href="categories.php?search=<? echo $products['products_name']; ?>" title="<?php echo TEXT_SHOW_PRODUCTS; ?>"><?php echo $products['products_name']; ?></a>
				<table border="0" width="100%" cellspacing="0" cellpadding="2">			
				<?php 
				// output options
				while ($attributes = xtc_db_fetch_array($attributes_query)) {
				?>
				<tr class="dataTableRow3" onMouseOver="this.className='dataTableRowOver3'" onMouseOut="this.className='dataTableRow3'">
				<td class="dataTableContent3" width="20%"><?php echo $attributes['products_options_name'].":&nbsp;"; ?></td>
				<td class="dataTableContent3" width="50%"><?php echo $attributes['products_options_values_name']; ?></td>
				<td class="dataTableContent3" align="right"><?php echo $attributes['attributes_stock']; ?>&nbsp;</td>			
				</tr>
				<?
				} // end while options
				?>
				</table>
			</td>
			<td class="dataTableContent2"><?php echo $products['products_quantity']; ?></td>
		</tr>
<?
 } // while end product loop
?>
			<tr>
				<td class="dataTableContent2" colspan="5">
					<table border="0" width="100%" cellspacing="0" cellpadding="2">
					  <tr>
						<td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, $showpage, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
						<td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, $showpage, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], 'search='.$_GET['search'].'&paging='.$_GET['paging'].'&sorting='.$_GET['sorting']); ?></td>
					  </tr>
					</table>
				</td>
			</tr>

          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- end content -->
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
require(DIR_WS_INCLUDES . 'application_bottom_0.php');
?>
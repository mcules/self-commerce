<?php
defined("_VALID_XTC") or die("Direct access to this location isn't allowed.");
require_once (DIR_FS_INC.'filter.inc.php');

function showFilterBox() {
    if(!isset($_GET['pID'])) {return '';}
    global $pInfo; //web28 - 2010-07-27 - show products_price
    // include localized categories specials strings
    include_lang_file(DIR_FS_LANGUAGES , $_SESSION['language'] , '/admin/categories_products_filters.php');

      // if editing an existing product
	$filters = Array();
	$filters_query = "	SELECT po.products_options_id, po.products_options_name, pov.products_options_values_name
					    FROM " . TABLE_PRODUCTS_OPTIONS . " po,
							" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov,
							" . TABLE_PRODUCTS_FILTER . " pf
						WHERE  pov.language_id = '" . (int)$_SESSION['languages_id'] . "'
						AND  po.language_id = pov.language_id
						AND pf.products_options_id = po.products_options_id
						AND pov.products_options_values_id = pf.products_options_values_id
						AND pf.products_id = '" . (int)$_GET['pID'] . "'
					ORDER BY po.products_options_sortorder,po.products_options_id";

     $filters_query = xtDBquery($filters_query);
	 $num_rows = xtc_db_num_rows($filters_query);
	 for($i=0;$i<$num_rows;$i++){
		$A = xtc_db_fetch_array($filters_query);
		$opt_id = $A['products_options_id'];
		if(!isset($filters[$opt_id])){
			$filters[$opt_id] = $A;
		}else{
			$filters[$opt_id]['products_options_values_name'].= ', '.$A['products_options_values_name'];
		}
	}
	$get_url_params = xtc_get_all_get_params(Array('opt','filter_load','__filter_','filter_id'));
	$get_url_params.= ($get_url_params=='' OR substr($get_url_params,-1)=='&')?'':'&';
	if(!isset($_GET['filter_load'])){
    ?>
<script language="JavaScript" type="text/JavaScript">
  function showFilter() {
	if ($("#filter").is(':hidden')) {
		$("#filter").show();
		$('#butFilter').html('<a href="JavaScript:showFilter()" class="button_aktiv">&laquo; Filter</a>');
	} else {
		$("#filter").hide();
		$('#butFilter').html('<a href="JavaScript:showFilter()" class="button">Filter &raquo;</a>');
	}
  }
  <?php
	if($num_rows > 0){
		echo '$(document).ready(function(){$("#butFilter").addClass("button_aktiv");});';
	}
  ?>
</script>
<style type='text/css'>#filter{display: none;}</style>
<noscript>
<style type="text/css">#filter{display: block;}</style>
</noscript>
  <div id="filter" style="clear:both;">
    <div style="padding: 8px 0px 3px 5px;">
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="main">
            <strong><?php echo FILTERS_TITLE; ?></strong>
          </td>
        </tr>
      </table>
    </div>
	<div id="filter_options">
	<?php } ?>
    <table bgcolor="f3f3f3" style="width: 100%; border: 1px solid; border-color: #aaaaaa; padding:5px;clear:both;">
      <tr>
        <td>
          <table width="100%" border="0" cellpadding="3" cellspacing="0" style="border: 0px dotted black;">
            <?php if(!isset($_GET['pID'])) { ?>
            <tr>
              <td class="main"><?php echo TEXT_SPECIALS_NO_PID; ?>&nbsp;</td>
            </tr>
            <?php } else { ?>
			<tr>
				<td class="main"><strong><?php echo TEXT_FITLERS_PRODUCTS_OPTIONS; ?></strong></td>
				<td class="main"><strong><?php echo TEXT_FITLERS_PRODUCTS_OPTIONS_VALUES; ?></strong></td>
			</tr>

			<?php foreach($filters AS $opt_id => $filters_array){ ?>
			<tr>
				<td class="main">
				<a href="<?php echo xtc_href_link(FILENAME_CATEGORIES, $get_url_params.'__filter_='.$filters_array['products_options_id'].'&filter_id='.$filters_array['products_options_id']);?>" class="thickbox ajax">
				<img src="<?php echo DIR_WS_IMAGES;?>icon_info.gif">
				<?php echo $filters_array['products_options_name']; ?>
				</a>:</td>
				<td class="main"><?php echo $filters_array['products_options_values_name']; ?></td>
			</tr>
			<?php } ?>

			<tr>
				<td><br>
				<?php
				echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL') . '" target="_blank"> -' . BOX_PRODUCTS_ATTRIBUTES . '</a>';
				?>
				</td>
				<td colspan="2" align="right">
					<a href="<?php echo xtc_href_link(FILENAME_CATEGORIES, $get_url_params.'__filter_=1&filter_id=1');?>" class="thickbox ajax">
					<div style="border: 1px solid #000; background-color: #DDD; height: 30px; width: 90px; text-align: center; float: right"><?php echo TEXT_SPECIALS_NEW_FILTER;?></div>
					</a>
					<div style="width: 10px; float: right">&nbsp;</div>
					<a href="<?php echo xtc_href_link(FILENAME_CATEGORIES, $get_url_params.'__filter_=2&filter_id=1');?>" class="thickbox ajax">
					<div style="border: 1px solid #000; background-color: #DDD; height: 30px; width: 70px; text-align: center; float: right">Link generieren</div>
					</a>
				</td>
			</tr>
            <?php } ?>
          </table>
        </td>
      </tr>
    </table>
	<?php if(!isset($_GET['filter_load'])){ ?>
	</div>
  </div>
<?php
}
}
?>
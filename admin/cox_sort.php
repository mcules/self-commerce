<?php
require('includes/application_top.php');
if ($_GET['sorting']) {
	xtc_db_query("UPDATE configuration SET configuration_value='".xtc_db_input($_GET['sorting'])."' WHERE configuration_key='CHECKOUT_BOX_ORDER'");
}
$box_order_query = xtc_db_query("SELECT configuration_value FROM configuration WHERE configuration_key='CHECKOUT_BOX_ORDER' LIMIT 1");
while ($row = xtc_db_fetch_array($box_order_query)) {
	$sorting = explode('|', $row['configuration_value']);
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="../lib/jquery/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="../lib/jquery-ui/sortable-1.8.2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#cox_order").sortable({});
	});

	function saveSorting() {
		if (window.location.href.indexOf("?") != -1) {
			var suffix = '&';
		} else {
			var suffix = '?';
		}
		var newSorting = [];
		$("input").each(function() {
			if (this.name == "sorting[]") newSorting.push(this.value);
		});
		window.location.href = window.location.href + suffix + 'sorting=' + newSorting.join("|");
	}
</script>
<style type="text/css">
ul.cox {
margin:0px;
padding:0px;
list-style:none;
}
ul.cox li {
margin:0px;
cursor:move;
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
list-style:none;
text-align:center;
margin-bottom:3px;
width:590px;
padding:10px;
border:1px #666 solid;
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
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?=HEADING_TITLE?></td>
            <td class="pageHeading" align="right"><?=xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT)?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td style="font-family:Verdana, Arial, Helvetica, sans-serif;"><?=HOWTO?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td>
            	<?php if (!empty($_GET['sorting'])) { ?>
				<div style="clear:both; padding:6px; width:590px; background-color: #eee; margin-bottom:20px; border:1px solid #333; color:green; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;"><strong><?=STORE_SUCCESS?></strong></div>
                <?php } ?>
            	<div style="clear:both;">
            		<ul id="cox_order" class="cox">
                    	<?php
                    	foreach ($sorting as $value) {
                    	$value = strtolower($value);
                    	?>
                        <?php if ($value == 'modules') { ?>
                    	<li style="height:60px; background-color:#FF6633;"><strong><?=MODULES?></strong><br /><?=MODULES_DESC?><input type="hidden" name="sorting[]" value="modules" /></li>
                        <?php } elseif ($value == 'addresses') { ?>
                        <li style="height:60px; background-color:#00CC33;"><strong><?=ADDRESSES?></strong><br /><?=ADDRESSES_DESC?><input type="hidden" name="sorting[]" value="addresses" /></li>
                        <?php } elseif ($value == 'products') { ?>
                        <li style="height:60px; background-color:#00CCFF;"><strong><?=PRODUCTS?></strong><br /><?=PRODUCTS_DESC?><input type="hidden" name="sorting[]" value="products" /></li>
                        <?php } elseif ($value == 'comments') { ?>
                        <li style="height:60px; background-color:#FFCC66;"><strong><?=COMMENTS?></strong><br /><?=COMMENTS_DESC?><input type="hidden" name="sorting[]" value="comments" /></li>
                        <?php } elseif ($value == 'legals') { ?>
                        <li style="height:100px; background-color:#9999CC;"><strong><?=LEGALS?></strong><br /><?=LEGALS_DESC?><input type="hidden" name="sorting[]" value="legals" /></li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
            	</div>
                <div style="clear:both; margin-top:10px;"><?=xtc_button_link(BUTTON_SAVE, 'javascript:saveSorting();')?></div>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
<?php
/* --------------------------------------------------------------
   $Id$
   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   Released under the GNU General Public License 
   --------------------------------------------------------------*/
require ('includes/application_top.php');

define('MAP_HEADING_TITLE', 'Google Map');			// Text ueber Karte
define('MAP_WIDTH', '100%');				// Breite der Karte
define('MAP_HEIGHT', '650');				// Hoehe der Karte
define('MAP_DETAIL_TXT', 'Edit');		// Info-Fenster
define('MAP_LOADING_TXT', 'Loading...');	// Text waehrend Karte geladen wird

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
    <title>
      <?php echo TITLE; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <style type="text/css">.h2 { font-family: Trebuchet MS,Palatino,Times New Roman,serif; font-size: 13pt; font-weight: bold; }.h3 { font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 9pt; font-weight: bold; }
    </style>
<!-- Google API //-->

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GOOGLEMAP_APIKEY; ?>"
      type="text/javascript"></script>
    <script type="text/javascript">

    //<![CDATA[

	var map = null;

	 // A function to create the marker and set up the event window
	 function createMarker(point, customers_id, wert)
	 {
		
		var icon = new GIcon();
		icon.image = "images/125.png";
		icon.shadow = "images/shadow50.png";
		icon.iconSize = new GSize(20, 34);
		icon.shadowSize = new GSize(37, 34);
		icon.iconAnchor = new GPoint(10, 34);
		icon.infoWindowAnchor = new GPoint(10, 10);

	   var marker = new GMarker(point, {icon:icon, title:customers_id});

		var html = "<a href=\"customers.php?cID=" + customers_id + "&action=edit\" target=\"_blank\"><?php echo MAP_DETAIL_TXT; ?> #" + customers_id + "</a>";
		
	   GEvent.addListener(marker, "click", function() {
	     marker.openInfoWindowHtml(html);
	   });

	   return marker;
	 }


    function load() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
		  map.addControl(new GLargeMapControl());
	     map.addControl(new GMapTypeControl());
	     map.addControl(new GOverviewMapControl());
	     map.addControl(new GScaleControl());
	     map.setCenter(new GLatLng(<?php echo MAP_CENTER_LAT; ?>, <?php echo MAP_CENTER_LNG; ?>), <?php echo MAP_CENTER_ZOOM; ?>);
		  map.enableScrollWheelZoom();	// Scrollrad-Zoom

		var pos = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(100,10));
      pos.apply(document.getElementById("control"));
      map.getContainer().appendChild(document.getElementById("control"));
		  
<?php
	$customers_query_raw = "select *  from customers_to_latlng";
	$customers_query = xtc_db_query($customers_query_raw);

	while ($customers = xtc_db_fetch_array($customers_query)) 
	{
		$name_query_raw = "select customers_lastname from customers WHERE customers_id=" . $customers['customers_id'] . "";
		$name_query = xtc_db_query($name_query_raw);
		$name = xtc_db_fetch_array($name_query);
		
		echo "var marker = createMarker(new GLatLng(" . $customers['lat'] . "," . $customers['lng'] . "), \"" . $customers['customers_id'] . "\", \"" . $customers['customers_id'] . "\"); \n";
		echo "map.addOverlay(marker);\n";
	}

?>

      }
    }

    //]]>
    </script>

<!-- Google API eof //-->    
  </head>
  <body onload="load()" onUnload="GUnload()">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="0">
      <tr>
        <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
          <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="0" class="columnLeft">
            <!-- left_navigation //-->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof //-->
          </table></td>
        <!-- body_text //-->
        <td class="boxCenter" width="100%" valign="top">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="80" rowspan="2">
                      <?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></td>
                    <td class="pageHeading">
                      Google Map</td>
                  </tr>
                  <tr>
                    <td class="main" valign="top">
                      powered by self-commerce.de</td>
                  </tr>
                </table></td>
            </tr>
          </table>
<!-- Body -->

		<div id="map" style="width: <?php echo MAP_WIDTH; ?>px; height: <?php echo MAP_HEIGHT; ?>px;border-style:solid;border-color:#000000; border-width:1px;"><?php echo MAP_LOADING_TXT; ?></div>
		<div id="control" style="background-color:#EEEEEE;border-style:solid;border-color:#333333; border-width:1px;">
		</div>
<!-- Body end -->         
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

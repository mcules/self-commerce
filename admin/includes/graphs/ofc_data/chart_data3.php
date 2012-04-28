<?php
include_once ('../../../includes/configure.php');

// sql abfrage des diagrammes
$db = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die("Could not connect");
mysql_select_db(DB_DATABASE,$db) or die("Could not select database");

$label = array();
$data = array();

 $orders_status_query = mysql_query("select orders_status_name, orders_status_id from orders_status where language_id = '2'");
  while ($orders_status = mysql_fetch_array($orders_status_query)) {
    $orders_pending_query = mysql_query("select count(*) as count from orders where orders_status = '" . $orders_status['orders_status_id'] . "'");
    $orders_pending = mysql_fetch_array($orders_pending_query);
    $label[] = $orders_status['orders_status_name'];
    $data[] = $orders_pending['count'];
  }      
      
// use the chart class to build the chart:
include_once( '../flash_chart/ofc_library/open-flash-chart.php' );

// generate some random data


$g = new graph();

//
// PIE chart, 60% alpha
//
$g->pie(60,'#505050','#000000');
//
// pass in two arrays, one of data, the other data labels
//
$g->pie_values( $data, $label );
//
// Colours for each slice, in this case some of the colours
// will be re-used (3 colurs for 5 slices means the last two
// slices will have colours colour[0] and colour[1]):
//
$g->pie_slice_colours( array('#d01f3c','#356aa0','#C79810') );

$g->set_tool_tip( 'Menge: #val#' );

$g->title( 'Bestellungen', '{font-size:18px; color: #d01f3c}' );
$g->bg_colour = '#FCFCFE';
echo $g->render();
?>

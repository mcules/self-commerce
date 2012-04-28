<?php
include_once ('../../../includes/configure.php');

// sql abfrage des diagrammes
$db = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die("Could not connect");
mysql_select_db(DB_DATABASE,$db) or die("Could not select database");

// meistbesuchte artikel
  $sql = "select p.products_id, pd.products_name, pd.products_viewed, l.name from products p, products_description pd, languages l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed DESC LIMIT 5";

$data = array();
$x_axis = array();

  $select = mysql_query($sql);
    while ($result = mysql_fetch_array($select)) {
      $data[] = $result['products_viewed'];
      $x_axis[] = $result['products_name'];
    }

// use the chart class to build the chart:
include_once( '../flash_chart/ofc_library/open-flash-chart.php' );

$g = new graph();
// Spoon sales, March 2007
$g->title( '5 meistbesuchten artikel' , '{font-size: 18px;}' );

$g->set_data( $data );
$g->bar( 50, '', '', 14 );
// label each point with its value

$g->set_x_labels( $x_axis );
$g->set_x_label_style( 10, '0x9933CC', 2 );
// set the Y max
$g->set_y_max( max($data) );
// label every 20 (0,20,40,60)
$g->y_label_steps( 4 );
// display the data
$g->bg_colour = '#FCFCFE';
echo $g->render();
?>

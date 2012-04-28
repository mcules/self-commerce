<?php
/* --------------------------------------------------------------
   $Id: orders_daily.php 279 2007-03-22 13:49:17Z mzanier $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banner_daily.php,v 1.2 2002/05/09); www.oscommerce.com 
   (c) 2003	 nextcommerce (banner_daily.php,v 1.6 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require(DIR_WS_CLASSES . 'phplot.php');

  $year = (date('Y'));
  $month = (date('n'));

  $days = (date('t'));
  $stats = array();
  for ($i=1; $i<$days; $i++) {
    $stats[] = array($i, '0', '0');
  }


  $orders_stats_query = xtc_db_query("SELECT sum(ot.value/o.currency_value) as value, dayofmonth(date_purchased) as day FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . " ot WHERE ot.orders_id = o.orders_id AND  ot.class = 'ot_total' and month(date_purchased) = '" . $month . "' and year(date_purchased) = '" . $year . "' GROUP BY day");
  
  
  while ($orders_stats = xtc_db_fetch_array($orders_stats_query)) {
    $stats[($orders_stats['day']-1)] = array($orders_stats['day'], (($orders_stats['value']) ? $orders_stats['value'] : '0'), (($orders_stats['value']) ? $orders_stats['value'] : '0'));
  }

  $graph = new PHPlot(600, 350, 'images/graphs/orders_daily.png');

  $graph->SetFileFormat('png');
  $graph->SetIsInline(1);
  $graph->SetPrintImage(0);
  //$graph->SetTitleFontSize('24');

  $graph->SetSkipBottomTick(1);
  $graph->SetDrawYGrid(1);
  $graph->SetPrecisionY(0);
  $graph->SetPlotType('bars');

  $graph->SetPlotBorderType('left');
  $graph->SetTitleFontSize('4');
  $graph->SetTitle(sprintf('Tagesbestellung', BOX_ORDERS, strftime('%B', mktime(0,0,0,$month)), $year));

  $graph->SetBackgroundColor('white');

  $graph->SetVertTickPosition('plotleft');
  $graph->SetDataValues($stats);
  $graph->SetDataColors(array('blue','red'),array('blue', 'red'));

  $graph->DrawGraph();

  $graph->PrintImage();
?>

<div class="data"><?php echo $product->getStocksData(array(
  'critical'  => __('Critical'),
  'correct'   => __('Correct'),
  'perfect'   => __('Good'),
), true) ?></div>
<div class="jqplot" id="jqplot_stocks_<?php echo $product->id ?>"></div>

<?php use_stylesheet('/js/jqplot/jquery.jqplot.css') ?>
<?php use_stylesheet('jqplot') ?>
<?php use_javascript('jqplot') ?>
<?php use_javascript('/js/jqplot/jquery.jqplot.js') ?>
<?php use_javascript('/js/jqplot/jqplot.axisLabelRenderer.js') ?>
<?php use_javascript('/js/jqplot/jqplot.axisTickRenderer.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.categoryAxisRenderer.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.pointLabels.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.barRenderer.js') ?>
<?php use_javascript('pos-stocks.lib.js') ?>

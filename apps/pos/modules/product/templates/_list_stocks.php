<div class="data"><?php echo $product->getStocksData(array(
  'critical'  => __('Critical'),
  'correct'   => __('Correct'),
  'perfect'   => __('Good'),
), true) ?></div>
<div class="jqplot" id="jqplot_stocks_<?php echo $product->id ?>"></div>

<?php include_partial('global/assets_jqplot') ?>
<?php use_javascript('pos-stocks.lib.js') ?>

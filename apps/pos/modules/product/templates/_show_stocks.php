<?php use_javascript('pos-stocks') ?>

<?php include_partial('global/graph_jqplot',array(
  'id' => 'stocks',
  'data' => '',
  'label' => __('Stocks'),
  'name' => $form->getObject()->name,
)) ?>

<?php use_javascript('/js/jqplot/plugins/jqplot.categoryAxisRenderer.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.pointLabels.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.barRenderer.js') ?>

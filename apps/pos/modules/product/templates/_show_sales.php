<?php use_javascript('pos-stocks') ?>

<?php include_partial('global/graph_jqplot', array(
  'id'    => 'sales',
  'data'  => url_for('product/salesEvolution?id='.$form->getObject()->id),
  'label' => __('Sales evolution'),
  'width' => '100%',
)) ?>

<?php use_javascript('helper') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.dateAxisRenderer.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.cursor.js') ?>

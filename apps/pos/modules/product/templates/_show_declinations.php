<?php use_javascript('pos-stocks') ?>

<?php include_partial('global/graph_jqplot', array(
  'id'    => 'declinations',
  'data'  => url_for('product/declinationsTrends?id='.$form->getObject()->id),
  'label' => __('Declinations trends'),
  'name' => $form->getObject()->name,
)) ?>

<?php use_javascript('helper') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.pieRenderer.js') ?>

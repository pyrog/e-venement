<?php use_javascript('pos-stocks') ?>

<?php include_partial('global/graph_jqplot',array(
  'id' => 'stocks',
  'data' => '',
  'label' => __('Stocks'),
  'name' => $form->getObject()->name,
)) ?>

<?php use_javascript('pos-stocks') ?>

<?php include_partial('global/graph_jqplot',array(
  'id' => 'stocks',
  'data' => '',
  'label' => __('Stocks'),
  'name' => $form->getObject()->name,
)) ?>

<div style="display: none" class="i18n">
  <span class="good"><?php echo __('Good') ?></span>
  <span class="correct"><?php echo __('Correct') ?></span>
  <span class="critical"><?php echo __('Critical') ?></span>
</div>

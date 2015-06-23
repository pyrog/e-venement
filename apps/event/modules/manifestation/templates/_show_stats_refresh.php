<div class="tab-print">
<?php echo link_to(
  '<span class="ui-icon ui-icon-arrowrefresh-1-s"></span> '.__('Refresh'),
  'manifestation/statsFillingData?id='.$form->getObject()->id.'&refresh=true',
  array('class' => 'fg-button ui-state-default fg-button-icon-left')
) ?>
</div>

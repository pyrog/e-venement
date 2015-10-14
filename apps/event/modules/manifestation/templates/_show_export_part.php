<p class="tab-export">
  <a class="fg-button fg-button-icon-left ui-state-default ui-priority-secondary" href="<?php echo url_for('manifestation/export?status=printed&id='.$manifestation_id) ?>" target="_blank" title="<?php echo __('Export') ?>">
    <span class="ui-icon ui-icon-person"></span>
    <?php echo __('With ticket') ?>
  </a>
  <a class="fg-button fg-button-icon-left ui-state-default ui-priority-secondary" href="<?php echo url_for('manifestation/export?status=ordered&id='.$manifestation_id) ?>" target="_blank" title="<?php echo __('Export') ?>">
    <span class="ui-icon ui-icon-person"></span>
    <?php echo __('Reservations') ?>
  </a>
  <?php if ( sfConfig::get('project_tickets_count_demands',false) ): ?>
  <a class="fg-button fg-button-icon-left ui-state-default ui-priority-secondary" href="<?php echo url_for('manifestation/export?status=asked&id='.$manifestation_id) ?>" target="_blank" title="<?php echo __('Export') ?>">
    <span class="ui-icon ui-icon-person"></span>
    <?php echo __('Demands') ?>
  </a>
  <?php endif ?>
</p>

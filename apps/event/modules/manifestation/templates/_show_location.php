<div class="sf_admin_form_row sf_admin_form_field_location">
  <label><?php echo __('Location') ?>:</label>
  <?php if ( $v = $manifestation->current_version ): ?>
  <span class="diff">
    <?php echo link_to($loc = Doctrine::getTable('Location')->findOneById($v->location_id), 'location/show?id='.$loc->id); ?>
  </span>
  <?php endif ?>
  <?php echo link_to($manifestation->Location,'location/show?id='.$manifestation->Location->id) ?>
</div>

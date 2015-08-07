<?php use_javascript('helper') ?>
<div class="sf_admin_form_row sf_admin_button sf_admin_form_field_save_button">
  <label for="email-save-button"><?php echo __('Save the content as a model') ?></label>
  <div class="label ui-helper-clearfix"></div>
  <input type="text" maxlength="255" name="email-save-name"
    data-url="<?php echo url_for('email/saveTemplate',true) ?>"
    data-msg-success="<?php echo __('The item was created successfully.', null, 'sf_admin') ?>"
    data-msg-error="<?php echo __('The item has not been saved due to some errors.', null, 'sf_admin') ?>"
  />
  <button type="submit" name="email-save-button" value="load" id="email-save-button" class="fg-button ui-state-default fg-button-icon-left">
    <span class="ui-icon ui-icon-circle-check"></span>
    <?php echo __('Save',null,'sf_admin') ?>
  </button>
</div>

<?php $name = 'load'; $label = 'Load and replace content'; $help = ''; ?>
<div class="sf_admin_form_row sf_admin_button sf_admin_form_field_load_button <?php echo isset($class) ? $class : '' ?><?php $form[$name]->hasError() and print ' ui-state-error ui-corner-all' ?>">
  <?php echo $form[$name]->renderLabel($label) ?>
  <div class="label ui-helper-clearfix"></div>
  <?php if ($help || $help = $form[$name]->renderHelp()): ?>
    <div class="help">
      <span class="ui-icon ui-icon-help floatleft"></span>
      <?php echo __(strip_tags($help), array(), 'messages') ?>
    </div>
  <?php endif; ?>
  <?php echo $form[$name]->render($attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes) ?>
  <?php if ($form[$name]->hasError()): ?>
    <div class="errors">
      <span class="ui-icon ui-icon-alert floatleft"></span>
      <?php echo $form[$name]->renderError() ?>
    </div>
  <?php endif; ?>
  <button type="submit" name="email-load-button" value="load" id="email-load-button" class="fg-button ui-state-default fg-button-icon-left">
    <span class="ui-icon ui-icon-circle-check"></span>
    <?php echo __('Load',null,'sf_admin') ?>
  </button>
</div>



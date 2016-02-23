<?php if ( !isset($form['domain']) ): ?>
<?php else: ?>
  <?php $widget = $form['domain']; ?>
  <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_domain <?php $widget->hasError() and print ' ui-state-error ui-corner-all li-nb-errors-1' ?>">
    <?php echo $widget->renderLabel($label) ?>
    <div class="label ui-helper-clearfix">
    
      <?php if ($help || $help = $widget->renderHelp()): ?>
        <div class="help">
          <span class="ui-icon ui-icon-help floatleft"></span>
          <?php echo __(strip_tags($help), array(), 'messages') ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="widget <?php echo $culture !== 0 ? 'culture-'.$culture.'" title="'.$langs[$culture].'"' : '"' ?>>
      <?php echo $widget->render($attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes) ?>
      .<?php echo sfConfig::get('project_internals_users_domain', '') ?>
      <?php if ( $culture !== 0 ): ?>
      <span class="culture"><?php echo $culture ?></span>
      <span class="lang culture-<?php echo $culture ?>"><?php echo $langs[$culture] ?></span>
      <?php endif ?>
  
      <?php if ($widget->hasError()): ?>
        <div class="errors">
          <span class="ui-icon ui-icon-alert floatleft"></span>
          <?php echo $widget->renderError() ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

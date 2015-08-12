<div class="label ui-helper-clearfix">
  <div class="help">
    <span class="ui-icon ui-icon-help floatleft"></span>
    <?php echo __('Assigns a member card of the type given to all members of this group (with the exception of organisms)') ?>
  </div>
</div>

<form method="get" action="" class="ui-widget-content ui-corner-all">
  <?php if ( $member_card_types->count() > 0 ): ?>
  <ul>
    <?php foreach ( $member_card_types as $mct ): ?>
    <li>
      <input type="checkbox" name="member_card_types[]" id="mct-<?php echo $mct->id ?>" value="<?php echo $mct->id ?>" />
      <label for="mct-<?php echo $mct->id ?>">
        <?php echo $mct ?>
        <span class="desc"><?php echo $mct->description ?></span>
      </label>
    </li>
    <?php endforeach ?>
  </ul>
  <p><button type="submit" name="submit" value="" class="fg-button ui-state-default fg-button-icon-left">
    <?php echo __('Validate', null, 'sf_admin') ?>
    <span class="ui-icon ui-icon-circle-check"></span>
  </button><input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?>" /></p>
  <?php endif ?>
  <div class="clear"></div>
</form>

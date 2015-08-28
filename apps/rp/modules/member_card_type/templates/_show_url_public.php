<div class="sf_admin_form_row sf_admin_number sf_admin_form_field_show_url_public">
  <label for="url_public"><?php echo __('Public URL that adds automatically a member card of this type into the cart') ?></label>
  <div class="label ui-helper-clearfix"></div>
  <div class="widget">
    <input size="95" readonly="readonly" name="url_public" value="<?php echo str_replace('https://', 'http://', cross_app_url_for('pub', 'card/order', true).'?member_card_type['.$form->getObject()->id.']=1&append') ?>" id="public_url" type="text">
  </div>
</div>

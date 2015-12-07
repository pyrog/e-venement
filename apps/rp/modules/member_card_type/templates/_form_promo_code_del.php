<?php if ( $sf_user->hasCredential('pr-card-promo-del') ): ?>
<a class="sf_admin_form_field_promo_code_del" href="<?php echo url_for('promo_code/deleteSimple') ?>">
  <?php echo __('Delete', null, 'sf_admin') ?>
</a>
<?php endif ?>

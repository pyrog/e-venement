<?php if ( sfConfig::get('app_member_cards_promo_code', false) ): ?>
<form action="<?php echo url_for('card/addPromoCode') ?>" method="post" id="promo-code">
  <p>
    <label for="promo_code_text"><?php echo __('Promo Code') ?>:</label>
    <input type="text" value="" id="promo_code_text" name="promo-code" size="10" />
    <input type="submit" name="submit" value="<?php echo __('Add') ?>" />
    <input type="hidden" name="redirect" value="<?php echo $_SERVER['PHP_SELF'] ?>" />
  </p>
</form>
<?php endif ?>

<?php if ( !$sf_user->hasCredential('pr-card-promo-mod') && $sf_user->hasCredential('pr-card-promo') ): ?>
  <ol>
  <?php foreach ( $form->getObject()->PromoCodes as $promo ): ?>
    <li data-id="<?php echo $promo->id ?>">
      <span class="code"><?php echo $promo ?></span>
      <?php if ( $promo->begins_at || $promo->ends_at ): ?>
      <br/>
      <span class="interval">
        <?php echo $promo->begins_at ? format_datetime($promo->begins_at) : '' ?>
        →
        <?php echo $promo->ends_at ? format_datetime($promo->ends_at) : '' ?>
      </span>
      <?php endif ?>
    </li>
  <?php endforeach ?>
  </ol>
<?php endif ?>

<div class="declination" id="declination-<?php echo $declination->id ?>" data-declination-id="<?php echo $declination->id ?>">
  <h3><?php echo $declination ?></h3>
  <div class="text">
    <?php echo $declination->getRawValue()->description ?>
  </div>
  <?php
    $max = $declination->stock - $declination->Product->online_limit;
    foreach ( $sf_user->getTransaction()->BoughtProducts as $bp )
    if ( $bp->product_declination_id == $declination->id )
      $max++;
  ?>
  <?php if ( $max > 0 ): ?>
    <?php include_partial('show_prices',array(
      'declination' => $declination,
      'max' => $max > $declination->Product->online_limit_per_transaction ? $declination->Product->online_limit_per_transaction : $max,
    )) ?>
  <?php else: ?>
    <div class="nomore"><?php echo pubConfiguration::getText('app_texts_store_nomore', __('Unavailable.')) ?></div>
  <?php endif ?>
</div>

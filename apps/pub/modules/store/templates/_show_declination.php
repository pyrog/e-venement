<div class="declination" id="declination-<?php echo $declination->id ?>" data-declination-id="<?php echo $declination->id ?>">
  <h3><?php echo $declination ?></h3>
  <div class="text">
    <?php echo $declination->getRawValue()->description ?>
  </div>
  <?php if ( ($max = $declination->stock - $declination->Product->online_limit) > 0 ): ?>
    <?php include_partial('show_prices',array(
      'declination' => $declination,
      'max' => $max > $declination->Product->online_max_per_transaction ? $declination->Product->online_max_per_transaction : $max,
    )) ?>
  <?php else: ?>
    <div class="nomore">No more</div>
  <?php endif ?>
</div>

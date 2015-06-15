<p class="contact">
  <?php if ( $professional ): ?>
    <?php echo cross_app_link_to($professional->Contact,'rp','contact/show?id='.$professional->Contact->id) ?>
    (<?php echo cross_app_link_to($professional->Organism,'rp','organism/show?id='.$professional->Organism->id) ?>
    -
    <?php echo $professional->name ?>)
    <span class="picto"><?php echo $sf_data->getRaw('professional')->groups_picto ?></span>
  <?php else: ?>
    &nbsp;
  <?php endif ?>
</p>

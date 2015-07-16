<?php use_helper('Date') ?>
<h1><?php echo __('Choose tickets') ?></h1>
<div id="event"><?php echo $manifestation->Event ?></div>
<?php if ( $manifestation->depends_on ): ?>
<div id="depends_on">+ <?php echo $manifestation->DependsOn->Event ?></div>
<?php endif ?>
<div id="manifestation"><?php echo __('on') ?> <?php echo $manifestation->getFormattedDate() ?></div>
<div id="location"><?php echo __('location') ?> : <?php echo $manifestation->Location ?></div>

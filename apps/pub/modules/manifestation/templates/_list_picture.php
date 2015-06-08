<?php echo $manifestation->Event->getRawValue()->Picture->render(array('app' => 'pub')) ?>
<span
  data-manifestation-id="<?php echo $manifestation->id ?>"
  data-event-id="<?php echo $event->id ?>"
></span>

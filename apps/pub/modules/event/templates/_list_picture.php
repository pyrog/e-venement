<?php if ( $event->picture_id ): ?>
<?php echo link_to($event->getRawValue()->Picture->render(array('app' => 'pub')), 'event/edit?id='.$event->id) ?>
<?php endif ?>

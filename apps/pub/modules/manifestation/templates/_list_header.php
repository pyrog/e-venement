<?php include_partial('global/ariane',array('active' => 1)) ?>
<?php include_partial('event/description', array('manifestations' => $pager->getResults(), 'event' => $filters)) ?>
<?php
  $ev = array();
  foreach ( $pager->getResults() as $manifestation )
  if ( !isset($ev[$manifestation->event_id]) )
    $ev[$manifestation->event_id] = $manifestation->Event;
?>
<?php if ( count($ev) == 1 ): ?>
<h1 class="event-title"><?php echo array_pop($ev) ?></h1>
<?php endif ?>

<?php include_partial('global/ariane',array('active' => 1)) ?>
<?php $manifestations = $pager->getResults() ?>
<?php
  $ev = array();
  foreach ( $pager->getResults() as $manifestation )
  if ( !isset($ev[$manifestation->event_id]) )
    $ev[$manifestation->event_id] = $manifestation->Event;
?>
<?php if ( count($ev) == 1 ): ?>
<h1 class="event-title"><?php echo array_pop($ev) ?></h1>
<?php endif ?>
<?php if ( sfConfig::get('app_options_home', 'event') == 'meta_event' ): ?>
  <div id="meta_event">&laquo;&nbsp;<?php echo link_to($manifestations[0]->Event->MetaEvent, 'event/index?meta-event='.$manifestations[0]->Event->MetaEvent->slug) ?></div>
<?php endif ?>
<?php include_partial('event/description', array('manifestations' => $manifestations, 'event' => $filters)) ?>

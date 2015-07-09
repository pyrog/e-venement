<?php use_helper('Number') ?>
<?php
  $q = Doctrine_Query::create()->from('EntryTickets et')
    ->select('et.*, ee.id, me.id')
    ->leftJoin('et.EntryElement ee')
    ->leftJoin('ee.ContactEntry ce')
    ->leftJoin('ee.ManifestationEntry me')
    ->leftJoin('me.Manifestation m')
    ->leftJoin('m.Event e')
    ->andWhereIn('e.meta_event_id',array_keys($sf_data->getRaw('sf_user')->getMetaEventsCredentials()))
    ->andWhere('ce.professional_id = ?',$professional->id)
    ->andWhere('ee.accepted = ?',true)
  ;
  
  $filters = $sf_user->getRawValue()->getAttribute('professional.filters', null, 'admin_module');
  if ( isset($filters['grp_meta_events_list']) && $filters['grp_meta_events_list'] )
    $q->andWhereIn('e.meta_event_id', $filters['grp_meta_events_list']);
  if ( isset($filters['grp_events_list']) && $filters['grp_events_list'] )
    $q->andWhereIn('e.id', $filters['grp_events_list']);
  
  $nb = 0;
  $mids = array();
  foreach ( $q->execute() as $tickets )
  {
    $mids[$tickets->EntryElement->ManifestationEntry->id] = 1;
    $nb += $tickets->quantity;
  }
  
  if ( count($mids) > 0 )
    echo format_number(round($nb/count($mids),1));
?>

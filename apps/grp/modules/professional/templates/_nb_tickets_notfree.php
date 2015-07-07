<?php use_helper('Number') ?>
<?php
  $q = Doctrine_Query::create()->from('EntryTickets et')
    ->select('et.*, ee.id, me.id, p.id, pm.id, pm.value, pm.manifestation_id')
    ->leftJoin('et.Price p')
    ->leftJoin('et.EntryElement ee')
    ->leftJoin('ee.ContactEntry ce')
    ->leftJoin('ee.ManifestationEntry me')
    ->leftJoin('me.Manifestation m')
    ->leftJoin('p.PriceManifestations pm ON p.id = pm.price_id AND m.id = pm.manifestation_id')
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
    foreach ( $tickets->Price->PriceManifestations as $pm )
    if ( $pm->manifestation_id == $tickets->EntryElement->ManifestationEntry->manifestation_id
      && $pm->value > 0 )
    {
      $nb += $tickets->quantity;
      break;
    }
  }
  
  if ( count($mids) > 0 )
    echo format_number(round($nb/count($mids),1));
  //print_r($sf_user->filters->getValue('grp_meta_events_list'));
?>

<?php

require_once dirname(__FILE__).'/../lib/resourceGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/resourceGeneratorHelper.class.php';

/**
 * location actions.
 *
 * @package    e-venement
 * @subpackage resource
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class resourceActions extends autoResourceActions
{
  public function executeCalendar(sfWebRequest $request)
  {
    $this->executeEdit($request);
  }

  public function executeNewManif(sfWebRequest $request)
  {
    // preconditions
    if ( $request->getParameter('event_name',$params = $request->getParameter('event', false)) )
    {
      if ( $request->getParameter('id') )
        $this->executeEdit($request);
      
      if ( !is_array($ids = $request->getParameter('ids',isset($this->location) ? array($this->location->id) : array())) || !$ids )
        throw new liEvenementException('Resource ids are not set properly');
      
      if ( $request->getParameter('event_name',false) ) // if we used the classical crappy way
      {
        $event = new Event;
        $event->name = $request->getParameter('event_name');
        $me = array_keys($this->getUser()->getMetaEventsCredentials());
        $event->meta_event_id = $me[0];
        $event->save();
      }
      else // if we used a special EventForm
      {
        $this->form = new EventForm;
        $this->form->removeManifestations();
        $this->form->bind($params);
        if ( !$this->form->isValid() )
        {
          $this->getUser()->setFlash('error', __('The item has not been saved due to some errors.', null, 'sf_admin'));
          $this->redirect('resource/index');
        }
        
        $event = $this->form->save();
      }
      
      $this->getUser()->setFlash('booking_list', $ids);
      $this->redirect('manifestation/new?event='.$event->slug);
    }
    
    // precondition for "batch" exec
    if (! ($this->ids = $this->getUser()->getFlash('resource_ids') ))
    {
      $this->getUser()->setFlash('You must at least select one item.',null,'sf_admin');
      $this->redirect('resource/index');
    }
    
    $this->form = new EventForm;
    $this->form->useFields(array('name', 'meta_event_id'));
  }
  
  public function executeBatchNewManif(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');
    
    $this->getUser()->setFlash('resource_ids',$ids);
    $this->redirect('resource/newManif');
  }
}
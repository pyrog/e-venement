<?php

/**
 * ticket actions.
 *
 * @package    symfony
 * @subpackage ticket
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ticketActions extends sfActions
{
  public function preExecute()
  {
    $this->dispatcher->notify(new sfEvent($this, 'pub.pre_execute', array('configuration' => $this->configuration)));
    parent::preExecute();
  }
  
  public function executeGetOrphans(sfWebRequest $request)
  {
    $options = array();
    foreach ( array('gauge_id', 'manifestation_id', 'seat_id', 'ticket_id') as $field )
      $options['$field'] = $request->getParameter($field, 'false');
    
    $this->debug($request);
    $this->json = array('error' => false, 'success' => false);
    $manif_details = true;
    
    try { $this->json['success']['orphans'] = $this->getContext()->getConfiguration()->getOrphans($this->getUser()->getTransaction(), $options); }
    catch ( liOnlineSaleException $e )
    { return $this->jsonError($e->getMessage(), $request); }
    
    $flat = array();
    foreach ( $this->json['success']['orphans'] as $gid => $data )
    foreach ( $data as $orphan )
      $flat[] = $orphan['seat_name'];
    
    $this->getContext()->getConfiguration()->loadHelpers('I18N');
    $this->json['success']['message'] = count($flat) == 0
      ? __('Perfect, no orphans found!')
      : __('You need to do something to avoid those orphans (%%orphans%%)...', array('%%orphans%%' => implode(', ', $flat)))
    ;
    
    return 'Success';
  }
  public function executeAutoSeating(sfWebRequest $request)
  {
    return require(dirname(__FILE__).'/auto-seating.php');
  }
  
  public function executeAddCategorizedTicket(sfWebRequest $request)
  {
    return require(dirname(__FILE__).'/add-categorized-ticket.php');
  }
  public function executeModTickets(sfWebRequest $request)
  {
    return require(dirname(__FILE__).'/mod-tickets.php');
  }
  public function executeModNamedTickets(sfWebRequest $request)
  {
    return require(dirname(__FILE__).'/mod-named-tickets.php');
  }
  public function executeAutoAdd(sfWebRequest $request)
  {
    foreach ( $request->getParameter('tickets', array()) as $ticket )
    {
      $q = Doctrine::getTable('Gauge')->createQuery('g')
        ->leftJoin('g.Manifestation m')
        ->andWhere('m.event_id = ?', $ticket['event_id'])
        ->orderBy('m.happens_at ASC, gauge.value DESC');
      $gauge = false;
      foreach ( $q->execute() as $gauge )
      if ( $gauge->free >= $ticket['quantity'] )
        break;
      
      if ( !$gauge )
        return sfView::NONE;
      
      $nb = intval($ticket['quantity']);
      $tickets = array();
      for ( $i = 0 ; $i < $nb ; $i++ )
      $tickets[] = array(
        'gauge_id' => $gauge->id,
        'price_id' => $ticket['price_id'],
        'action'   => 'add',
      );
      
      $request->setParameter('tickets', $tickets);
      $request->setParameter('manifestation_id', $gauge->manifestation_id);
      $this->executeModTickets($request);
      $this->getUser()->getTransaction(true);
    }
    return sfView::NONE;
  }
  
  // used only for manifestations list's inline-ticketting
  public function executeCommit(sfWebRequest $request)
  {
    return require(dirname(__FILE__).'/commit.php');
  }
  
  protected function checkForOrphansInJson(array $options)
  {
  }
  
  protected function getMaxPerManifestation(Manifestation $manifestation)
  {
    $sf_user = $this->getUser();
    
    // limitting the max quantity, especially for prices linked to member cards
    $vel = sfConfig::get('app_tickets_vel');
    $vel['max_per_manifestation'] = isset($vel['max_per_manifestation']) ? $vel['max_per_manifestation'] : 9;
    if ( $manifestation->online_limit_per_transaction && $manifestation->online_limit_per_transaction < $vel['max_per_manifestation'] )
      $vel['max_per_manifestation'] = $manifestation->online_limit_per_transaction;
    
    // max per manifestation per contact ...
    $vel['max_per_manifestation_per_contact'] = isset($vel['max_per_manifestation_per_contact']) ? $vel['max_per_manifestation_per_contact'] : false;
    if ( $vel['max_per_manifestation_per_contact'] > 0 )
    {
      $max = $vel['max_per_manifestation_per_contact'];
      foreach ( $sf_user->getContact()->Transactions as $transaction )
      if ( $transaction->id != $sf_user->getTransaction()->id )
      foreach ( $transaction->Tickets as $ticket )
      if (( $ticket->transaction_id == $sf_user->getTransaction()->id || $ticket->printed_at || $ticket->integrated_at || $transaction->Order->count() > 0 )
        && !$ticket->hasBeenCancelled()
        && $manifestation->id == $ticket->manifestation_id
      )
      {
        $vel['max_per_manifestation_per_contact']--;
      }
      $vel['max_per_manifestation'] = $vel['max_per_manifestation'] > $vel['max_per_manifestation_per_contact']
        ? $vel['max_per_manifestation_per_contact']
        : $vel['max_per_manifestation'];
    }
    
    return $vel['max_per_manifestation'];
  }
  protected function jsonError($messages = array(), sfWebRequest $request)
  {
    if ( !is_array($messages) )
      $messages = array($messages);
    $this->json['error']['message'] = $messages;
    
    error_log('app: pub, module: ticket --> '.implode(' | ', $messages));
    $this->debug($request);
    return 'Error';
  }
  protected function debug(sfWebRequest $request, $no_get_param = false)
  {
    $this->raw_debug(sfConfig::get('sf_web_debug', false) && ($no_get_param || $request->hasParameter('debug')));
  }
  protected function raw_debug($bool)
  {
    if ( $bool )
    {
      $this->debug = true;
      $this->getResponse()->setContentType('text/html');
      $this->getResponse()->sendHttpHeaders();
      $this->setLayout(sfConfig::get('app_options_template', 'public'));
    }
    else
      sfConfig::set('sf_web_debug', false);
  }
}

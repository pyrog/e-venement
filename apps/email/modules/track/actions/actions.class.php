<?php

/**
 * track actions.
 *
 * @package    e-venement
 * @subpackage track
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class trackActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $q = Doctrine::getTable('Email')->createQuery('e')
      ->andWhere('e.id = ?', $request->getParameter('i',false))
    //  ->leftJoin('e.Tracks t')
    //  ->andWhere('t.detail != ?', $request->getParameter('s',''))
    ;
    $this->forward404Unless($this->email = $q->fetchOne());
    
    $ea = new EmailAction;
    
    $ea->email_id = $this->email->id;
    $ea->type = 'open';
    $ea->detail = $request->getParameter('s', '--');
    $ea->email_address = $request->getParameter('e','');
    
    if ( intval($request->getParameter('i')).'' === ''.$request->getParameter('i') )
    switch ( $request->getParameter('t') ) {
    case 'c':
      $ea->contact_id = $request->getParameter('i');
      break;
    case 'p':
      $ea->professional_id = $request->getParameter('i');
      break;
    case 'o':
      $ea->organism_id = $request->getParameter('i');
      break;
    }
    
    $ea->save();
    
    return sfView::NONE;
  }
}

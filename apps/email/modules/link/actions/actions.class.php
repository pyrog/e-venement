<?php

/**
 * link actions.
 *
 * @package    e-venement
 * @subpackage link
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class linkActions extends sfActions
{
  public function executeFollow(sfWebRequest $request)
  {
    $q = Doctrine::getTable('EmailExternalLink')->createQuery('el')
      ->andWhere('el.encrypted_uri = ?',$request->getParameter('u',false));
    $this->forward404Unless($this->link = $q->fetchOne());
    
    $ea = new EmailAction;
    $ea->email_id = $this->link->email_id;
    $ea->type = 'link';
    $ea->detail = $this->link->original_url;
    $ea->source = $_SERVER['REQUEST_URI'];
    $ea->email_address = $request->getParameter('e');
    
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
    
    $this->redirect($this->link->original_url);
  }
  
  public function executeError404(sfWebRequest $request)
  {
  }
}

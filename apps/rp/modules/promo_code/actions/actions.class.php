<?php

require_once dirname(__FILE__).'/../lib/promo_codeGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/promo_codeGeneratorHelper.class.php';

/**
 * promo_code actions.
 *
 * @package    e-venement
 * @subpackage promo_code
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class promo_codeActions extends autoPromo_codeActions
{
  public function executeDeleteSimple(sfWebRequest $request)
  {
    $this->forward404Unless($promo = Doctrine::getTable('MemberCardTypePromoCode')->find($request->getParameter('promo_code_id', 0)) );
    $promo->delete();
    return sfView::NONE;
  }
  
  public function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    if ( $request->hasParameter('mct_id') )
      $this->form->setDefault('member_card_type_id', $request->getParameter('mct_id'));
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $this->executeEdit($request);
  }
  public function executeEdit(sfWebRequest $request)
  {
    parent::executeEdit($request);
    $this->redirect('member_card_type/edit?id='.$this->member_card_type_promo_code->member_card_type_id.'#sf_fieldset_promo_codes');
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    if ( $request->hasParameter('mct_id') )
      $this->redirect('member_card_type/edit?id='.$request->getParameter('mct_id').'#sf_fieldset_promo_codes');
    parent::executeIndex($request);
  }
}

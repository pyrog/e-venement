<?php

/**
 * MemberCardTypePromoCode form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MemberCardTypePromoCodeForm extends BaseMemberCardTypePromoCodeForm
{
  /**
   * @see TraceableForm
   */
  public function configure()
  {
    parent::configure();
    
    foreach ( array(
      'sf_guard_user_id',
      'automatic',
      'version',
      'member_card_type_id',
    ) as $field )
      $this->widgetSchema[$field] = new sfWidgetFormInputHidden;
    
    $this->widgetSchema['name']->setLabel('Code')->setAttribute('class', 'promo-code-name');
    $this->widgetSchema['id']->setAttribute('class', 'promo-code-id');
    $this->widgetSchema['description'] = new sfWidgetFormTextarea;
  }
}

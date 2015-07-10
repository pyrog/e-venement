<?php

/**
 * Workspace form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class WorkspaceForm extends BaseWorkspaceForm
{
  public function configure()
  {
    unset(
      $this->widgetSchema['manifestations_list'],
      $this->widgetSchema['seated_plans_list'],
      $this->widgetSchema['linked_products_list']
    );
    
    $this->widgetSchema['users_list']->setOption('expanded', 'true')
      ->setOption('order_by', array('u.username', ''))
      ->setOption('query', $q = Doctrine::getTable('sfGuardUser')->createQuery('u'))
    ;
    if ( !$this->object->isNew() )
      $q->andWhere('ws.id IS NOT NULL AND ws.id = ? OR u.is_active = ?', array($this->object->id, true));
    else
      $q->andWhere('u.is_active = ?', true);
    
    $this->widgetSchema['prices_list']->setOption('expanded',true)
      ->setOption('order_by', array('pt.name', ''));
    
    if ( !sfContext::getInstance()->getUser()->hasCredential('event-seated-plan') )
      $this->widgetSchema['seated'] = new sfWidgetFormInputHidden;
  }
}

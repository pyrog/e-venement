<?php

/**
 * MemberCardPriceModel filter form.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MemberCardPriceModelFormFilter extends BaseMemberCardPriceModelFormFilter
{
  /**
   * @see TraceableFormFilter
   */
  public function configure()
  {
    parent::configure();
    
    if ( !sfContext::hasInstance() )
      return;
    $this->user = sfContext::getInstance()->getUser();
    
    $this->widgetSchema   ['event_id']->setOption('query', Doctrine::getTable('Event')->createQuery('e')
      ->andWhereIn('e.meta_event_id', array_keys($this->user->getMetaEventsCredentials()))
    );
    $this->validatorSchema['event_id']->setOption('query', $this->widgetSchema['event_id']->getOption('query'));
    
    $this->widgetSchema   ['meta_events_list'] = new sfWidgetFormDoctrineChoice(array(
      'multiple' => true,
      'model'    => 'MetaEvent',
      'order_by' => array('name',''),
    ));
    $this->validatorSchema['meta_events_list'] = new sfValidatorDoctrineChoice(array(
      'multiple' => true,
      'model'    => 'MetaEvent',
    ));
  }
  
  public function addMetaEventsListColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( !$value )
      return $q;
    if ( !is_array($value) )
      $value = array($value);
    
    $a = $q->getRootAlias();
    $q->leftJoin("$a.Event e")
      ->andWhereIn('e.meta_event_id', $value);
    
    return $q;
  }
}

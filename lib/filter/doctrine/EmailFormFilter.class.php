<?php

/**
 * Email filter form.
 *
 * @package    e-venement
 * @subpackage filter
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class EmailFormFilter extends BaseEmailFormFilter
{
  public function configure()
  {
    // organism
    $this->widgetSchema['organisms_list'] = new cxWidgetFormDoctrineJQuerySelectMany(array(
      'model' => 'Organism',
      'url'   => url_for('organism/ajax'),
      'order_by' => array('name',''),
    ));
    $this->widgetSchema['contacts_list'] = new cxWidgetFormDoctrineJQuerySelectMany(array(
      'model' => 'Contact',
      'url'   => url_for('contact/ajax'),
      'order_by' => array('name,firstname',''),
    ));
    $this->widgetSchema['professionals_list'] = new cxWidgetFormDoctrineJQuerySelectMany(array(
      'model' => 'Professional',
      'url'   => url_for('professional/ajax'),
      'method'=> 'getFullName',
      'order_by' => array('c.name,c.firstname,o.name,t.name,p.name',''),
    ));
    
    $this->widgetSchema['sf_guard_user_id']->setOption('order_by',array('first_name, last_name, username',''));
    
    $this->widgetSchema   ['email_address'] = new sfWidgetFormInput(array(
      'type' => 'email',
    ));
    $this->validatorSchema['email_address'] = new sfValidatorEmail(array(
      'required' => false,
    ));
  }
  
  public function getFields()
  {
    $fields = parent::getFields();
    $fields['email_address'] = 'EmailAddress';
    return $fields;
  }
  
  public function addEmailAddressColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( !$value )
      return $q;
    
    $a = $q->getRootAlias();
    $q->andWhere("LOWER($a.field_to) = LOWER(?) OR LOWER($a.field_cc) = LOWER(?) OR LOWER($a.field_bcc) = LOWER(?)", array($value, $value, $value));
    
    return $q;
  }
}

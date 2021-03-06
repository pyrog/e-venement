<?php

/**
 * Invoice filter form.
 *
 * @package    e-venement
 * @subpackage filter
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InvoiceFormFilter extends BaseInvoiceFormFilter
{
  /**
   * @see AccountingFormFilter
   */
  public function configure()
  {
    parent::configure();
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    
    $this->widgetSchema   ['id'] = new sfWidgetFormInput;
    $this->validatorSchema['id'] = new sfValidatorInteger(array(
      'required' => false,
    ));
    
    $this->widgetSchema['created_at'] = new sfWidgetFormDateRange(array(
      'from_date' => new liWidgetFormDateText(),
      'to_date'   => new liWidgetFormDateText(),
      'template'  => __('<span class="dates"><span>from %from_date%</span> <span>to %to_date%</span></span>'),
    ));
    
    $this->widgetSchema['transaction_id'] = new sfWidgetFormInputText();
    
    $this->widgetSchema   ['tickets_value'] = new sfWidgetFormInputText();
    $this->validatorSchema['tickets_value'] = new sfValidatorInteger(array(
      'required' => false,
    ));
    
    $this->widgetSchema   ['contact_id'] = new liWidgetFormDoctrineJQueryAutocompleter(array(
      'model' => 'Contact',
      'url'   => cross_app_url_for('rp','contact/ajax'),
    ));
    $this->validatorSchema['contact_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Contact',
      'required' => false,
    ));
    $this->widgetSchema['organism_id'] = new liWidgetFormDoctrineJQueryAutocompleter(array(
      'model' => 'Organism',
      'url'   => cross_app_url_for('rp','organism/ajax'),
    ));
    $this->validatorSchema['organism_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Organism',
      'required' => false,
    ));
  }
  public function setup()
  {
    $this->noTimestampableUnset = true;
    parent::setup();
  }
  public function getFields()
  {
    return array_merge(array(
      'tickets_value' => 'TicketsValue',
      'id' => 'Id',
      'contact_id' => 'ContactId',
      'organism_id' => 'OrganismId',
    ), parent::getFields());
  }
  
  public function addIdColumnQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);
    if ( $value )
    {
      $a = $query->getRootAlias();
      $query->andWhere("$a.$fieldName = ?",$value);
    }
    return $query;
  }
  
  public function addContactIdColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( !trim($value) )
      return $q;
    
    $q->andWhere('c.id = ?', $value);
    
    return $q;
  }
  public function addOrganismIdColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( !trim($value) )
      return $q;
    
    $q->andWhere('o.id = ?', $value);
    
    return $q;
  }
  
  public function addTicketsValueColumnQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);
    if ( $value )
    {
      $a = $query->getRootAlias();
      $query->andWhere("(SELECT sum(ttck.value) FROM Ticket ttck LEFT JOIN ttck.Transaction tt WHERE tt.id = $a.transaction_id AND ttck.duplicating IS NULL AND (ttck.printed_at IS NOT NULL OR ttck.integrated_at IS NOT NULL)) = ?",$value);
    }
    
    return $query;
  }
}

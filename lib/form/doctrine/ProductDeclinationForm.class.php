<?php

/**
 * ProductDeclination form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProductDeclinationForm extends BaseProductDeclinationForm
{
  /**
   * @see TraceableForm
   */
  public function configure()
  {
    parent::configure();
    
    $this->widgetSchema['product_id'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['stock_perfect']->setLabel('Perfect stock')
      ->setAttribute('class', 'stock stock-perfect');
    $this->widgetSchema['stock_critical']->setLabel('Critical stock')
      ->setAttribute('class', 'stock stock-critical');
    $this->widgetSchema['stock']
      ->setAttribute('class', 'stock stock-current');
    
    $this->useFields(array_merge(array(
      'id', 'product_id', 'prioritary', 'code',
    ),array_keys($this->embeddedForms),
    array(
      'stock', 'stock_perfect', 'stock_critical',
    )));
  }
}

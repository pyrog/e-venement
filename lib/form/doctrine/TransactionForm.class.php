<?php

/**
 * Transaction form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TransactionForm extends BaseTransactionForm
{
  /**
   * @see TraceableForm
   */
  public function configure()
  {
    parent::configure();
    $this->widgetSchema['contact_id']->setOption('config','{ max: 30 }');
    $this->widgetSchema['description'] = new sfWidgetFormTextArea;
  }
}

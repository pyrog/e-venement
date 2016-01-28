<?php

/**
 * SurveyAnswer form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SurveyAnswerForm extends BaseSurveyAnswerForm
{
  /**
   * @see TraceableForm
   */
  public function configure()
  {
    parent::configure();
    $this->widgetSchema['survey_query_id'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['lang'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['contact_id'] = new sfWidgetFormInputHidden;

    $sf_user = sfContext::hasInstance() ? sfContext::getInstance()->getUser() : NULL;
    if ( $sf_user )
      $this->setDefault('lang', $sf_user->getCulture());
  }

  public function forge(SurveyQuery $query, $selected_choices = array())
  {
    $this->widgetSchema   ['value'] = $query->getWidget();
    $this->validatorSchema['value'] = $query->getValidator();

    if ( $selected_choices )
    {
      $this->setDefault('value', $selected_choices);
    }

    $this->useFields(array(
      'value',
      'survey_query_id',
      'lang',
      'contact_id',
    ));

    return $this;
  }

  public function doSave($con = null)
  {
    if ( !trim($this->object->value) )
      return;
    parent::doSave($con);
  }
}

<?php

/**
 * SurveyAnswer form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SurveyAnswersForm extends sfFormDoctrine
{
  protected $query;
  protected $answers;
  protected $answers_group;
  
  public function __construct($answers = null, $options = array(), $CSRFSecret = null)
  {
    if ( $answers instanceof Iterable )
      throw new liEvenementException('You must provide an Iterable batch of $answers for your SurveyAnswersForm');
    $this->answers = $answers;
    
    if (! $options['query'] instanceof SurveyQuery )
      throw new liEvenementException('You must provide a SurveyQuery as the "query" option of the SurveyAnswersForm');
    $this->query = $options['query'];
    if (! $options['answers_group'] instanceof SurveyAnswersGroup )
      throw new liEvenementException('You must provide a SurveyAnswersGroup as the "answers_group" option of the SurveyAnswersForm');
    $this->answers_group = $options['answers_group'];

    parent::__construct(array(), $options, $CSRFSecret);
  }
  
  /**
   * @see TraceableForm
   */
  public function configure()
  {
    parent::configure();
    
    // defining the query
    $this->defaults['survey_query_id'] = $this->query->id;
    
    // defining the values (from answers) for this query
    $this->values['value'] = array();
    foreach ( $answers as $answer )
      $this->values['value'][] = $answer->value;
    
    $this->widgetSchema['survey_query_id'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['lang'] = new sfWidgetFormInputHidden;
    
    $sf_user = sfContext::hasInstance() ? sfContext::getInstance()->getUser() : NULL;
    if ( $sf_user )
      $this->setDefault('lang', $sf_user->getCulture());
    
    $this->widgetSchema   ['value'] = $this->query->getWidget();
    $this->validatorSchema['value'] = $this->query->getValidator();
    
    $this->useFields(array(
      'value',
      'survey_query_id',
      'lang',
    ));
    
    return $this;
  }
  
  public function getModelName()
  {
    return 'SurveyAnswer';
  }
  
  /*
  public function updateObject($values = null)
  {
    die('update');
    $answers = array_values($this->answers);
    
    // new or existing answers
    foreach ( array_values($values['value']) as $key => $value )
    {
      // new answer
      if ( !isset($answers[$key]) )
      {
        $answers[$key] = new SurveyAnswer;
        $answers[$key]->Query = $this->query;
        $answers[$key]->AnswersGroup = $this->answer_group;
      }
      $answers[$key]->value = $value;
    }
    
    // answers to be removed
    for ( $i = $key+1 ; $i < count($answers) ; $i++ )
      $answers[$i]->delete();
  }
  */
}

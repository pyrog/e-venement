<?php

/**
 * SurveyAnswersGroup form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SurveyAnswersGroupForm extends BaseSurveyAnswersGroupForm
{
  /**
   * @see TraceableForm
   */
  public function configure()
  {
    parent::configure();
    $this->widgetSchema['contact_id'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['transaction_id'] = new sfWidgetFormInputHidden;
    
    $useFields = array('contact_id', 'transaction_id');
    
    $queries = array();
    foreach ( $this->object->Survey->Queries as $query )
      $queries[$query->rank.'-'.$query->id] = $query;
    ksort($queries);
    foreach ( $queries as $query )
    {
      $answers = array();
      foreach ( $this->object->Survey->AnswersGroups as $sag )
      foreach ( $sag->Answers as $sa )
      if ( $sa->survey_query_id == $query->id )
        $answers[] = $sa;
      
      if ( $answers )
      foreach ( $answers as $answer )
      {
        $query->Answers[] = $answer;
        $this->object->Answers[] = $answer;
      }
      else
      {
        $answer = new SurveyAnswer;
        $query->Answers[] = $answer;
        $answer->Query = $query;
        $this->object->Answers[] = $answer;
      }
      
      $form = new SurveyAnswersForm($this->object->Answers, array('query' => $query, 'answers_group' => $this->object));
      $this->embedForm($query->id, $form);
      $useFields[] = $query->id;
    }
    
    $this->useFields($useFields);
  }
}

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
      // TODO: loop for each direct contact when query.type is liWidgetFormChoiceMultiple

      $answer = null;
      $selected_choices = array();
      foreach ($this->object->Answers as $a)
      if ( $a->Query->id == $query->id )
      {
        $selected_choices[] = $a->value;
        $answer = $a;
      }
      if ( !$answer ) {
        $answer = new SurveyAnswer;
        $answer->Query = $query;
        $answer->Group = $this->object;
        $this->object->Answers[] = $answer;
      }

      $form = new SurveyAnswerForm($answer);
      $this->embedForm($query->id, $form->forge($query, $selected_choices));
      $useFields[] = $query->id;
    }

    $this->useFields($useFields);
  }
}

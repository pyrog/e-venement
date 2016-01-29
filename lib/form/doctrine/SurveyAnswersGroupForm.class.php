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
      if ( $query->type == 'liWidgetFormChoiceMultipleContact' ) // "direct" query
      {
        foreach ( $this->object->Transaction->DirectContacts as $contact )
        {
          $answer = new SurveyAnswer;
          $answer->Query = $query;
          $answer->Group = $this->object;
          $answer->Contact = $contact;
          $selected_choices = array();
          foreach ($this->object->Answers as $a)
          if ( $a->Query->id == $query->id && $a->contact_id == $contact->id)
          {
            $selected_choices[] = $a->value;
          }
          if ( !$selected_choices ) {

            $this->object->Answers[] = $answer;
          }

          $form = new SurveyAnswerForm($answer);
          if ( $contact )
              $form->getWidgetSchema()->setLabel($contact);
          $subform_id = $query->id . "_" .$contact->id;
          $this->embedForm($subform_id, $form->forge($query, $selected_choices));
          $useFields[] = $subform_id;
        }
      }
      else // query that is not linked to participants
      {
        $answer = null;
        foreach( $this->object->Answers as $a)
        if ( $a->survey_query_id == $query->id )
          $answer = $a;

        if ( !$answer )
        {
          $answer = new SurveyAnswer;
          $answer->Query = $query;
          $answer->Group = $this->object;
          $this->object->Answers[] = $answer;
        }

        $form = new SurveyAnswerForm($answer);
        $form->getWidgetSchema()->setLabel('<span>' . $query->id . '</span>');
        $this->embedForm($query->id, $form->forge($query));
        $useFields[] = $query->id;
      }
    }

    $this->useFields($useFields);
  }
}

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
      // get transaction direct contacts for liWidgetFormChoiceMultiple queries
      $contacts = ( $query->type == 'liWidgetFormChoiceMultipleContact' ) ?
         $this->object->Transaction->DirectContacts :
         array(null);

      foreach ( $contacts as $contact )
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
        $subform_id = $contact ? $query->id . "_" .$contact->id : $query->id;
        $this->embedForm($subform_id, $form->forge($query, $selected_choices));
        $useFields[] = $subform_id;
      }
    }

    $this->useFields($useFields);
  }
}

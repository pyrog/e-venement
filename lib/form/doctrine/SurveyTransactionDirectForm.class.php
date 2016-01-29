<?php

/**
 * Survey form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @author     Marcos BEZERRA DE MENEZES <marcos.bezerra AT libre-informatique.fr>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SurveyTransactionDirectForm extends SurveyAnswersGroupForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['contact_id'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['transaction_id'] = new sfWidgetFormInputHidden;
    $this->widgetSchema['survey_id'] = new sfWidgetFormInputHidden;

    $this->validatorSchema['survey_id'] = new sfValidatorPass();

    $useFields = array('contact_id', 'transaction_id', 'survey_id', 'Answers');

    $queries = array();
    foreach ( $this->object->Survey->Queries as $query )
      $queries[$query->rank.'-'.$query->id] = $query;
    ksort($queries);

    $subform_ids = array();
    foreach ( $queries as $query )
    {
      if ( $query->type != 'liWidgetFormChoiceMultipleContact' )
          continue;

      foreach( $this->object->Transaction->Tickets as $ticket )
      if ( $contact = $ticket->DirectContact )
      if ( $contact->id )
      {
        $answer = new SurveyAnswer;
        $answer->Query = $query;
        $answer->contact_id = $contact;
        $this->object->Answers[] = $answer;

//        $form = new SurveyAnswerForm($answer);
//        $form->getWidgetSchema()->setLabel($contact);
//        $subform_id = sprintf('Answers[%d_%d]', $query->id, $contact->id);
//        $this->embedForm($subform_id , $form->forge($query));
//        $useFields[] = $subform_id;
//        $subform_ids[] = $subform_id;
      }
    }

    $this->embedRelation('Answers');


    $this->useFields($useFields);

    // Avoid "Unexpected extra form field named xx_yyyy" validation errors
    //$this->validatorSchema->setOption('allow_extra_fields', true);
  }

  public function doBind(array $values)
  {

    foreach ( $values as $key => $answer )
    if ( preg_match('/^[1-9]+_[0-9]+$/', $key) )
    {
      //if (!( isset($answer['value']) && !(!is_array($answer['value']) && !trim($answer['value'])) ))
      {
        unset($values[$key]);
        unset($this->embeddedForms[$key]);
        //unset($this->validatorSchema[$key]);
        continue;
      }
    }

    parent::doBind($values);
  }

  public function doSave($con = NULL)
  {
    if (null === $con)
      $con = $this->getConnection();

    $this->updateObject();
    $this->saveEmbeddedForms($con);
  }
}

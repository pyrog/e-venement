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
class SurveyDirectForm extends SurveyForm
{
  private $transaction;

  public function configure()
  {
    parent::configure();

    $this->transaction = $this->getOption('transaction');

    $group = null;
    foreach ($this->transaction->SurveyAnswersGroups as $sag)
    if ( $sag->survey_id == $this->object->id )
    {
      $group = $sag;
      break;
    }
    if ( !$group )
    {
      $group = new SurveyAnswersGroup;
      $group->contact_id = $this->transaction->contact_id;
      $group->transaction_id = $this->transaction->id;
      $group->survey_id = $this->object->id;
    }

    $this->object->AnswersGroups = new Doctrine_collection('SurveyAnswersGroup');
    $this->object->AnswersGroups[] = $group;

    $this->embedRelation('AnswersGroups');
    $this->useFields(array('AnswersGroups'));
  }

  public function doBind(array $values)
  {
    if ( isset($values['AnswersGroups']) && is_array($values['AnswersGroups']) )
    foreach ( $values['AnswersGroups'] as $gid => $group )
    {
      if ( is_array($group) )
      foreach ( $group as $aid => $answer )
      if ( intval($aid).'' === ''.$aid )
      {
        if ( !$this->validatorSchema['AnswersGroups'][$gid][$aid]['value']->getOption('required') )
        if (!(isset($answer['value']) && !(!is_array($answer['value']) && !trim($answer['value'])) ))
        {
          unset($values['AnswersGroups'][$gid][$aid]);
          unset($this->embeddedForms['AnswersGroups'][$gid]->embeddedForms[$aid]);
          unset($this->validatorSchema['AnswersGroups'][$gid][$aid]);
          continue;
        }

        if ( !(isset($answer['lang']) && $answer['lang']) && $sf_user )
          $values['AnswersGroups'][$gid][$aid]['lang'] = $sf_user->getCulture();
      }

      if ( !$group['contact_id'] )
        $values['AnswersGroups'][$gid]['contact_id'] = $this->transaction->contact_id;
      if ( !$group['transaction_id'] )
        $values['AnswersGroups'][$gid]['transaction_id'] = $this->transaction->id;
    }

    parent::doBind($values);
  }

  public function doSave($con = NULL)
  {
    if (null === $con)
      $con = $this->getConnection();

    foreach ($this->transaction->SurveyAnswersGroups as $sag)
    if ( $sag->survey_id == $this->object->id )
    {
       $sag->Answers = new Doctrine_collection('SurveyAnswer');
    }

    $this->updateObject();

    $this->saveEmbeddedForms($con);
  }
}

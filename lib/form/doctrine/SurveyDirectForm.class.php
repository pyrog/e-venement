<?php

/**
 * Survey form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SurveyTckForm extends SurveyForm
{
  protected $transaction;
  
  public function configure()
  {
    parent::configure();
    $this->transaction = $this->getOption('transaction');
    
    $tmp = null;
    foreach ( $this->transaction->SurveyAnswersGroups as $sag )
    if ( $sag->transaction_id == $this->transaction->id )
      $tmp = $sag;
    $sag = $sag ? $sag : new SurveyAnswersGroup;
    $sag->Transaction = $this->transaction;
    $this->object->AnswersGroups[] = $sag;
    
    $this->embedRelation('AnswersGroups');
    $this->useFields(array('AnswersGroups'));
  }
  
  public function doBind(array $values)
  {
    $sf_user = sfContext::hasInstance() ? sfContext::getInstance()->getUser() : NULL;
    
    if ( isset($values['AnswersGroups']) && is_array($values['AnswersGroups']) )
    foreach ( $values['AnswersGroups'] as $gid => $group )
    {
      if ( is_array($group) )
      foreach ( $group as $aid => $answer )
      if ( intval($aid).'' === ''.$aid )
      {
        if ( !$this->validatorSchema['AnswersGroups'][$gid][$aid]['value']->getOption('required') )
        if (!( isset($answer['value']) && !(!is_array($answer['value']) && !trim($answer['value'])) ))
        {
          unset($values['AnswersGroups'][$gid][$aid]);
          unset($this->embeddedForms['AnswersGroups'][$gid]->embeddedForms[$aid]);
          unset($this->validatorSchema['AnswersGroups'][$gid][$aid]);
          continue;
        }
        
        if ( !(isset($answer['lang']) && $answer['lang']) && $sf_user )
          $values['AnswersGroups'][$gid][$aid]['lang'] = $sf_user->getCulture();
      }
      
      if ( !$group['contact_id'] && $this->transaction->contact_id )
        $values['AnswersGroups'][$gid]['contact_id'] = $this->transaction->contact_id;
      if ( !$group['transaction_id'] && $this->transaction->id )
        $values['AnswersGroups'][$gid]['transaction_id'] = $this->transaction->id;
    }
    
    parent::doBind($values);
  }
  
  public function doSave($con = NULL)
  {
    if (null === $con)
      $con = $this->getConnection();
    $this->updateObject();
    $this->saveEmbeddedForms($con);
    
    // remove old an
      Doctrine_Query::create()->from('SurveyAnswer sa')
        ->andWhere('sa.survey_answer_group_id = ?', $group
    // process the answers...
    if ( isset($values['AnswersGroups']) && is_array($values['AnswersGroups']) )
    foreach ( $values['AnswersGroups'] as $gid => $group )
    {
      if ( is_array($group) )
      foreach ( $group as $aid => $answer )
      if ( intval($aid).'' === ''.$aid )
      {
      }
    }
  }
}

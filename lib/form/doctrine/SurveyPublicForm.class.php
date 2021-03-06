<?php

/**
 * Survey form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SurveyPublicForm extends SurveyForm
{
  public function configure()
  {
    parent::configure();
    $sf_user = sfContext::hasInstance() ? sfContext::getInstance()->getUser() : NULL;
    
    $group = new SurveyAnswersGroup;
    if ( $sf_user && $sf_user->getTransaction()->contact_id )
      $group->contact_id = $sf_user->getTransaction()->contact_id;
    if ( $sf_user && $sf_user->getTransaction()->id )
      $group->transaction_id = $sf_user->getTransaction()->id;
    
    unset($this->object->AnswersGroups);
    $this->object->AnswersGroups[] = $group;
    
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
      
      if ( $sf_user && !$group['contact_id'] && $sf_user->getTransaction()->contact_id )
        $values['AnswersGroups'][$gid]['contact_id'] = $sf_user->getTransaction()->contact_id;
      if ( $sf_user && !$group['transaction_id'] && $sf_user->getTransaction()->id )
        $values['AnswersGroups'][$gid]['transaction_id'] = $sf_user->getTransaction()->id;
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

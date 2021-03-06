<?php

/**
 * ManifestationEntry form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ManifestationEntryForm extends BaseManifestationEntryForm
{
  public function configure()
  {
    $this->widgetSchema   ['entry_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['entry_id']->setOption('required', false);
    
    /*
    $this->widgetSchema['manifestation_id'] = new sfWidgetFormDoctrineJQueryAutocompleter(array(
      'model' => 'Manifestation',
      'url' => cross_app_url_for('event','manifestation/ajax'),
      'config' => '{ max: '.sfConfig::get('app_manifestation_depends_on_limit',10).' }',
    ));
    */
    
    $this->widgetSchema['manifestation_id']->setOption('add_empty',true);
    $q = Doctrine_Query::create()
      ->from('Manifestation m')
      ->leftJoin('m.Event e')
      ->leftJoin('e.MetaEvent me')
      ->leftJoin('m.Workspaces w')
      ->leftJoin('w.GroupWorkspace gw')
      ->select('m.*, e.*')
      ->andWhere('gw.id IS NOT NULL');
    if ( sfContext::hasInstance() && $sf_user = sfContext::getInstance()->getUser() )
      $q->leftJoin('w.Users wu')
        ->andWhere('wu.id = ?',$sf_user->getId())
        ->leftJoin('me.Users meu')
        ->andWhere('meu.id = ?',$sf_user->getId());
    $this->widgetSchema   ['manifestation_id']->setOption('query',$q);
    $this->validatorSchema['manifestation_id']->setOption('query',$q);
    
    $this->enableCSRFProtection();
  }
  
  public function searchAllManifestations($all, Entry $entry)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('CrossAppLink'));
    
    if ( $all )
      $this->widgetSchema['manifestation_id'] = new liWidgetFormDoctrineJQueryAutocompleter(array(
        'model' => 'Manifestation',
        'url'   => cross_app_url_for('event', 'manifestation/ajax?for=grp'),
      ));
    elseif ( !$entry->isNew() )
      $this->widgetSchema['manifestation_id']->getOption('query')
        ->andWhere('e.id = ?', $entry->event_id)
        ->andWhereNotIn('m.id', $entry->ManifestationEntries->toKeyValueArray('id', 'manifestation_id'))
      ;
    
    return $this;
  }
  
  public function save($con = NULL)
  {
    if ( !$this->values['entry_id'] && $this->values['manifestation_id'] )
      $this->object->Entry->event_id = Doctrine::getTable('Manifestation')->find($this->values['manifestation_id'])->event_id;
    return parent::save($con);
  }
}

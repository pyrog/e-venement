<?php

/**
 * PluginSurveyQueryTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginSurveyQueryTable extends Doctrine_Table implements CompositeSearchableTable
{
  public function batchUpdateIndex($limit = null, $offset = null, $encoding = null)
  {
    if ( !$this->hasTemplate('Searchable') )
      return false;
    
    return $this->getTemplate('Searchable')->getListener()->get('Searchable')->batchUpdateIndex($limit, $offset, $encoding);
  }
  
  public static function getInstance()
  {
      return Doctrine_Core::getTable('PluginSurveyQuery');
  }
}

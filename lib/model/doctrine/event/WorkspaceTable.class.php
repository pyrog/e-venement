<?php

/**
 * WorkspaceTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class WorkspaceTable extends PluginWorkspaceTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object WorkspaceTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Workspace');
    }
  
  public function createQuery($alias = 'g')
  {
    return parent::createQuery($alias)
      ->andWhereIn("$alias.id",array_keys(sfContext::getInstance()->getUser()->getWorkspacesCredentials()));
  }
}

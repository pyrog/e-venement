<?php

/**
 * ProductWorkspaceLinkTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ProductWorkspaceLinkTable extends PluginProductWorkspaceLinkTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object ProductWorkspaceLinkTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ProductWorkspaceLink');
    }
}
<?php

/**
 * ModelTypeTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ModelTypeTable extends PluginModelTypeTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object ModelTypeTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ModelType');
    }
}
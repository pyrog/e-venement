<?php

/**
 * YOBTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class YOBTable extends PluginYOBTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object YOBTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('YOB');
    }
}
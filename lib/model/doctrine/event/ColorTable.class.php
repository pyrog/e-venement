<?php

/**
 * ColorTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ColorTable extends PluginColorTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object ColorTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Color');
    }
}
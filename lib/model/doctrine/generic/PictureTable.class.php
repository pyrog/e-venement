<?php

/**
 * PictureTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PictureTable extends PluginPictureTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object PictureTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Picture');
    }
}
<?php

/**
 * PluginOptionMandatoryField
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginOptionMandatoryField extends BaseOptionMandatoryField
{
  public function save(Doctrine_Connection $con = NULL)
  {
    $this->name = 'mandatory';
    if ( sfContext::hasInstance() && is_null($this->sf_guard_user_id) )
      $this->sf_guard_user_id = sfContext::getInstance()->getUser()->getId();
    
    return parent::save($con);
  }
}
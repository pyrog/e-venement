<?php

/**
 * MemberCardTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MemberCardTable extends PluginMemberCardTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object MemberCardTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('MemberCard');
    }
  
  public function retreiveListOfActivatedCards()
  {
    $q = $this->createQuery('mc');
    $q->leftJoin('mc.Contact c')
      ->andWhere('mc.active = true');
    return $q;
  }
}

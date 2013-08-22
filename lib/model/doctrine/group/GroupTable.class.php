<?php

/**
 * GroupTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class GroupTable extends PluginGroupTable
{
  public function createQuery($alias = 'a')
  {
    $u  = 'u'  != $alias ? 'u'  : 'u1';
    $c  = 'c'  != $alias ? 'c'  : 'c1';
    $p  = 'p'  != $alias ? 'p'  : 'p1';
    $pc = 'pc' != $alias ? 'pc' : 'pc1';
    $pt = 'pt' != $alias ? 'pt' : 'pt1';
    $o  = 'o'  != $alias ? 'o'  : 'o1';
    
    $query = parent::createQuery($alias)
      ->leftJoin("$alias.User $u")
      //->leftJoin("$alias.Picture $p")
      ;
/*
      ->leftJoin("$alias.Professionals $p")
      ->leftJoin("$p.Contact $pc")
      ->leftJoin("$p.ProfessionalType $pt")
      ->leftJoin("$p.Organism $o")
      ->leftJoin("$alias.Contacts $c");
*/
    if ( !sfContext::hasInstance() )
      return $query;
    
    $sf_user = sfContext::getInstance()->getUser();
    return $query->andWhere(($sf_user->hasCredential('pr-group-common') ? "$alias.sf_guard_user_id IS NULL OR " : '')."$alias.sf_guard_user_id = ?",$sf_user->getId())
      ->leftJoin("$alias.Users auth_users")
      ->andWhere("$alias.sf_guard_user_id IS NOT NULL OR auth_users.id = ? OR ?",array(
        $sf_user->getId(),
        $sf_user->hasCredential(array('admin-users','admin-power'),false),
      ));
  }

  public function retrieveList()
  {
    return $this->createQuery('g');
  }
  
    /**
     * Returns an instance of this class.
     *
     * @return object GroupTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Group');
    }
}

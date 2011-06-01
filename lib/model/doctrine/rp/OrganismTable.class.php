<?php

/**
 * OrganismTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class OrganismTable extends PluginOrganismTable
{
  public function createQueryByEmailId($id)
  {
    $q = $this->createQuery();
    $a = $q->getRootAlias();
    $q->leftJoin("$a.Emails e")
      ->andWhere('e.sent = TRUE')
      ->andWhere('e.id = ?',$id)
      ->orderby('name, city');
    return $q;
  }
  public function createQuery($alias = 'a')
  {
    $p  = 'p'  != $alias ? 'p'  : 'p1';
    $c  = 'c'  != $alias ? 'c'  : 'c1';
    $pt = 'pt' != $alias ? 'pt' : 'pt1';
    $pn = 'pn' != $alias ? 'pn' : 'pn1';
    $oc = 'oc' != $alias ? 'oc' : 'oc1';
    
    $query = parent::createQuery($alias)
      ->leftJoin("$alias.Professionals $p")
      ->leftJoin("$p.ProfessionalType $pt")
      ->leftJoin("$p.Contact $c")
      ->leftJoin("$alias.Phonenumbers $pn")
      ->leftJoin("$alias.Category $oc");
    return $query;
  }

    /**
     * Returns an instance of this class.
     *
     * @return object OrganismTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Organism');
    }
}

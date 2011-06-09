<?php

/**
 * Professional
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Professional extends PluginProfessional
{
  public function __toString()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    return ($this->name ? $this->name : $this->ProfessionalType).' '.__('at').' '.$this->Organism;
  }
  public function getFullName()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    return $this->Contact.', '.($this->name ? $this->name : $this->ProfessionalType).' '.__('at').' '.$this->Organism;
  }
  public function getNameType()
  {
    return $this->name ? $this->name.($this->ProfessionalType ? ' ('.$this->ProfessionalType.')' : '') : $this->ProfessionalType;
  }
  public function getFullDesc()
  {
    return $this->Organism.' ('.($this->name ? $this->name : $this->ProfessionalType).')';
  }
}

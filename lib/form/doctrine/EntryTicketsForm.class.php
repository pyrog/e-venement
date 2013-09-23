<?php
/**********************************************************************************
*
*	    This file is part of e-venement.
*
*    e-venement is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License.
*
*    e-venement is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with e-venement; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006-2011 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2011 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php

/**
 * EntryTickets form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class EntryTicketsForm extends BaseEntryTicketsForm
{
  public function configure()
  {
    $this->widgetSchema['entry_element_id'] = new sfWidgetFormInputHidden();
    
    $prices = $userp = $manifp = array();
    
    $this->widgetSchema['price_id']->setOption('add_empty', true);
    $this->restrictPriceIdQuery();
    
    $this->validatorSchema['gauge_id']->setOption('query', $q = Doctrine::getTable('Gauge')
      ->createQuery('g')
      ->select('g.*')
      ->leftJoin('g.Manifestation m')
      ->leftJoin('m.ManifestationEntries me')
      ->leftJoin('me.Entries ee')
      ->leftJoin('g.Workspace w')
      ->leftJoin('w.GroupWorkspace gw')
      ->andWhere('gw.id IS NOT NULL')
    );
    $this->widgetSchema   ['gauge_id']
      ->setOption('query', $q->copy())
      ->setOption('order_by', array('w.name', ''));
    if ( !$this->isNew() )
      $this->widgetSchema ['gauge_id'] = new sfWidgetFormInputHidden;

    $arr = array('id','entry_element_id','quantity','price_id', 'gauge_id');
    $this->widgetSchema->setPositions($arr);
    $this->enableCSRFProtection();
  }
  
  public function doBind(array $values)
  {
    $this->restrictGaugeIdQuery($this->validatorSchema['entry_element_id']->clean($values['entry_element_id']));
    parent::doBind($values);
  }
  
  public function restrictGaugeIdQuery($entry_element_id)
  {
    $manifid = Doctrine::getTable('EntryElement')->findOneById($entry_element_id)->ManifestationEntry->manifestation_id;
    if ( $this->widgetSchema['gauge_id']->getOption('query') instanceof Doctrine_Query )
      $this->widgetSchema   ['gauge_id']->getOption('query')->andWhere('g.manifestation_id = ?', $manifid);
    $this->validatorSchema['gauge_id']->getOption('query')->andWhere('g.manifestation_id = ?', $manifid);
    return $this;
  }
  
  public function restrictPriceIdQuery($entry_element_id = NULL)
  {
    $prices = array();
    $q = Doctrine::getTable('Price')->createQuery('p')
      ->select('p.*')
      ->leftJoin('p.Users u')
      ->andWhere('u.id = ?',sfContext::getInstance()->getUser()->getId())
      ->leftJoin('p.Manifestations m')
      ->leftJoin('m.ManifestationEntries me')
      ->leftJoin('me.Entries el')
      ->andWhere('el.id = ?',$id = !is_null($entry_element_id) ? $entry_element_id : ($this->values['entry_element_id'] ? $this->values['entry_element_id'] : $this->getObject()->entry_element_id));
    foreach ( $q->execute() as $pm )
      $prices[] = $pm->id;
    
    $this->widgetSchema['price_id']->setOption('query', $q = Doctrine::getTable('Price')
      ->createQuery('p')
      ->andWhere('p.hide = FALSE')
      ->andWhereIn('p.id',$prices)
      ->orderBy('p.name')
    );
    $this->validatorSchema['price_id']->setOption('query',$q);
  }
}


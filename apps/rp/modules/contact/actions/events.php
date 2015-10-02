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
*    Copyright (c) 2006-2015 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2015 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
      $id = $request->getParameter('id',0);
      
      $meta_events = array();
      $q = Doctrine::getTable('Transaction')->createQuery('t')
        ->leftJoin('m.Event e')
        ->leftJoin('t.Contact c')
        ->leftJoin('t.Professional p')
        ->orderBy("t.contact_id = $id, m.happens_at DESC")
        ->andWhere('e.meta_event_id = ?', $request->getParameter('meid',0))
        ->andWhere('tck.printed_at IS NOT NULL OR tck.integrated_at IS NOT NULL OR (SELECT count(oo.id) FROM order oo WHERE oo.transaction_id = t.id) > 0')
      ;
      switch ( $request->getParameter('type','contact') ) {
      case 'contact':
        $this->type = 'Contact';
        $q->andWhere('tck.contact_id = ? OR t.contact_id = ?', array($id,$id))
          ->andWhere('t.professional_id IS NULL');
        break;
      case 'professional':
        $this->type = 'Professional';
        $q->andWhere('t.professional_id = ?', $id);
        break;
      }
      $this->transactions = $q->execute();
      $this->object = Doctrine::getTable($this->type)->find($id);
      
      return 'Success';

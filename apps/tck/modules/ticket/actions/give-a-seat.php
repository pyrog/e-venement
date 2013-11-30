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
*    Copyright (c) 2006-2013 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2013 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
  $ticket = $request->getParameter('ticket');
  $form = new sfForm;
  $validators = $form->getValidatorSchema();
  $validators['id'] = new sfValidatorDoctrineChoice(array(
    'model' => 'Ticket',
    'query' => Doctrine::getTable('Ticket')->createQuery('tck')
      ->andWhere('tck.transaction_id = ?', $request->getParameter('id')),
  ));
  $validators['numerotation'] = new sfValidatorDoctrineChoice(array(
    'model' => 'Seat',
    'column' => 'name',
    'query' => $q = Doctrine::getTable('Seat')->createQuery('s')
      ->leftJoin('s.SeatedPlan sp')
      ->leftJoin('sp.Workspace ws')
      ->leftJoin('ws.Gauges g')
      ->leftJoin('g.Tickets tck')
      ->leftJoin('tck.Transaction t')
      ->andWhere('t.closed = ?',false)
      ->andWhere('(tck.numerotation IS NULL OR tck.numerotation = ?)','')
      ->andWhere('tck.id = ?',$ticket['id'])
      ->andWhere('tck.transaction_id = ?', $request->getParameter('id')),
  ));
  
  $form->bind($ticket);
  if ( !$form->isValid() ) // security checks
    throw new liSeatedException('The submitted data are not correct to give a seat to this ticket... '.$form->getErrorSchema());
  
  $this->ticket = Doctrine::getTable('Ticket')->findOneById($ticket['id']);
  $this->ticket->numerotation = $ticket['numerotation'];
  $this->ticket->save();
  
  return sfView::NONE;
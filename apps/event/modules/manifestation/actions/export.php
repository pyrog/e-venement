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
*    Foundation, Inc., 5'.$rank.' Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006-2011 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2011 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
    $this->getContext()->getConfiguration()->loadHelpers(array('Date','CrossAppLink'));
    $manifestation = $this->getRoute()->getObject();
    
    $q = liDoctrineQuery::create()->from('Contact c')
      ->leftJoin('c.Transactions t')
      // Tickets from Transaction
      ->leftJoin('t.Tickets tck WITH tck.cancelling IS NULL AND tck.id NOT IN (SELECT tck2.cancelling FROM Ticket tck2 WHERE tck2.cancelling IS NOT NULL) AND tck.manifestation_id = ?', $manifestation->id)
      ->leftJoin('c.Professionals cp WITH tck.id IS NOT NULL AND t.professional_id IS NOT NULL AND cp.id = t.professional_id')
      // DirectContacts
      ->leftJoin('c.DirectTickets dtck WITH dtck.cancelling IS NULL AND dtck.id NOT IN (SELECT dtck2.cancelling FROM Ticket dtck2 WHERE dtck2.cancelling IS NOT NULL) AND dtck.manifestation_id = ?', $manifestation->id)
      
      ->select('c.*, cp.*')
      ->andWhere('dtck.id IS NOT NULL OR tck.id IS NOT NULL')
    ;
    
    
    switch ( $type = $request->getParameter('status') ) {
    case 'asked':
      $q->leftJoin('t.Order o')
        ->andWhere('o.id IS NULL')
        ->andWhere('dtck.printed_at IS NULL AND dtck.integrated_at IS NULL')
        ->andWhere(' tck.printed_at IS NULL AND  tck.integrated_at IS NULL');
      break;
    case 'ordered':
      $q->leftJoin('t.Order o')
        ->andWhere('o.id IS NOT NULL')
        ->andWhere('dtck.printed_at IS NULL AND dtck.integrated_at IS NULL')
        ->andWhere(' tck.printed_at IS NULL AND  tck.integrated_at IS NULL');
      break;
    default:
      $q->andWhere('(tck.printed_at IS NOT NULL OR tck.integrated_at IS NOT NULL OR dtck.printed_at IS NOT NULL OR dtck.integrated_at IS NOT NULL)');
      break;
    }

    $contacts = $q->execute();
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $i18n = array(
      'asked'   => __('Asked tickets'),
      'ordered' => __('Ordered tickets'),
      'printed' => __('Printed tickets'),
    );
    
    $group = new Group;
    $group->name = $manifestation.' / '.format_datetime(date('Y-m-d H:i:s')).' ('.strtolower($i18n[$type]).')';
    $group->sf_guard_user_id = $this->getUser()->getId();
    
    foreach ( $contacts as $contact )
    if ( $contact->Professionals->count() > 0 )
    foreach ( $contact->Professionals as $professional )
      $group->Professionals[] = $professional;
    else
      $group->Contacts[] = $contact;
    
    $group->save();
    $this->redirect(cross_app_url_for('rp','group/show?id='.$group->id));
    return sfView::NONE;

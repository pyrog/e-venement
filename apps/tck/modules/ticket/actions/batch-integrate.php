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
  $this->getContext()->getConfiguration()->loadHelpers(array('I18N','CrossAppLink'));
  $notices = array();
  $glue = ' |~| ';
  
  // get back the manifestation
  $mid = $request->getParameter('manifestation_id');
  $q = Doctrine::getTable('Manifestation')->createQuery('m')
    ->where('id = ?',$mid);
  $this->manifestation = $q->fetchOne();
  
  // preconditions
  if ( !$this->manifestation->reservation_confirmed )
  {
    $this->getUser()->setFlash('error', __('It is forbidden to integrate foreign sales on an unconfirmed manifestation'));
    $this->redirect(cross_app_url_for('event', 'manifestation/show?id='.$this->manifestation->id));
  }
  
  $this->payform = new PaymentIntegrationForm($this->manifestation);
  $this->importform = new TicketsIntegrationForm($this->manifestation);
  
  // the data to integrate (tickets)
  $files = $request->getFiles('integrate');
  if ( count($files) > 0 )
  {
    $this->importform->bind($integrate = $request->getParameter('integrate'),$request->getFiles('integrate'));
    if ( $this->importform->isValid() )
    {
      $price_default_id = Doctrine::getTable('Price')->createQuery('p')
        ->andWhere('pt.name = ?',sfConfig::get('app_tickets_foreign_price', 'PART'))
        ->fetchOne()->id;
      
      $this->translation = array('prices' => array(), 'workspaces' => array());
      if ( !isset($integrate['translation_workspaces_category_ref'.$i]) )
        $integrate['translation_workspaces_category_ref'.$i] = '';
      if ( !isset($integrate['translation_workspaces_category_ref'.$i]) )
        $integrate['translation_categories_ref'.$i] = '';
      for ( $i = 0 ; isset($integrate['translation_workspaces_zone_ref'.$i]) && isset($integrate['translation_workspaces_dest'.$i]) ; $i++ )
      if ( $integrate['translation_workspaces_dest'.$i] )
        $this->translation['workspaces'][$integrate['translation_workspaces_zone_ref'.$i].$glue.$integrate['translation_workspaces_category_ref'.$i]] = $integrate['translation_workspaces_dest'.$i];
      for ( $i = 0 ; isset($integrate['translation_prices_ref'.$i]) && isset($integrate['translation_prices_dest'.$i]) ; $i++ )
      if ( $integrate['translation_prices_ref'.$i] && $integrate['translation_prices_dest'.$i] )
      {
        $pm = Doctrine::getTable('PriceManifestation')->createQuery('pm')
          ->andWhere('pm.price_id = ?',$integrate['translation_prices_dest'.$i])
          ->andWhere('pm.manifestation_id = ?',$mid)
          ->orderBy('pm.id DESC')
          ->fetchOne();
        $this->translation['prices'][$integrate['translation_prices_ref'.$i].($integrate['translation_categories_ref'.$i] ? '/'.$integrate['translation_categories_ref'.$i] : '')]
          = array('id' => $integrate['translation_prices_dest'.$i], 'value' => $pm->value);
      }
      
      $fp = fopen($files['file']['tmp_name'],'r');
      
      $transaction_ref = false;
      if ( $integrate['transaction_ref_id'] )
      {
        // get back the original transaction
        $q = Doctrine::getTable('Transaction')->createQuery()
          ->andWhere('tck.printed_at IS NULL AND tck.integrated_at IS NULL')
          ->andWhere('tck.price_id = ?',$price_default_id)
          ->andWhere('tck.manifestation_id = ?',$this->manifestation->id)
          ->andWhere('id = ?',$integrate['transaction_ref_id'])
          ->orderBy('id ASC');
        
        $transaction_ref = $q->fetchOne();
      }
      
      $transaction = new Transaction();
      
      switch ( $integrate['filetype'] ) {
      case 'digitick':
        require(dirname(__FILE__).'/batch-integrate-'.$integrate['filetype'].'.php');
        break;
      case 'fb':
        require(dirname(__FILE__).'/batch-integrate-'.$integrate['filetype'].'.php');
        break;
      case 'tkn':
        require(dirname(__FILE__).'/batch-integrate-'.$integrate['filetype'].'.php');
        break;
      default:
        $this->getUser()->setFlash('error',__("You've chosen an unimplemented feature."));
        $this->redirect('ticket/batchIntegrate?manifestation_id='.$this->manifestation->id);
        require(dirname(__FILE__).'/batch-integrate-default.php');
        break;
      }
      
      // workspace to gauge translation
      foreach ( $this->manifestation->Gauges as $gauge )
        $gauges[$gauge->workspace_id] = $gauge->id;
      
      $nbtck = $nberr = 0;
      // integrating normalized content
      foreach ( $tickets as $ticket )
      {
        // if the line references a named contact
        if ( $ticket['name'] && $ticket['firstname'] )
        {
          $charset = sfConfig::get('software_internals_charset');
          $search = array(implode('* ',explode(' ',$ticket['name'])).'*',implode('* ',explode(' ',$ticket['firstname'])).'*');
          $search = strtolower(iconv($charset['db'],$charset['ascii'],implode(' ',$search)));
          $q = Doctrine::getTable('Contact')->createQuery('c');
          if ( $ticket['postalcode'] )
            $q->andWhere('c.postalcode = ?',$ticket['postalcode']);
          if ( isset($ticket['email']) && $ticket['email'] )
            $q->andWhere('c.email = ?', $ticket['email']);
          $contacts = Doctrine::getTable('Contact')->search($search,$q)->execute();
          
          if ( $contacts->count() == 0 )
          {
            $transaction = new Transaction();
            $transaction->Contact = new Contact();
            $transaction->Contact->name = $ticket['name'];
            $transaction->Contact->firstname = $ticket['firstname'];
            $transaction->Contact->postalcode = $ticket['postalcode'];
            if ( !$ticket['city'] && $ticket['postalcode'] )
            if ( $postalcode = Doctrine::getTable('Postalcode')->createQuery()->andWhere('postalcode = ?',$ticket['postalcode'])->fetchOne() )
              $ticket['city'] = $postalcode->city;
            $transaction->Contact->city = $ticket['city'];
            $transaction->Contact->country = $ticket['country'];
            
            if ( isset($ticket['email']) && $ticket['email'] )
              $transaction->Contact->email = $ticket['email'];
          }
          else
          {
            // keep the last transaction if contact is the same, or create a new one if not
            if ( $transaction->isNew()
              || !(!is_null($transaction->contact_id) && $transaction->Contact->id == $contacts[0]->id) )
            {
              $transaction = new Transaction();
              $transaction->Contact = $contacts[0];
            }
            
            // adding a keyword
            $transaction->Contact->description =
              implode(' ',array($transaction->Contact->description,'integration-'.$ticket['type']));
          }
        }
        else // if ( !($ticket['name'] && $ticket['firstname'] )
        {
          if ( $transaction->Contact instanceof Contact )
          {
            $transaction = new Transaction();
            $transaction->Contact = NULL; // hack
          }
        }
        
        // if it's not a cancellation
        if (!( isset($ticket['cancel']) && $ticket['cancel'] ))
        {
          $tck = new Ticket();
          $tck->manifestation_id = $this->manifestation->id;
          $tck->price_name = $ticket['price_name'];
          $tck->price_id = $ticket['price_id'] ? $ticket['price_id'] : $price_default_id;
          $tck->value = $ticket['value'];
          $tck->integrated_at = date('Y-m-d H:i:s');
          $tck->gauge_id = $gauges[$ticket['workspace_id']];
          $tck->created_at = date('Y-m-d H:i:s',strtotime(isset($ticket['created_at']) && $ticket['created_at'] ? $ticket['created_at'] : NULL));
          
          // is the ticket's id found from the sequence or from the imported data ?
          if ( isset($ticket['id']) && $ticket['id'] )
            $tck->id = $ticket['id'];
          
          // seated integration...
          if ( isset($ticket['seat']) && $ticket['seat'] )
          {
            $q = Doctrine::getTable('Seat')->createQuery('s')
              ->leftJoin('s.SeatedPlan sp')
              ->leftJoin('sp.Workspaces ws')
              ->leftJoin('ws.Gauges g')
              ->andWhere('g.id = ?', $tck->gauge_id)
              ->leftJoin('sp.Location l')
              ->leftJoin('l.Manifestations m')
              ->andWhere('m.id = ?', $tck->manifestation_id)
              ->leftJoin('s.Tickets tck WITH tck.manifestation_id = ?', $tck->manifestation_id)
              ->andWhere('tck.id IS NULL')
              ->andWhere('lower(s.name) = ?', strtolower($ticket['seat']))
              ->leftJoin('s.Holds h WITH h.manifestation_id = m.id')
            ;
            if ( $seat = $q->fetchOne() )
            {
              // creates a HoldTransaction if required
              if ( $seat->Holds->count() > 0 )
              {
                if ( is_null($transaction->HoldTransaction->hold_id) )
                {
                  $transaction->HoldTransaction = new HoldTransaction;
                  $transaction->HoldTransaction->Hold = $seat->Holds[0];
                }
                $transaction->save(); // to be sure that we have a $transaction->id set
                
                if ( $transaction->HoldTransaction->hold_id != $seat->Holds[0]->id )
                {
                  $transaction->save(); // to get to the new transaction on the new hold
                  $t = new Transaction;
                  $t->Contact = $transaction->Contact;
                  $transaction = $t;
                  $transaction->HoldTransaction->Hold = $seat->Holds[0];
                  $transaction->save(); // to be sure that we have a transaction->id set
                }
              }
              
              $tck->Seat = $seat;
            }
            elseif ( sfConfig::get('sf_web_debug', false) )
              error_log('Seat not found: '.$ticket['seat']);
            
          }
          
          // Digitick & e-venement's QRCodes
          foreach ( array('barcode', 'othercode') as $id )
          if ( isset($ticket[$id]) && $ticket[$id] )
            $tck->$id = $ticket[$id];
          
          if ( !$tck->gauge_id )
            $nberr++;
          else
          {
            $nbtck++;
            $transaction->Tickets[] = $tck;
            if ( $integrate['transaction_ref_id'] && $transaction_ref !== false )
            {
              if ( $transaction_ref->Tickets->count() > 0 )
              {
                $transaction_ref->Tickets[$transaction_ref->Tickets->count()-1]->delete();
                unset($transaction_ref->Tickets[$transaction_ref->Tickets->count()-1]);
              }
              else
                $notices['no-more-refs'] = __("You've integrated more tickets than you've got in your base transaction.");
            }
          }
        }
        else
          $this->getUser()->setFlash('error',__('Tried to integrate a cancellation ticket without any referenced id. This kind of cancellation ticket has to be integrated manually.'));
        
        $transaction->save();
      }

      fclose($fp);
      $this->getContext()->getConfiguration()->loadHelpers(array('Url','I18N'));
      $this->getUser()->setFlash('notice',__("File importated with the last transaction's id %%tid%%, %%nbtck%% ticket(s), %%nberr%% error(s).",array('%%tid%%' => $transaction->id, '%%nbtck%%' => $nbtck, '%%nberr%%' => $nberr)).' -- '.implode(' ',$notices));
      //$this->redirect(url_for('ticket/batchIntegrate?manifestation_id='.$this->manifestation->id));
    }
    else
    {
      $this->getUser()->setFlash('error','Error in the form validation');
    }
  }
  
  if ( $request->hasParameter('pay') )
  {
    $this->payform->bind($request->getParameter('pay'));
    if ( $this->payform->isValid() )
    {
      try {
        $this->payform->save();
        $this->getUser()->setFlash('notice','Tickets paid');
        $this->redirect('ticket/batchIntegrate?manifestation_id='.$mid);
      }
      catch ( liEvenementException $e )
      {
        $this->getUser()->setFlash('error',__('No ticket found, no payment created'));
      }
    }
  }

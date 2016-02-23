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
$tickets = array();

$separator = ';';
$tarif_line = 'Type de tarif';
$client_line = 'Categorie Client';
$zone_line = 'Genre de Zone';
$category_line = 'Categorie Client';
$charset = sfConfig::get('software_internals_charset');
$i = 0;
$match = array(
  'transaction-id'  => $i++,
  'link-type'       => $i++,
  'external-id'     => $i++,
  'event'           => $i++,
  'date'            => $i++,
  'payment-detail'  => $i++,
  'buying-time'     => $i++,
  'buying-channel'  => $i++,
  'channel-type'    => $i++,
  'name'            => $i++,
  'firstname'       => $i++,
  'phonenumber'     => $i++,
  'email'           => $i++,
  'address'         => $i++,
  'postalcode'      => $i++,
  'city'            => $i++,
  'country'         => $i++,
  'ticket-name'     => $i++,
  'ticket-firstname'=> $i++,
  'ticket-fiscalid' => $i++,
  'ticket-id'       => $i++,
  'ticket-download' => $i++,
  'ticket-controled'=> $i++,
  'ticket-type'     => $i++,
  'guest'           => $i++,
  'organism'        => $i++,
  'organism-type'   => $i++,
  'price-name'      => $i++,
  'price-value'     => $i++,
  'zone'            => $i++,
  'seat-rank'       => $i++,
  'seat-number'     => $i++,
  'partner'         => $i++,
  'sending-process' => $i++,
  'barcode'         => $i++,
  'member-card'     => $i++,
);

for ( $i = 0 ; $line = fgetcsv($fp, 0, $separator) ; $i++ )
{
  if ( $i == 0 )
    continue;
  
  foreach ( $line as $key => $value )
    $line[$key] = iconv($charset['ms'],$charset['db'],$line[$key]);
  
  // creation of tickets to integrate
  if ( isset($line[$match['barcode']]) && $line[$match['barcode']] )
  {
    $ticket = array();
    $ticket['name']           = $line[$match['name']];
    $ticket['firstname']      = $line[$match['firstname']];
    $ticket['address']        = $line[$match['address']];
    $ticket['postalcode']     = $line[$match['postalcode']];
    $ticket['city']           = $line[$match['city']];
    $ticket['country']        = $line[$match['country']];
    $ticket['phonenumber']    = $line[$match['phonenumber']];
    
    $ticket['cancel']         = false;
    $ticket['price_name']     = $line[$match['price-name']];
    $ticket['value']          = $line[$match['price-value']];
    $ticket['price_id']       = isset($this->translation['prices'][$ticket['price_name']]) ? $this->translation['prices'][$ticket['price_name']]['id'] : NULL;
    $ticket['workspace_id']   = isset($this->translation['workspaces'][$line[$match['zone']].$glue]) ? $this->translation['workspaces'][$line[$match['zone']].$glue] : NULL;
    $ticket['value']          = isset($this->translation['prices'][$ticket['price_name']]) ? $this->translation['prices'][$ticket['price_name']]['value'] : $line[$match['ticket-value']];
    $ticket['othercode']      = $line[$match['ticket-id']];
    $ticket['barcode']        = $line[$match['barcode']];
    $ticket['type']           = 'digitick';
    $ticket['seat']           = $line[$match['seat-rank']].$line[$match['seat-number']];
    
    // created_at
    $ticket['created_at'] = $line[$match['buying-time']];
    
    $tickets[] = $ticket;
  }
}

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

/**
 * Ticket
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Ticket extends PluginTicket
{
  public function hasBeenCancelled($direction = 'both')
  {
    if ( $this->Cancelling->count() > 0 )
      return true;
    
    if ( in_array($direction,array('both','down')) )
    foreach ( $this->Duplicatas as $dup )
    if ( $dup->hasBeenCancelled('down') )
      return true;
    
    if ( in_array($direction,array('both','up')) )
    if ( !is_null($this->duplicating) )
    if ( $this->Duplicated->hasBeenCancelled('up') )
      return true;
    
    return false;
  }
  
  public function getOriginal()
  {
    if ( is_null($this->duplicating) )
      return $this;
    
    return $this->Duplicated->getOriginal();
  }
  
  public function getBarcode($salt = '')
  {
    return md5('#'.$this->id.'-'.$salt);
  }
  
  public function getIdBarcoded()
  {
    $c = ''.$this->id;
    $n = strlen($c);
    for ( $i = 12-$n ; $i > 0 ; $i-- )
      $c = '0'.$c;
    return $c;
  }
  
  public function renderSimplified()
  {
    sfApplicationConfiguration::getActive()->loadHelpers(array('Url', 'Number'));
    
    // the barcode
    $c = curl_init();
    curl_setopt_array($c, array(
      CURLOPT_URL => $url = public_path('/liBarcodePlugin/php-barcode/barcode.php?mode=html&scale=3&code='.$this->getIdBarcoded(),true),
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_RETURNTRANSFER => true,
    ));
    if (!( $barcode = curl_exec($c) ))
      error_log('Error loading the barcode: '.curl_error($c));
    curl_close($c);
    
    // the HTML code
    return sprintf(<<<EOF
  <div class="cmd-ticket">
    <div class="bc">%s</div>
    <div class="desc"><p>%s: %s</p>
      <p>%s: %s, %s</p>
      <p>%s: %s</p>
      <p>%s: %s %s</p>
      <p>%s</p>
      <p>#%s-%s<!-- transaction_id --></p>
      <p>%s</p>
      <p class="duplicate">%s</p></div><div class="clear"></div></div>
EOF
      , $barcode
      , __('Event', null, 'li_tickets_email')
      , (string)$this->Manifestation->Event
      , __('Venue', null, 'li_tickets_email')
      , (string)$this->Manifestation->Location
      , (string)$this->Gauge
      , __('Date', null, 'li_tickets_email')
      , $this->Manifestation->getShortenedDate()
      , __('Price', null, 'li_tickets_email')
      , $this->price_name
      , format_currency($this->value,'€')
      , $this->numerotation ? __('Seat #%%num%%', array('%%num%%' => $this->numerotation), 'li_tickets_email') : ($this->Manifestation->Location->getWorkspaceSeatedPlan($this->Gauge->workspace_id) ? __('Not yet allocated', null, 'li_tickets_email') : __('Seat #%%num%%', array('%%num%%' => ' N/A'), 'li_tickets_email'))
      , $this->transaction_id
      , $this->id
      , $this->Transaction->professional_id ? $this->Transaction->Professional->getFullName() : (string)$this->Transaction->Contact
      , !$this->duplicating ? '' : __('This ticket is a duplicate of #%%tid%%, it replaces and cancels any previous version of this ticket you might have recieved', array('%%tid%%' => $this->transaction_id.'-'.$this->duplicating), 'li_tickets_email')
    );
  }
  
  public function __toString()
  {
    return '#'.$this->id;
  }
}

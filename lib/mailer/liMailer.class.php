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
*    Copyright (c) 2006-2012 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2012 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
class liMailer extends sfMailer
{
  protected $email = NULL;
  protected $matcher = NULL;
  
  public function send(Swift_Mime_Message $message, &$failedRecipients = NULL)
  {
    $cpt = 0;
    if ( $this->email instanceof Email && count($to = $message->getTo()) == 1 )
    foreach ( $to as $address => $name )
    {
      $fields = array(
        'title'       => '',
        'firstname'   => '',
        'name'        => '',
        'address'     => '',
        'postalcode'  => '',
        'city'        => '',
        'country'     => '',
        'function'    => '',
        'organism'    => '',
      );
      switch ( get_class($this->matcher[$cpt]) ) {
        case 'Contact':
          foreach ( array('firstname', 'title') as $field )
            $fields[$field]   = $this->matcher[$cpt]->$field;
        case 'Organism':
          foreach ( array('name', 'address', 'postalcode', 'city', 'country') as $field )
            $fields[$field]   = $this->matcher[$cpt]->$field;
        break;
        case 'Professional':
          foreach ( array('firstname', 'title', 'name') as $field )
            $fields[$field]   = $this->matcher[$cpt]->Contact->$field;
          foreach ( array('address', 'postalcode', 'city', 'country', 'organism') as $field )
            $fields[$fields]  = $this->matcher[$cpt]->Organism->$field;
          $fields['function'] = $this->matcher[$cpt]->name ? $this->matcher[$cpt]->name : (string)$this->matcher[$cpt]->Category;
        break;
      }
      
      $replace = array('%%EMAILADDRESS%%'  => is_int($address) ? $name : $address);
      foreach ( $fields as $field => $data )
        $replace['%%'.strtoupper($field).'%%'] = $data;
      $content = str_replace(
        array_keys($replace),
        array_values($replace),
        $this->email->getFormattedContent()
      );
      $message = $this->email->removePart('text')->removePart('html')
        ->addParts($content)
        ->getMessage();
      
      $cpt++;
    }
    return parent::send($message);
  }
  public function setEmail(Email $email)
  {
    $this->email = $email;
    return $this;
  }
  /**
   * @function setMatcher
   *
   * @param $array    array   $id in the $message->to array => corresponding Contact|Professional|Organism
   **/
  public function setMatcher(array $array)
  {
    $this->matcher = $array;
    return $this;
  }
  public function getMatcher()
  {
    return $this->matcher;
  }
  public function batchSend(Swift_Message $message)
  {
    $arr = $message->getTo();
    foreach ( $arr as $address => $name )
    {
      $message->setTo(is_int($address) ? $name : array($address => $name));
      $this->send($message);
    }
    
    return count($arr) > 0;
  }
}

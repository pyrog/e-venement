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
  /**
    * FORMAT: CSV
    * ENCODING: UTF-8
    * SEPARATOR: ,
    * CONTENT STRUCTURE:
    * Id,Nom,Prénom,Adresse1,Adresse2,CP,Ville,Pays,Type Tel1,Tel1,Type Tel2,Tel2,email_perso,no_newsletter,Mémo,org_id,Organisme,Type_Organisme,admin_number,site_web,Adresse CP,Ville,Pays,org_type_tel1,org_tel1,org_type_tel2,org_tel2,org_email,org_no_newsletter,Fonction_Type,Fonction_libellé,Service,pro_tel,pro_email,pro_no_newsletter
    *
    **/
?>
<?php
class ContactOrganismCSVImportTask extends sfBaseTask{

  protected function configure() {
    $this->addArguments(array(
      new sfCommandArgument('input', sfCommandArgument::REQUIRED, 'The file to parse'),
      new sfCommandArgument('go', sfCommandArgument::OPTIONAL, 'no'),
    ));
    $this->addOptions(array(
      new sfCommandOption('no-headers', sfCommandOption::PARAMETER_OPTIONAL),
    ));
    $this->namespace = 'e-venement';
    $this->name = 'csv-import';
    $this->briefDescription = '';
    $this->detailedDescription = '';
  }

  protected function execute($arguments = array(), $options = array())
  {
    throw new liEvenementException('Work still in progress... contact@libre-informatique.fr for more information.');
    
    sfContext::createInstance($this->configuration, $options['env']);
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    if (!( $fp = fopen($arguments['input']) ))
      throw new liEvenementException('Error reading the file!!');
    
    $lines = fgetcsv($fp);
    if ( $options['no-headers'] )
      array_shift($lines);
    foreach ( $lines as $line )
    {
      $organism = $contact = NULL;
      $i = 0;
      // there is a contact
      if ( $line[1] )
      {
        $contact = new Contact;
        if ( trim($line[$i++]) )
          $contact->id = trim($line[$i]);
        $contact->name = trim($line[$i++]);
        $contact->firstname = trim($line[$i++]);
        $contact->address = trim($line[$i++])."\n".trim($line[$i++]);
        $contact->postalcode = trim($line[$i++]);
        $contact->city = trim($line[$i++]);
        $contact->country = trim($line[$i++]);
        
        if ( trim($line[$i+1]) )
        {
          $pn = new ContactPhonenumber;
          $pn->name = $line[$i++];
          $pn->number = $line[$i++];
          $contact->Phonenumbers[] = $pn;
        }
        
        $contact->email = trim($line[$i++]);
        $contact->email_no_newsletter = trim($line[$i++]) ? true : false;
        $contact->description = trim($line[$i++]);
        
        // TODO
      }
    }
    
    $this->logSection('Clean', 'Useless WebOrigins ... '.$cpt.' ... done');
  }
}

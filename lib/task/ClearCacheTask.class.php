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
*    Copyright (c) 2006-2016 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2016 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
class ClearCacheTask extends sfBaseTask{

  protected function configure() {
    $this->addArguments(array(
      new sfCommandArgument('domain', sfCommandArgument::REQUIRED, 'The domain to clear'),
      new sfCommandArgument('identifier', sfCommandArgument::OPTIONAL, 'How the identifiers starts with, clear that'),
      new sfCommandArgument('go', sfCommandArgument::OPTIONAL, 'Whether we try or we executes the clearance'),
      new sfCommandArgument('application', sfCommandArgument::OPTIONAL, '', 'default'),
    ));
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));
    $this->namespace = 'e-venement';
    $this->name = 'cache-clear';
    $this->briefDescription = 'Cache DB cache';
    $this->detailedDescription = <<<EOF
      The [cc:cache-clear|INFO] clears the DB cache used within e-venement, giving the details of what needs to be cleared
      [./symfony e-venement:cache-clear --env=dev domain [identifier]|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfContext::createInstance($this->configuration, $options['env']);
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->logSection('Cache', 'Clearing cache related to the domain "'.$arguments['domain'].'" and the identifier "'.$arguments['identifier'].'*"');
    
    $q = Doctrine::getTable('Cache')->createQuery('c')
      ->andWhere('c.domain = ?', $arguments['domain']);
    if ( isset($arguments['identifier']) )
      $q->andWhere('c.identifier LIKE ?', $arguments['identifier'].'%');
    $nb = $q->count();
    $q->delete()
      ->execute();
    
    $this->logSection('Cache', $nb.' entries cleared');
  }
}

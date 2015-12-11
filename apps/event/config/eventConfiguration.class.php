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

require_once dirname(__FILE__).'../../../../config/autoload.inc.php';

class eventConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
    parent::configure();
    sfConfig::set('sf_app_template_dir', sfConfig::get('sf_apps_dir') . '/templates');
    
    $this->dispatcher->connect('user.change_authentication', array($this, 'logAuthentication'));
  }
  
  public function logAuthentication(sfEvent $event)
  {
    $params   = $event->getParameters();
    $user     = sfContext::getInstance()->getUser();
    $request  = sfContext::getInstance()->getRequest();
    if ( !is_object($user) )
      return false;

    if (( sfConfig::get('project_login_alert_beginning_at', false) && sfConfig::get('project_login_alert_beginning_at') < time() || !sfConfig::get('project_login_alert_beginning_at', false) )
      &&( sfConfig::get('project_login_alert_ending_at', false) && sfConfig::get('project_login_alert_ending_at') > time() || !sfConfig::get('project_login_alert_ending_at', false) )
      && sfConfig::get('project_login_alert_message', false) )
      $user->setFlash('error', sfConfig::get('project_login_alert_message'));

    $auth = new Authentication();
    $auth->sf_guard_user_id = $user->getId();
    $auth->description      = $user;
    $auth->ip_address       = $request->getHttpHeader('addr','remote');
    $auth->user_agent       = $request->getHttpHeader('User-Agent');
    $auth->referer          = $request->getReferer();
    $auth->success          = $params['authenticated'];
    
    $auth->save();
  }

  public function initGarbageCollectors(sfCommandApplicationTask $task = NULL)
  {
    $this->task = $task;
    
    // Caching manifestations in the background
    $this->addGarbageCollector('manifestations-cache', function(){
      $section = 'Caching manifs';
      if (!( $url = sfConfig::get('app_cacher_public_url', false) )
        || !function_exists('curl_init') )
      {
        $this->stdout($section, 'No public URL set in the configuration (app.yml). Stopping the process.', 'ERROR');
        return $this;
      }
      
      $this->loadHelpers('CrossAppLink');
      $timeout = sfConfig::get('app_cacher_timeout', '1 day ago');
      $this->stdout($section, 'Starting the caching process...', 'COMMAND');
      
      $context = sfContext::getInstance();
      $context->getUser()->setGuardUser($user = Doctrine::getTable('sfGuardUser')->createQuery('u')
        ->andWhere('u.is_super_admin = ?', true)
        ->andWhere('u.is_active = ?', true)
        ->fetchOne());
      if ( !$user )
      {
        $this->stdout($section, 'No usable user found to build the cache. Stopping...', 'ERROR');
        return $this;
      }
      
      $q = Doctrine::getTable('Manifestation')->createQuery('m')
        ->andWhere("m.happens_at + (m.duration||' seconds')::interval > NOW() - '1 month'::interval")
        ->andWhere("m.happens_at < NOW() + '1 year'::interval")
        ->orderBy('m.happens_at');
      $nb = array();
      foreach ( $q->execute() as $manifestation )
      {
        foreach ( array('showTickets', 'showSpectators', 'statsFillingData',) as $action )
        {
          if ( !isset($nb[$action]) )
            $nb[$action] = 0;
          
          // preparing the fake request
          $request = new sfWebRequest($context->getEventDispatcher());
          $request->setAttribute('sf_route', new sfDoctrineRoute(
            'manifestation/'.$action,
            array(),
            array(),
            array('model' => 'Manifestation', 'type' => 'object')
          ));
          foreach ( $params = array(
            'id' => $manifestation->id,
            //'refresh' => 1,
          ) as $param => $value )
            $request->setParameter($param, $value);
          
          // preparing the context
          $request->getAttribute('sf_route')->bind($context, $params);
          $context['request'] = $request;
          $actions = $context->getController()->getAction('manifestation', $action);
          while ( $context->getActionStack()->popEntry() ); // clearing the stack
          $context->getActionStack()->addEntry('manifestation', $action, $actions);
          $_SERVER['REQUEST_URI'] = $uri =
            preg_replace('!/$!', '', sfConfig::get('app_cacher_public_url')).
            '/'.
            $context->getConfiguration()->getApplication().
            ($context->getConfiguration()->getEnvironment() != 'prod' ? '_'.$context->getConfiguration()->getEnvironment() : '').
            '.php'.
            '/manifestation/'.
            $manifestation->id.
            '/'.$action.
            //'?refresh'
            ''
          ;
          
          if ( !liCacher::create($request)->needsRefresh() )
          {
            if ( sfConfig::get('sf_web_debug', false) )
              $this->stdout($section, '  x Refreshing the action '.$action.' is not needed for manifestation #'.$manifestation->id, 'INFO');
            continue;
          }
          
          // executing the action
          $actions->{'execute'.ucfirst($action)}($request);
          
          // logs
          if ( sfConfig::get('sf_web_debug', false) )
            $this->stdout($section, '  - Action '.$action.' done for manifestation #'.$manifestation->id, 'INFO');
          $nb[$action]++;
        }
      }
      
      if ( !$nb )
        $this->stdout($section, '[OK] No manifestation found for cache updating.', 'INFO');
      else
      {
        if ( array_sum($nb) == 0 )
          $this->stdout($section, "[KO] Nothing has to be updated, cache is up-to-date everywhere.", 'INFO');
        else
        foreach ( $nb as $action => $i )
        if ( $i > 0 )
          $this->stdout($section, "[OK] $action cache created for $i manifestations", 'INFO');
      }
    });
    
    return $this;
  }
}

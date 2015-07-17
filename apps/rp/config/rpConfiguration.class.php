<?php

require_once dirname(__FILE__).'../../../../config/autoload.inc.php';

class rpConfiguration extends sfApplicationConfiguration
{
  protected $collectors = array();
  protected $task;
  protected $init_configuration = false;
  protected $bad_indexes_email = array(
    'orange',
    'sfr',
    'free',
    'yahoo',
    'gmail',
  );
  
  public function setup()
  {
    if (!( sfContext::hasInstance() && get_class(sfContext::getInstance()->getConfiguration()) != get_class($this) ))
      $this->enablePlugins(array('liClassLoaderPlugin', 'sfDomPDFPlugin', 'liBarcodePlugin'));
    parent::setup();
  }
  public function configure()
  {
    parent::configure();
    sfConfig::set('sf_app_template_dir', sfConfig::get('sf_apps_dir') . '/templates');
    
    $this->dispatcher->connect('admin.save_object', array($this, 'setSpecialFlash'));
    $this->dispatcher->connect('admin.save_object', array($this, 'addPhoneNumber'));
    $this->dispatcher->connect('user.change_authentication', array($this, 'logAuthentication'));
  }
  public function initialize()
  {
    if (!( sfContext::hasInstance() && get_class(sfContext::getInstance()->getConfiguration()) != get_class($this) ))
      $this->enableSecondWavePlugins(sfConfig::get('app_options_plugins', array()));
    ProjectConfiguration::initialize();
  }
  
  public static function changeTemplatesDir(sfAction $action)
  {
    if ( sfConfig::get('app_options_design',false) && sfConfig::get(sfConfig::get('app_options_design').'_active',false) )
    {
      $params = $action->getRoute()->getParameters();
      $action->setTemplate('_'.sfConfig::get('app_options_design','').'/'.($action->getTemplate() ? $action->getTemplate() : $params['action']));
    }
  }
  
  public function addPhoneNumber(sfEvent $event)
  {
    $params = $event->getParameters();
    
    // Phone
    if ( $params['object'] instanceof Contact || $params['object'] instanceof Organism )
    {
      if ( $params['object'] instanceof Contact )
      {
        $params = $event->getSubject()->getRequestParameter('contact');
        $pn = new ContactPhonenumber();
        $pn->contact_id = $event->getSubject()->contact->id;
      }
      else
      {
        $params = $event->getSubject()->getRequestParameter('organism');
        $pn = new OrganismPhonenumber();
        $pn->organism_id = $event->getSubject()->organism->id;
      }
      
      if ( $event->getSubject()->form->isValid() && isset($params['phone_number']) && $params['phone_number'] )
      {
        $pn->name = $params['phone_type'];
        $pn->number = $params['phone_number'];
        $pn->save();
      }
    }
  }
  public function setSpecialFlash(sfEvent $event)
  {
    $params = $event->getParameters();
    
    // Email
    if ( $params['object'] instanceof Email )
    if ( !$params['object']->isATest() )
      $event->getSubject()->getUser()->setFlash('success',"Your email have been sent correctly.");
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
    
    // Bad indexes removal
    $this->addGarbageCollector('indexes-removal', function(){
      $section = 'Indexes removal';
      $this->stdout($section, 'Removing bad indexes...', 'COMMAND');
      $nb = 0;
      
      $q = "DELETE FROM contact_index WHERE field = 'email' AND keyword IN ('".implode("','", $this->bad_indexes_email)."')";
      $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
      $stmt = $pdo->prepare($q);
      $stmt->execute();
      $this->stdout($section, "[OK] indexes removed", 'INFO');
    });
  }
}

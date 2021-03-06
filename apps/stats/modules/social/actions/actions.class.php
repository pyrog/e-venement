<?php

/**
 * social actions.
 *
 * @package    e-venement
 * @subpackage social
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class socialActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    if ( $request->hasParameter('criterias') )
    {
      $this->criterias = $request->getParameter('criterias');
      $this->setCriterias($this->criterias);
      $this->redirect($this->getContext()->getModuleName().'/index');
    }
    
    $this->form = new StatsCriteriasForm();
    $this->form
      ->addGroupsCriteria()
      ->removeDatesCriteria()
      ->addStrictContactsCriteria()
    ;
    if ( is_array($this->getCriterias()) )
      $this->form->bind($this->getCriterias());
  }
  
  protected function getCriterias()
  {
    return $this->getUser()->getAttribute('stats.criterias',array(),'admin_module');
  }
  protected function setCriterias($values)
  {
    $this->getUser()->setAttribute('stats.criterias',$values,'admin_module');
    return $this;
  }
  
  public function executeCsv(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N','Date','CrossAppLink','Number'));
    $param = $request->getParameter('id');
    
    $this->lines = $this->getData($param)->toArray();
    
    switch ( $param ) {
    case 'fs':
      $this->name = __('Familial situations');
      break;
    case 'fq':
      $this->name = __('Familial quotients');
      break;
    case 'tor':
      $this->name = __('Types of resources');
      break;
    }
    
    $total = 0;
    foreach ( $this->lines as $line )
      $total += $line['nb'];
    
    foreach ( $this->lines as $key => $line )
      $this->lines[$key]['percent'] = format_number(round($line['nb']*100/$total,2));
    
    $params = OptionCsvForm::getDBOptions();
    $this->options = array(
      'ms' => in_array('microsoft',$params['option']),
      'fields' => array('name','nb','percent'),
      'tunnel' => false,
      'noheader' => false,
    );
    
    $this->outstream = 'php://output';
    $this->delimiter = $this->options['ms'] ? ';' : ',';
    $this->enclosure = '"';
    $this->charset   = sfConfig::get('software_internals_charset');
    
    sfConfig::set('sf_escaping_strategy', false);
    $confcsv = sfConfig::get('software_internals_csv'); if ( isset($confcsv['set_charset']) && $confcsv['set_charset'] ) sfConfig::set('sf_charset', $this->options['ms'] ? $this->charset['ms'] : $this->charset['db']);
    
    if ( $request->hasParameter('debug') )
    {
      $this->setLayout(true);
      $this->getResponse()->sendHttpHeaders();
    }
    else
      sfConfig::set('sf_web_debug', false);
  }
  
  public function executeData(sfWebRequest $request)
  {
    $this->data = $this->getData($request->getParameter('id'));
    
    if ( !$request->hasParameter('debug') )
    {
      $this->setLayout('raw');
      sfConfig::set('sf_debug',false);
      $this->getResponse()->setContentType('application/json');
    }
  }
  
  protected function getData($id)
  {
    switch ( $id ) {
    case 'fs':
      $table = 'FamilialSituation';
      break;
    case 'fq':
      $table = 'FamilialQuotient';
      break;
    case 'tor':
      $table = 'TypeOfResources';
      break;
    default:
      throw new liEvenementException("You forgot to specify what kind of data you are expecting or you requested something not implemented.");
    }
    
    $q = Doctrine_Query::create()->from($table.' t')
      ->select('t.id, t.name')
      ->leftJoin('t.Contacts c')
      ->leftJoin('c.Groups g')
      ->groupBy('t.id, t.name')
      ->orderBy('t.name');
    
    $criterias = $this->getCriterias();
    if ( isset($criterias['groups_list']) && $criterias['groups_list'] )
      $q->andWhereIn('g.id',$criterias['groups_list']);
    
    if ( isset($criterias['strict_contacts']) && $criterias['strict_contacts'] )
      $q->addSelect('count(DISTINCT (c.id)) AS nb');
    else
      $q->leftJoin('c.YOBs y')
        ->addSelect('count(DISTINCT (c.id, y.id)) AS nb');
    
    return $q->execute();
  }
}

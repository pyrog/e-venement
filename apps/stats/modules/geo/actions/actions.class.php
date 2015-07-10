<?php

/**
 * geo actions.
 *
 * @package    e-venement
 * @subpackage geo
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class geoActions extends sfActions
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
      ->addByTicketsCriteria()
      ->addEventCriterias()
      ->addManifestationCriteria()
      ->addGroupsCriteria();
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
    
    $criterias = $this->getCriterias();
    $this->data = $this->getData($request->getParameter('type','ego'), isset($criterias['by_tickets']) && $criterias['by_tickets'] === 'y');
    
    $total = 0;
    foreach ( $this->data['nb'] as $data )
      $total += $data;
    $totalvalue = 0;
    foreach ( $this->data['value'] as $data )
      $totalvalue += $data;
    
    $this->lines = array();
    foreach ( $this->data['nb'] as $name => $data )
    {
      $this->lines[$name] = array(
        'name' => __($name),
        'qty' => $data,
        'percent' => format_number(round($data*100/$total,2)),
      );
    }
    foreach ( $this->data['value'] as $name => $data )
    {
      $this->lines[$name]['value'] = format_currency($data, '€');
      $this->lines[$name]['value%'] = format_number(round($data*100/$totalvalue, 2));
    }
    
    $this->lines['total'] = array(
      'name'    => __('Total'),
      'qty'     => $total,
      'percent' => 100,
      'value'   => format_currency($totalvalue,'€'),
      'value%' => 100,
    );
    
    $params = OptionCsvForm::getDBOptions();
    $this->options = array(
      'ms' => in_array('microsoft',$params['option']),
      'fields' => array('name','qty','percent','value','value%'),
      'tunnel' => false,
      'noheader' => false,
    );
    
    $this->outstream = 'php://output';
    $this->delimiter = $this->options['ms'] ? ';' : ',';
    $this->enclosure = '"';
    $this->charset   = sfConfig::get('software_internals_charset');
    
    sfConfig::set('sf_escaping_strategy', false);
    $confcsv = sfConfig::get('software_internals_csv');
    if ( isset($confcsv['set_charset']) && $confcsv['set_charset'] ) sfConfig::set('sf_charset', $this->options['ms'] ? $this->charset['ms'] : $this->charset['db']);
    
    if ( $request->hasParameter('debug') )
    {
      $this->setLayout('layout');
      $this->getResponse()->sendHttpHeaders();
    }
    else
      sfConfig::set('sf_web_debug', false);
  }
  
  public function executeData(sfWebRequest $request)
  {
    $criterias = $this->getCriterias();
    $data = $this->getData($request->getParameter('type','ego'), isset($criterias['by_tickets']) && $criterias['by_tickets'] === 'y');
    $this->data = $data['nb'];
    
    if ( !$request->hasParameter('debug') )
    {
      $this->setLayout('raw');
      sfConfig::set('sf_debug',false);
      $this->getResponse()->setContentType('application/json');
    }
  }
  
  protected function buildQuery()
  {
    $q = $this->addFiltersToQuery(Doctrine_Query::create()->from('Contact c'))
      ->leftJoin('c.Transactions t')
      ->leftJoin('t.Professional pro')
      ->leftJoin('pro.Organism o')
      ->leftJoin('t.Tickets tck')
      ->andWhere('tck.printed_at IS NOT NULL OR tck.integrated_at IS NOT NULL')
      ->andWhere('tck.cancelling IS NULL AND tck.id NOT IN (SELECT tck2.cancelling FROM Ticket tck2 WHERE cancelling IS NOT NULL)')
      ->andWhere('tck.duplicating IS NULL')
      ->leftJoin('tck.Gauge g')
      ->andWhereIn('g.workspace_id', array_keys($this->getUser()->getWorkspacesCredentials()))
      ->leftJoin('tck.Manifestation m')
      ->leftJoin('m.Event e')
      ->andWhereIn('e.meta_event_id', array_keys($this->getUser()->getMetaEventsCredentials()))
      ->leftJoin('tck.Price p')
      ->leftJoin('p.Users u')
      ->andWhere('u.id = ?', $this->getUser()->getId())
    ;
    return $q;
  }
  protected function addFiltersToQuery(Doctrine_Query $q)
  {
    $criterias = $this->getCriterias();
    
    if ( isset($criterias['groups_list']) && is_array($criterias['groups_list']) )
    {
      $q->leftJoin('c.Groups gc')
        ->leftJoin('pro.Groups gp')
        ->andWhere('(TRUE')
        ->andWhereIn('gc.id', $criterias['groups_list'])
        ->orWhereIn('gp.id', $criterias['groups_list'])
        ->andWhere('TRUE)')
      ;
    }
    
    if ( isset($criterias['meta_events_list']) && is_array($criterias['meta_events_list']) )
      $q->andWhereIn('e.meta_event_id', $criterias['meta_events_list']);
    if ( isset($criterias['event_categories_list']) && is_array($criterias['event_categories_list']) )
      $q->andWhereIn('e.event_category_id', $criterias['event_categories_list']);
    if ( isset($criterias['workspaces_list']) && is_array($criterias['workspaces_list']) )
      $q->andWhereIn('g.workspace_id', $criterias['workspaces_list']);
    if ( isset($criterias['sf_guard_users_list']) && is_array($criterias['sf_guard_users_list']) )
      $q->andWhereIn('tck.sf_guard_user_id', $criterias['sf_guard_users_list']);
    if ( isset($criterias['events_list']) && is_array($criterias['events_list']) )
      $q->andWhereIn('m.event_id', $criterias['events_list']);
    if ( isset($criterias['manifestations_list']) && is_array($criterias['manifestations_list']) )
      $q->andWhereIn('m.id', $criterias['manifestations_list']);
    
    if ( isset($criterias['dates']) && is_array($criterias['dates']) )
    {
      foreach ( array('from' => '>=', 'to' => '<') as $margin => $operand )
      if ( isset($criterias['dates'][$margin]) && is_array($criterias['dates'][$margin]) )
      if ( isset($criterias['dates'][$margin]['day']) && isset($criterias['dates'][$margin]['month']) && isset($criterias['dates'][$margin]['year']) )
      if ( $criterias['dates'][$margin]['day'] && $criterias['dates'][$margin]['month'] && $criterias['dates'][$margin]['year'])
      {
        $q->andWhere(
          'tck.printed_at '.$operand.' ? OR tck.printed_at IS NULL AND tck.integrated_at '.$operand.' ?',
          array(
            $criterias['dates'][$margin]['year'].'-'.$criterias['dates'][$margin]['month'].'-'.$criterias['dates'][$margin]['day'],
            $criterias['dates'][$margin]['year'].'-'.$criterias['dates'][$margin]['month'].'-'.$criterias['dates'][$margin]['day']
          )
        );
      }
    }
    
    return $q;
  }
  
  protected function getData($type = 'ego', $count_tickets = false)
  {
    $this->type = $type;
    $res = array('nb' => array(), 'value' => array());
    $total = $totalval = 0;
    switch ( $type ) {
    
    case 'postalcodes':
      foreach ( $arr = $this->buildQuery()
        ->select('c.id')
        ->addSelect('(CASE WHEN pro.id IS NOT NULL THEN o.postalcode ELSE c.postalcode END) AS postalcode')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id, c.postalcode, pro.id, o.postalcode')
        ->fetchArray() as $pc )
      {
        if ( !isset($res['nb'][$pc['postalcode']]) )
          $res['nb'][$pc['postalcode']] = 0;
        $res['nb'][$pc['postalcode']] += $count_tickets ? $pc['qty'] : 1;
        if ( !isset($res['value'][$pc['postalcode']]) )
          $res['value'][$pc['postalcode']] = 0;
        $res['value'][$pc['postalcode']] += $pc['sum'];
      }
      arsort($res['nb']);
      arsort($res['value']);
      
      $cpt = $others = $othersval = 0;
      foreach ( $res['nb'] as $code => $qty )
      {
        if ( intval($code).'' !== ''.$code )
        {
          $others += $qty;
          $othersval += $res['value'][$code];
          unset($res['nb'][$code]);
          unset($res['value'][$code]);
          continue;
        }
        if ( $cpt >= sfConfig::get('app_geo_limits_'.$type, 8) )
        {
          $others += $qty;
          $othersval += $res['value'][$code];
          unset($res['nb'][$code]);
          unset($res['value'][$code]);
        }
        $cpt++;
      }
      $res['nb']['others'] = $others;
      $res['value']['others'] = $othersval;
      arsort($res['nb']);
      arsort($res['value']);
    break;
    
    case 'departments':
      foreach ( $this->buildQuery()
        ->select('c.id')
        ->addSelect('substr(CASE WHEN pro.id IS NOT NULL THEN o.postalcode ELSE c.postalcode END,1,2) AS dpt')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id, c.postalcode, pro.id, o.postalcode')
        ->fetchArray() as $pc )
      {
        if ( !isset($res['nb'][$pc['dpt']]) )
          $res['nb'][$pc['dpt']] = 0;
        $res['nb'][$pc['dpt']] += $count_tickets ? $pc['qty'] : 1;
        $total += $count_tickets ? $pc['qty'] : 1;
        if ( !isset($res['value'][$pc['dpt']]) )
          $res['value'][$pc['dpt']] = 0;
        $res['value'][$pc['dpt']] += $pc['sum'];
        $total += $count_tickets ? $pc['qty'] : 1;
        $totalval += $pc['sum'];
      }
      arsort($res['nb']);
      
      $cpt = $others = $othersval = 0;
      foreach ( $res['nb'] as $code => $qty )
      {
        if ( intval($code).'' !== ''.$code )
        {
          $others += $qty;
          $othersval += $res['value'][$code];
          unset($res['nb'][$code]);
          unset($res['value'][$code]);
          continue;
        }
        if ( $cpt >= sfConfig::get('app_geo_limits_'.$type, 4) )
        {
          $others += $qty;
          $othersval += $res['value'][$code];
          unset($res['nb'][$code]);
          unset($res['value'][$code]);
        }
        $cpt++;
      }
      $res['nb']['others'] = $others;
      $res['value']['others'] = $othersval;
      
      foreach ( Doctrine::getTable('GeoFrDepartment')->createQuery('gd')
        ->andWhereIn('gd.num', array_keys($res['nb']))
        ->execute() as $dpt )
      {
        $res['nb'][$dpt->name] = $res['nb'][$dpt->num];
        $res['value'][$dpt->name] = $res['value'][$dpt->num];
        unset($res['nb'][$dpt->num]);
        unset($res['value'][$dpt->num]);
      }
      arsort($res['nb']);
    break;
    
    case 'regions':
      $dpts = array();
      foreach ( Doctrine::getTable('GeoFrDepartment')->createQuery('gd')
        ->leftJoin('gd.Region gr')
        ->select('gd.num, gr.id AS region_id, gr.name AS region')
        ->fetchArray() as $dpt )
        $dpts[$dpt['num']] = $dpt['region'];
      $dpts[''] = '';
      
      foreach ( $this->buildQuery()
        ->select('c.id')
        ->addSelect('substr(CASE WHEN pro.id IS NOT NULL THEN o.postalcode ELSE c.postalcode END,1,2) AS dpt')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id, c.postalcode, pro.id, o.postalcode')
        ->fetchArray() as $pc )
      {
        if ( !isset($dpts[trim($pc['dpt'])]) )
          $pc['dpt'] = '';
        if ( !isset($res['nb'][$dpts[trim($pc['dpt'])]]) )
          $res['nb'][$dpts[trim($pc['dpt'])]] = 0;
        $res['nb'][$dpts[trim($pc['dpt'])]] += $count_tickets ? $pc['qty'] : 1;
        if ( !isset($res['value'][$dpts[trim($pc['dpt'])]]) )
          $res['value'][$dpts[trim($pc['dpt'])]] = 0;
        $res['value'][$dpts[trim($pc['dpt'])]] += $pc['sum'];
        $total += $count_tickets ? $pc['qty'] : 1;
        $totalval += $pc['sum'];
      }
      $others = $othersval = 0;
      if ( isset($res['nb']['']) )
      {
        $others += $res['nb'][''];
        unset($res['nb']['']);
      }
      if ( isset($res['value']['']) )
      {
        $othersval += $res['value'][''];
        unset($res['value']['']);
      }
      
      $cpt = 0;
      foreach ( $res['nb'] as $code => $qty )
      {
        if ( $cpt >= sfConfig::get('app_geo_limits_'.$type, 4) )
        {
          $others += $qty;
          $othersval += $qty;
          unset($res['nb'][$code]);
          unset($res['value'][$code]);
        }
        $cpt++;
      }
      $res['nb']['others'] = $others;
      $res['value']['others'] = $othersval;
      arsort($res['nb']);
    break;
    
    case 'countries':
      $dpts = array();
      $tmp = sfConfig::get('app_about_client', array());
      $default_country = isset($tmp['country']) ? $tmp['country'] : '';
      
      foreach ( $this->buildQuery()
        ->select('c.id')
        ->addSelect('(CASE WHEN pro.id IS NOT NULL THEN o.country ELSE c.country END) AS country')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id, c.country, pro.id, o.country')
        ->fetchArray() as $pc )
      {
        if ( !trim($pc['country']) )
          $pc['country'] = $default_country;
        $pc['country'] = trim(strtolower($pc['country']));
        if ( !isset($res['nb'][$pc['country']]) )
          $res['nb'][$pc['country']] = 0;
        $res['nb'][$pc['country']] += $count_tickets ? $pc['qty'] : 1;
        $total = $count_tickets ? $pc['qty'] : 1;
        if ( !isset($res['value'][$pc['country']]) )
          $res['value'][$pc['country']] = 0;
        $res['value'][$pc['country']] += $pc['sum'];
        $totalval = $pc['qty'];
      }
      $others = $othersval = 0;
      if ( isset($res['nb']['']) )
      {
        $others += $res['nb'][''];
        unset($res['nb']['']);
      }
      if ( isset($res['value']['']) )
      {
        $othersval += $res['value'][''];
        unset($res['value']['']);
      }
      
      $cpt = 0;
      foreach ( $res['nb'] as $country => $qty )
      {
        if ( $cpt >= sfConfig::get('app_geo_limits_'.$type, 4) )
        {
          $others += $qty;
          $othersval += $res['value'][$country];
          unset($res['nb'][$country]);
          unset($res['value'][$country]);
        }
        elseif ( ucwords($country) != $country )
        {
          $res['nb'][ucwords($country)] = $res['nb'][$country];
          $res['value'][ucwords($country)] = $res['value'][$country];
          unset($res['nb'][$country]);
          unset($res['value'][$country]);
        }
        $cpt++;
      }
      $res['nb']['others'] = $others;
      $res['value']['others'] = $othersval;
      arsort($res['nb']);
    break;
    
    default:
      $client = sfConfig::get('app_about_client',array());
      $res['nb'] = $res['value'] = array('exact' => 0, 'department' => 0, 'region' => 0, 'country' => 0, 'others' => 0);
      $q = $this->buildQuery()
        ->select('c.id')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id')
        ->andWhere('(pro.id IS NULL AND c.postalcode = ? OR pro.id IS NOT NULL AND o.postalcode = ?)', array($client['postalcode'], $client['postalcode']))
        ->andWhere('(pro.id IS NULL AND (c.country ILIKE ? OR c.country IS NULL OR c.country = ?) OR pro.id IS NOT NULL AND (o.country ILIKE ? OR o.country IS NULL OR o.country = ?))', array(isset($client['country']) ? $client['country'] : 'France', '', isset($client['country']) ? $client['country'] : 'France', '',));
      $res['nb']['exact'] = $res['value']['exact'] = 0;
      if ( $count_tickets )
      foreach ( $arr = $q->fetchArray() as $c )
      {
        $res['nb']['exact'] += $c['qty'];
        $total += $c['qty'];
      }
      else
      {
        $res['nb']['exact'] = $q->count();
        $total = 1;
      }
      foreach ( $arr as $c )
      {
        $res['value']['exact'] += $c['sum'];
        $totalval += $c['sum'];
      }
      
      $q = $this->buildQuery()
        ->select('c.id')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id')
        ->andWhere('substring(CASE WHEN pro.id IS NULL THEN c.postalcode ELSE o.postalcode END, 1, 2) = substring(?, 1, 2)', $client['postalcode'])
        ->andWhere('(pro.id IS NULL AND (c.country ILIKE ? OR c.country IS NULL OR c.country = ?) OR pro.id IS NOT NULL AND (o.country ILIKE ? OR o.country IS NULL OR o.country = ?))', array(isset($client['country']) ? $client['country'] : 'France', '', isset($client['country']) ? $client['country'] : 'France', '',));
      $res['nb']['department'] = -$res['nb']['exact'];
      $res['value']['department'] = -$res['value']['exact'];
      if ( $count_tickets )
      foreach ( $arr = $q->fetchArray() as $c )
      {
        $res['nb']['department'] += $c['qty'];
        $total += $c['qty'];
      }
      else
      {
        $res['nb']['department'] += $q->count();
        $total += 1;
      }
      foreach ( $arr as $c )
      {
        $res['value']['department'] += $c['sum'];
        $totalval += $c['sum'];
      }
      
      $q = $this->buildQuery()
        ->select('c.id')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id')
        ->andWhere('substring(CASE WHEN pro.id IS NULL THEN c.postalcode ELSE o.postalcode END, 1, 2) IN (SELECT gd.num FROM GeoFrRegion gr LEFT JOIN gr.Departments gd LEFT JOIN gr.Departments gdc WHERE gdc.num = substring(?, 1, 2))', $client['postalcode'])
        ->andWhere('(pro.id IS NULL AND (c.country ILIKE ? OR c.country IS NULL OR c.country = ?) OR pro.id IS NOT NULL AND (o.country ILIKE ? OR o.country IS NULL OR o.country = ?))', array(isset($client['country']) ? $client['country'] : 'France', '', isset($client['country']) ? $client['country'] : 'France', '',));
      $res['nb']['region'] = -$res['nb']['department'] -$res['nb']['exact'];
      $res['value']['region'] = -$res['value']['department'] -$res['value']['exact'];
      if ( $count_tickets )
      foreach ( $arr = $q->fetchArray() as $c )
      {
        $res['nb']['region'] += $c['qty'];
        $total += $c['qty'];
      }
      else
      {
        $res['nb']['region'] += $q->count();
        $total += 1;
      }
      foreach ( $arr as $c )
      {
        $res['value']['region'] += $c['sum'];
        $totalval += $c['sum'];
      }
      
      $q = $this->buildQuery()
        ->select('c.id')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id')
        ->andWhere('(pro.id IS NULL AND (c.country ILIKE ? OR c.country IS NULL OR c.country = ?) OR pro.id IS NOT NULL AND (o.country ILIKE ? OR o.country IS NULL OR o.country = ?))', array(isset($client['country']) ? $client['country'] : 'France', '', isset($client['country']) ? $client['country'] : 'France', '',));
      $res['nb']['country'] = -$res['nb']['region'] -$res['nb']['department'] -$res['nb']['exact'];
      $res['value']['country'] = -$res['value']['region'] -$res['value']['department'] -$res['value']['exact'];
      if ( $count_tickets )
      foreach ( $arr = $q->fetchArray() as $c )
      {
        $res['nb']['country'] += $c['qty'];
        $total += $c['qty'];
      }
      else
      {
        $res['nb']['country'] += $q->count();
        $total += 1;
      }
      foreach ( $arr as $c )
      {
        $res['value']['country'] += $c['sum'];
        $totalval += $c['sum'];
      }
      
      $q = $this->buildQuery()
        ->select('c.id')
        ->addSelect('count(DISTINCT tck.id) AS qty')
        ->addSelect('sum(tck.value) AS sum')
        ->groupBy('c.id');
      $res['nb']['others'] = -$res['nb']['country'] -$res['nb']['region'] -$res['nb']['department'] -$res['nb']['exact'];
      $res['value']['others'] = -$res['value']['country'] -$res['value']['region'] -$res['value']['department'] -$res['value']['exact'];
      if ( $count_tickets )
      foreach ( $arr = $q->fetchArray() as $c )
      {
        $res['nb']['others'] += $c['qty'];
        $total += $c['qty'];
      }
      else
      {
        $res['nb']['others'] += $q->count();
        $total += 1;
      }
      foreach ( $arr as $c )
      {
        $res['value']['others'] += $c['sum'];
        $totalval += $c['sum'];
      }
      break;
    }
    
    return $res;
  }
}

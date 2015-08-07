<?php

require_once dirname(__FILE__).'/../lib/email_templateGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/email_templateGeneratorHelper.class.php';

/**
 * email_template actions.
 *
 * @package    e-venement
 * @subpackage email_template
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class email_templateActions extends autoEmail_templateActions
{
  public function executeCreateEmail(sfWebRequest $request)
  {
    $this->redirect('email/new?template='.$request->getParameter('id'));
  }
}

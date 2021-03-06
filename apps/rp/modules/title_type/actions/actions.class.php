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
*    Copyright (c) 2006-2011 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2011 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php

require_once dirname(__FILE__).'/../lib/title_typeGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/title_typeGeneratorHelper.class.php';

/**
 * title_type actions.
 *
 * @package    e-venement
 * @subpackage title_type
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class title_typeActions extends autoTitle_typeActions
{
  public function executeAjax(sfWebRequest $request)
  {
    $this->getResponse()->setContentType('application/json');
    $request = Doctrine::getTable('TitleType')->createQuery()
      ->where('name ILIKE ?',array('%'.$request->getParameter('q').'%'))
      ->limit($request->getParameter('limit'))
      ->execute()
      ->getData();
    
    $titles = array();
    foreach ( $request as $title )
      $titles[$title->id] = (string) $title;
    
    return $this->renderText(json_encode($titles));
  }
}

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

class liPDFPlugin
{
  protected $content, $pdf, $verbose_in_error_log;
  
  public function __construct($html, $verbose_in_error_log = true)
  {
    $this->content = $html;
    $this->verbose_in_error_log = $verbose_in_error_log;

    $composer_dir = sfConfig::get('sf_lib_dir').'/vendor/composer/';
    $wkhtmltopdf = $composer_dir.'h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';
    
    // with wkhtmltopdf
    if ( sfConfig::get('project_tickets_wkhtmltopdf', 'enabled') && is_executable($wkhtmltopdf) )
    try
    {
      require_once $composer_dir.'autoload.php';
      $snappy = new Knp\Snappy\Pdf($wkhtmltopdf);
      $this->pdf = $snappy->getOutputFromHtml(get_partial('global/get_tickets_pdf', array('tickets_html' => $this->content)));
      return;
    } catch ( RuntimeException $e ) {
      $this
        ->log('Printing tickets: even if the executable is present, "wkhtmltopdf" is not working. Falling back to classic PDF generation.')
        ->log('Error: '.$e->getMessage());
    }

    // without wkhtmltopdf, using DomPDF
    $pdf = new sfDomPDFPlugin();
    $pdf->setInput(get_partial('global/get_tickets_pdf', array('tickets_html' => $content)));
    $this->pdf = $pdf->render();
  }

  public function getPDF()
  {
    return $this->pdf;
  }
  
  protected function log($msg)
  {
    if ( $verbose_in_error_log )
      error_log($msg);
    return $this;
  }
}



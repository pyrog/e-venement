<?php
  if ( sfConfig::get('sf_web_debug', false) )
  {
    echo get_partial('global/get_tickets_pdf', array('tickets_html' => $content));
    return;
  }
  
  $composer_dir = sfConfig::get('sf_lib_dir').'/vendor/composer/';
  $wkhtmltopdf = $composer_dir.'h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';
  
  // with wkhtmltopdf
  if ( sfConfig::get('app_tickets_wkhtmltopdf', 'enabled') && is_executable($wkhtmltopdf) )
  try
  {
    require_once $composer_dir.'autoload.php';
    $snappy = new Knp\Snappy\Pdf($wkhtmltopdf);
    echo $snappy->getOutputFromHtml(get_partial('global/get_tickets_pdf', array('tickets_html' => $content)));
    return;
  } catch ( RuntimeException $e ) {
    error_log('Printing tickets: even if the executable is present, "wkhtmltopdf" is not working. Falling back to classic PDF generation');
    error_log('Error: '.$e->getMessage());
  }

  // without wkhtmltopdf, using DomPDF
  $pdf = new sfDomPDFPlugin();
  $pdf->setInput(get_partial('global/get_tickets_pdf', array('tickets_html' => $content)));
  echo $pdf->render();

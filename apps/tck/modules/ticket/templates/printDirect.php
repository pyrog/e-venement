<?php
  if ( sfConfig::get('sf_web_debug', false) )
  {
    echo get_partial('global/get_tickets_pdf', array('tickets_html' => $content));
    return;
  }
  
  $generator = new liPDFPlugin(get_partial('global/get_tickets_pdf', array('tickets_html' => $content)));

  // if no printer has been found, then prints out a PDF
  if ( !$printer )
  {
    echo $generator->getPDF();
    return;
  }
  
  // records the PDF as a file, and remember the name of that file
  $filename = sfConfig::get('sf_app_cache_dir').'/tickets-'.date('YmdHis').'-'.rand(1000000, 9999999).'.pdf';
  file_put_contents($filename, $generator->getPDF());
  
  // defining which PPD file we will use
  switch ( $type ) {
  case 'boca':
    $ppd = sfConfig::get('sf_root_dir').'/data/cups/Boca.ppd';
    break;
  default:
    $ppd = sfConfig::get('sf_root_dir').'/data/cups/StarTSP700.ppd';
    break;
  }
  
  $cmd = sprintf('%s -e -m printer/pqueue -p %s %s 2> /dev/null | %s', $paths['cupsfilter'], $ppd, $filename, $paths['base64']);
  if ( sfConfig::get('sf_web_debug', false) )
    error_log("Executing: $cmd...");
  exec($cmd, $raw);
  echo implode('', $raw);
?>

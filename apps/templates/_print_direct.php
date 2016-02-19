<?php
  // records the PDF as a file, and remember the name of that file
  $filename = sfConfig::get('sf_app_cache_dir').'/tickets-'.date('YmdHis').'-'.rand(1000000, 9999999).'.pdf';
  file_put_contents($filename, $pdf);
  
  // defining which PPD file we will use
  switch ( $printer ) {
  case 'boca':
    $ppd = sfConfig::get('sf_root_dir').'/data/cups/Boca.ppd';
    break;
  default:
    $ppd = sfConfig::get('sf_root_dir').'/data/cups/StarTSP700.ppd';
    break;
  }

  $paths = sfConfig::get('project_internals_exec_path', sfConfig::get('software_internals_exec_path'));
  $cmd = sprintf('%s -e -m printer/pqueue -p %s %s 2> /dev/null | %s', $paths['cupsfilter'], $ppd, $filename, $paths['base64']);
  if ( sfConfig::get('sf_web_debug', false) )
    error_log("Executing: $cmd...");
  exec($cmd, $raw);
  echo implode('', $raw);
?>

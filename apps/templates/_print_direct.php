<?php
  // records the PDF as a file, and remember the name of that file
  $filename = sfConfig::get('sf_app_cache_dir').'/file-'.date('YmdHis').'-'.rand(1000000, 9999999).'.pdf';
  file_put_contents($filename, $sf_data->getRaw('pdf'));
  
  // defining which PPD file we will use
  $usb = sfConfig::get('project_internals_usb', array());
  $declination = isset($usb['drivers']) && isset($usb['drivers'][$printer]) ? $usb['drivers'][$printer] : '';
  $suffix = isset($suffix) ? $suffix : '';
  switch ( $printer ) {
  case 'boca':
    $ppd = sprintf('%s/data/cups/Boca%s%s.ppd', sfConfig::get('sf_root_dir'), $declination, $suffix);
    break;
  default:
    $ppd = sprintf('%s/data/cups/StarTSP700%s%s.ppd', sfConfig::get('sf_root_dir'), $declination, $suffix);
    break;
  }

  $paths = sfConfig::get('project_internals_exec_path', sfConfig::get('software_internals_exec_path'));
  $cmd = sprintf('%s -e -m printer/pqueue -p %s %s 2> /dev/null | %s', $paths['cupsfilter'], $ppd, $filename, $paths['base64']);
  if ( sfConfig::get('sf_web_debug', false) )
    error_log("Executing: $cmd...");
  exec($cmd, $raw);
  echo implode('', $raw);
?>

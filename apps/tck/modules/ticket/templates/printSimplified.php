<?php
  if ( sfConfig::get('sf_web_debug', false) )
  {
    echo get_partial('global/get_tickets_pdf', array('tickets_html' => $content));
    return;
  }
  
  $pdf = new sfDomPDFPlugin();
  $pdf->setInput(get_partial('global/get_tickets_pdf', array('tickets_html' => $content)));

  $pdfcontent = $pdf->render();
?>
<?php if ( !sfConfig::get('app_tickets_direct_printing', false) ): ?>

  <?php echo $pdfcontent ?>

<?php else: ?>
  
<?php
  // records the PDF as a file, and remember the name of that file
  $filename = sfConfig::get('sf_app_cache_dir').'/ticket-'.date('YmdHis').'-'.rand(1000000, 9999999).'.pdf';
  file_put_contents($filename, $pdfcontent);
  touch($filename.'.prn'); // prepare the target file of cups to avoid permission issues
  
  // creates a new specific printer for THIS file only
  $printername = 'RawOutputStar-'.basename($filename);
  exec('/usr/sbin/lpadmin -p '.$printername.' -v file://'.$filename.'.prn -P '.sfConfig::get('sf_root_dir').'/data/cups/StarTSP700.ppd');
  exec('/usr/sbin/cupsaccept '.$printername);
  exec('/usr/sbin/cupsenable '.$printername);
  
  $file = array(
    'mtime' => filemtime($filename.'.prn'),
    'size'  => filesize($filename.'.prn'),
  );
  
  // prints out in a raw file the PDF previously generated
  exec('/usr/bin/lp -d '.$printername.' '.$filename);
  
  // waits for the time tickets are processed
  for ( $i = 0 ; $i < 150 ; $i++ ) // leaves after 15 sec max.
  {
    usleep(100000); // sleeps 0.10 sec before doing any check
    clearstatcache(true, $filename.'.prn'); // clear stat cache, to get the real filesize
    if ( $file['size'] > 0 && $file['size'] == filesize($filename.'.prn') )
      break;
    $file['size'] = filesize($filename.'.prn');
  }
  error_log('break after '.$i);
  
  // removes the specific printer
  exec('/usr/sbin/lpadmin -x '.$printername.') &');
  
  // prints out the raw data ready for printing on a thermic printer
  if ( $content = file_get_contents($tmp = $filename.'.prn') )
    echo $content;
  else
    ; // do something if nothing is printed out
  
?>

<?php endif ?>

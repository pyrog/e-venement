<?php $json = $sf_data->getRaw('lines') ?>
<?php
  /*
  $json['translations'] = array();
  foreach ( $json['nb'] as $key => $value )
    $json['translations'][$key] = __($key);
  */
?>

<?php echo json_encode($json) ?>

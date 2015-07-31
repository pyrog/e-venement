<div class="ui-widget-content ui-corner-all versions-diff">
<?php use_stylesheet('diff') ?>
<?php
  $first_fields = array('version' => NULL, 'last_accessor_id' => 'sfGuardUser', 'created_at' => NULL, 'updated_at' => NULL);
  $specific_fields = array('sf_guard_user_id' => 'sfGuardUser');
  $remove_fields = array('id', 'confirmed', 'automatic');
  
  foreach ( array('newest' => NULL, 'searched' => 'searched_version', 'previous' => 'previous_version') as $var => $record )
  {
    $tmpobj = $record ? $object->$record : $object;
    $$var = '';
    foreach ( $first_fields as $field => $fk )
    {
      $value = $fk && $tmpobj->$field ? Doctrine::getTable($fk)->find($tmpobj->$field) : $tmpobj->$field;
      $$var .= (isset($form[$field]) ? $form[$field]->renderLabelName() : $field).': '.$value."\n";
    }
    foreach ( $tmpobj->getData() as $field => $value )
    if ( isset($form[$field]) && !isset($first_fields[$field]) && !in_array($field, $remove_fields) )
    {
      if ( is_bool($value) )
        $value = $value ? 'true' : 'false';
      if ( isset($specific_fields[$field]) && $specific_fields[$field] && $value )
        $value = Doctrine::getTable($specific_fields[$field])->find($value);
      
      $$var .= $form[$field]->renderLabelName().': '.$value."\n";
    }
  }
  
  echo Diff::toTable(Diff::compare($previous, $searched));
  echo Diff::toTable(Diff::compare($searched, $newest));
?>
</div>

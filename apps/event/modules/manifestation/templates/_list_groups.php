<?php $cpt = 0 ?>
<?php foreach ( $manifestation->getBiggestTransactions() as $transaction ): ?>
  <?php $cpt++ ?>
  
  <span class="transaction">
    #<a href="<?php echo cross_app_url_for('tck', 'transaction/edit?id='.$transaction->id) ?>"><?php echo $transaction->id ?></a>
  </span>
  <a class="contact" href="<?php echo cross_app_url_for('rp', 'contact/edit?id='.$transaction->contact_id) ?>">
    <?php echo $transaction->Contact ?>
  </a>
  <?php if ( $transaction->professional_id ): ?>
  <a class="organism" href="<?php echo cross_app_url_for('rp', 'organism/edit?id='.$transaction->Professional->organism_id) ?>">
    <?php echo $transaction->Professional->Organism ?>
  </a>
  <?php endif ?>
  <?php
    $pro = array();
    if ( $transaction->Professional->name_type )
      $pro[] = '<span class="function">'.$transaction->Professional->name_type.'</span>';
    if ( $transaction->Professional->department )
      $pro[] = '<span class="department">'.$transaction->Professional->department.'</span>';
  ?>
  <?php if ( $pro ): ?>
  <span class="professional">
    - <?php echo implode(', ', $pro) ?>
  </span>
  <?php endif ?>
  
  <?php if ( $cpt >= 10 ) break; ?>
  <br/>
<?php endforeach ?>

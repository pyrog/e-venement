<?php $cpt = 0 ?>
<ul>
<?php foreach ( $manifestation->getBiggestTransactions() as $transaction ): ?>
  <li class="<?php echo $transaction->printed ? 'printed' : 'ordered' ?>">
  <?php $cpt++ ?>
  
  <span class="transaction">
    #<a href="<?php echo cross_app_url_for('tck', 'transaction/edit?id='.$transaction->id) ?>"><?php echo $transaction->id ?></a>
  </span>
  →
  <span class="nb-tickets"><?php echo __('%%nb%% tickets', array('%%nb%%' => $transaction->nb_tickets)) ?></span>
  
  <span class="before-contact"></span>
  <a class="contact" href="<?php echo cross_app_url_for('rp', 'contact/edit?id='.$transaction->contact_id) ?>">
    <?php echo $transaction->Contact ?>
  </a>

  <?php if ( $transaction->professional_id ): ?>
  <?php
    $pro = array();
    if ( $transaction->Professional->department )
      $pro[] = '<span class="department">'.$transaction->Professional->department.'</span>';
    if ( $transaction->Professional->name_type )
      $pro[] = '<span class="function">'.$transaction->Professional->name_type.'</span>';
  ?>
  <?php if ( $pro ): ?>
  -
  <span class="professional">
    <?php echo implode(', ', $pro) ?>
  </span>
  <?php endif ?>
  -
  <a class="organism" href="<?php echo cross_app_url_for('rp', 'organism/edit?id='.$transaction->Professional->organism_id) ?>">
    <?php echo $transaction->Professional->Organism ?>
  </a>
  <?php endif ?>
  
  </li>
  <?php if ( $cpt >= 10 ) break; ?>
<?php endforeach ?>

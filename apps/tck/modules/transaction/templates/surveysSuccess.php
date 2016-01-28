<?php include_partial('survey_assets') ?>
<h1><?php echo __('The survey') ?></h1>
<?php foreach ( $forms as $form ): ?>
  <?php include_partial('survey_form', array('form' => $form, 'transaction' => $transaction)) ?>
<?php endforeach ?>

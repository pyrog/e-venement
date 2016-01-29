<?php use_helper('I18N', 'Date') ?>
<?php include_partial('transaction/assets') ?>


<div id="sf_admin_container" class="sf_admin_edit ui-widget ui-widget-content ui-corner-all">
  <div class="fg-toolbar ui-widget-header ui-corner-all">
    <h1><?php echo __("Surveys for transaction #") . $transaction->id ?></h1>
  </div>

  <?php include_partial('transaction/flashes') ?>

  <div id="sf_admin_content">

    <?php foreach ($forms as $form): ?>
      <?php include_partial('survey_form', array('form' => $form, 'transaction' => $transaction)) ?>
    <?php endforeach ?>

    <input type="submit" value="Enregistrer" id="submit-all-forms">

  </div>
</div>

  <?php include_partial('transaction/themeswitcher') ?>


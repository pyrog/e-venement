<?php use_helper('I18N', 'Date') ?>
<?php include_partial('transaction/assets') ?>


<div id="sf_admin_container" class="sf_admin_edit ui-widget ui-widget-content ui-corner-all">
  <div class="fg-toolbar ui-widget-header ui-corner-all">
    <h1><?php echo __("Surveys for transaction #%%id%%", array('%%id%%' => $transaction->id)) ?></h1>
  </div>

  <?php include_partial('transaction/flashes') ?>

  <div class="sf_admin_actions_block ui-widget ui-helper-clearfix">
    <ul class="sf_admin_actions_form">
      <li class="sf_admin_action_save">
        <button class="submit-all-forms fg-button ui-state-default fg-button-icon-left" type="submit">
          <span class="ui-icon ui-icon-circle-check"></span><?php echo __("Save and close") ?>
        </button>
      </li>
    </ul>
  </div>

  <div id="sf_admin_form_tab_menu" class="ui-tabs ui-widget ui-widget-content ui-corner-all ui-helper-clearfix">
    <?php foreach ($forms as $form): ?>
      <?php include_partial('survey_form', array('form' => $form, 'transaction' => $transaction)) ?>
    <?php endforeach ?>
  </div>

  <div class="sf_admin_actions_block ui-widget ui-helper-clearfix">
    <ul class="sf_admin_actions_form">
      <li class="sf_admin_action_save">
        <button class="submit-all-forms fg-button ui-state-default fg-button-icon-left" type="submit">
          <span class="ui-icon ui-icon-circle-check"></span><?php echo __("Save and close") ?>
        </button>
      </li>
    </ul>
  </div>

</div>

  <?php include_partial('transaction/themeswitcher') ?>


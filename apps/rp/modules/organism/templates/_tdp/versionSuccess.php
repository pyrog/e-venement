<?php use_helper('I18N', 'Date') ?>
<?php include_partial('assets') ?>

<div id="sf_admin_container" class="sf_admin_show ui-widget ui-widget-content ui-corner-all">
  <div class="fg-toolbar ui-widget-header ui-corner-all">
    <h1><?php echo $object ?></h1>
  </div>
  
  <?php include_partial('flashes') ?>

  <div class="sf_admin_actions_block ui-widget">
    <?php include_partial('contact/version_actions', array('form' => $form, 'object' => $object, 'configuration' => $configuration, 'helper' => $helper,)) ?>
  </div>

  <div class="ui-helper-clearfix"></div>

  <?php include_partial('contact/version', array('form' => $form, 'object' => $object, 'configuration' => $configuration)) ?>

  <?php include_partial('themeswitcher') ?>
</div>

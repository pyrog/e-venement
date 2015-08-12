<?php use_helper('I18N', 'Date') ?>
<?php include_partial('assets') ?>

<div id="sf_admin_container" class="sf_admin_edit ui-widget ui-widget-content ui-corner-all">
  <div class="fg-toolbar ui-widget-header ui-corner-all">
    <h1><?php echo __('Group %%name%%', array('%%name%%' => $group->getName()), 'messages') ?></h1>
  </div>

  <?php include_partial('group/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('group/form_header', array('group' => $group, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <div class="sf_admin_actions_block ui-widget">
      <?php include_partial('group/add_member_cards_actions', array('group' => $group, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
    </div>
    <div class="clear"></div>
    <?php include_partial('add_member_cards', array('member_card_types' => $member_card_types, 'group' => $group, 'csrf_token' => $csrf_token,)) ?>
  </div>

  <div id="sf_admin_footer">
  </div>

  <?php include_partial('group/themeswitcher') ?>
</div>


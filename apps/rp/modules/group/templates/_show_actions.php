<ul class="sf_admin_actions_form">
  <?php echo $helper->linkToList(array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'list',  'label' => 'Back to list',  'ui-icon' => '',)) ?><li class="li-submenu">
    <a class="fg-button ui-state-default fg-button-icon-left" href="#" onclick="javascript: return false;"><span class="ui-icon-lightbulb ui-icon"></span><?php echo __('Actions') ?></a>
    <ul><li>
      <a class="fg-button ui-state-default fg-button-icon-left" href="<?php echo url_for('group/emailing?id='.$group->id) ?>"><span class="ui-icon-mail-closed ui-icon"></span><?php echo __('Emailing') ?></a>
    </li><li>
      <a class="fg-button ui-state-default fg-button-icon-left" href="<?php echo url_for('group/addMemberCards?id='.$group->id) ?>"><span class="ui-icon-heart ui-icon"></span><?php echo __('Member cards') ?></a>
    </li></ul>
  </li><?php echo $helper->linkToExtraAction($group, array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'action' => 'csv', 'extra-icon' => 'filter', 'label' => "Contacts' List",)) ?>
  <?php echo $helper->linkToEdit($group, array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'edit',  'label' => 'Edit',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToDelete($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',  'ui-icon' => '',)) ?>
</ul>

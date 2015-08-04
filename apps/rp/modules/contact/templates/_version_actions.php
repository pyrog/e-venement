<ul class="sf_admin_actions_form">
  <?php echo $helper->linkToList(array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'list',  'label' => __('Contact'),  'ui-icon' => '',), $object) ?>
    
    <li class="sf_admin_action_version_by fg-button ui-state-default fg-button-icon-left">
      <span class="ui-icon ui-icon-person"></span>
      <?php echo $object->searched_version->last_accessor_id
        ? ( ($u = Doctrine::getTable('sfGuardUser')->findOneById($object->searched_version->last_accessor_id)) ? $u : $object->version->last_accessor_id )
        : 'n/a' ?>
    </li>
    <li class="sf_admin_action_updated_at fg-button ui-state-default fg-button-icon-left">
      <span class="ui-icon ui-icon-calendar"></span>
      <?php echo format_datetime($object->searched_version->updated_at) ?>
    </li>
    
    <li class="sf_admin_action_version fg-button ui-state-default fg-button-icon-left">
    <form action="<?php echo url_for('contact/version?id='.$object->id) ?>" method="get">
      <p>
        <span class="ui-icon ui-icon-flag"></span>
        <label><?php echo __('Version') ?></label>
        <input maxlength="4" size="3" type="number" onchange="javascript: submit();" name="v" value="<?php echo $object->searched_version ? $object->searched_version->version : $object->searched_version ?>" />
        <span class="max">/&nbsp;<?php echo $object->version ?></span>
      </p>
    </form>
    </li>
  
    <li class="sf_admin_action_versions">
    <?php echo link_to(
      '<span class="ui-icon ui-icon-document"></span>'.__('Current', array(), 'sf_admin'),
      'contact/show?id='.$object->id,
      array('class' => 'fg-button ui-state-default fg-button-icon-left')
    ) ?>
    </li>
    
    <script type="text/javascript">
      $(document).ready(function(){
        // remove information linked to foreign data
        $('#sf_fieldset_spectators, #sf_fieldset_unbalanced, #sf_fieldset_tickets, #sf_fieldset_workspaces, #more').remove()
        $('[href="#sf_fieldset_spectators"], [href="#sf_fieldset_unbalanced"], [href="#sf_fieldset_tickets"], [href="#sf_fieldset_workspaces"]').parent().remove()
        $('.sf_admin_form_row.sf_admin_form_field_gauge_txt, .sf_admin_form_row.sf_admin_form_field_organizers_list, .sf_admin_form_row.sf_admin_form_field_prices_list, .sf_admin_form_row.sf_admin_form_field_booking_list').remove();
      });
    </script>

    <?php
      $groups = $sort = array();
      
      $objects = array($object);
      $config = $sf_data->getRaw('config');
      foreach ( $config['subobjects'] as $subobjects => $conf )
      foreach ( $object->$subobjects as $subobject )
        $objects[] = $subobject;
    ?>
    <ul class="tdp-object-groups">
      <?php $cpt = 0 ?>
      <?php foreach ( $objects as $obj ): ?>
      <?php $cpt++ ?>
      <li class="groups-<?php echo $cpt == 1 ? 'object' : 'subobject-'.$obj->id ?>">
        <?php if ( count($objects) > 1 ): ?>
          <h3><?php echo $obj ?></h3>
        <?php endif ?>
        <form action="#" method="get"><ul>
          <?php
            // ordering groups
            $groups = array();
            foreach ( $obj->getGroups() as $group )
              $groups[$group.' || '.$group->id] = $group;
            ksort($groups);
          ?>
          <?php foreach ( $groups as $group ): ?>
          <?php $users = array(); foreach ( $group->Users as $user ) $users[] = $user->id; ?>
          <?php if ( is_null($group->sf_guard_user_id) && (in_array($sf_user->getId(), $users) || $sf_user->hasCredential(array('super-admin','admin'),false)) || $group->sf_guard_user_id === $sf_user->getId() ): ?>
          <li>
            <?php if ( $group->sf_guard_user_id === $sf_user->getId() || (is_null($group->sf_guard_user_id) && $sf_user->hasCredential('pr-group-common')) ): ?>
            <a href="#" class="remove" onclick="javascript: LI.contact_tdp_group_removing_obj<?php echo $cpt.'_'.$obj->id ?>(this);">x</a>
            <input type="hidden" name="group_id" value="<?php echo $group->id ?>" />
            <?php else: ?>
            <span>&nbsp;&nbsp;</span>
            <?php endif ?>
            <?php echo link_to($group,'group/show?id='.$group->id) ?>
          </li>
          <?php endif ?>
          <?php endforeach ?>
          <li class="empty" <?php if ( $obj->Groups->count() > 0 ): ?>style="display: none"<?php endif ?>><?php echo __('No result',null,'sf_admin') ?></li>
          <?php if ( $sf_user->hasCredential('pr-group-common') || $sf_user->hasCredential('pr-group-perso') ): ?>
          <li class="new">
            <input type="hidden" name="object-id" value="<?php echo $obj->id ?>" />
            <select name="unassociated_professional[groups_list][]"></select>
            <script type="text/javascript"><!--
            if ( LI == undefined )
              var LI = {};
            
            $(document).ready(function(){
              var object = <?php echo $cpt == 1 ? "$('.sf_admin_edit.tdp-object')" : "$(tmp = '#tdp-content [name=\"professional".($obj->isNew() ? '' : '_'.$obj->id)."[id]\"][value=".$obj->id."]').closest('.tdp-subobject')" ?>;
              var groups = <?php echo $cpt == 1 ? "$('.groups-object')" : "$('.groups-subobject-".$obj->id."')" ?>;
              var input = object.find(tmp = '.tdp-groups_list .open_list .open_list_source');
              groups.find('select').replaceWith(input);
              groups.find('.ac_input').prop('placeholder', '<?php echo __('Add to a group') ?>');
              groups.find('.ac_input').prop('title', '<?php echo __('Press <SPACE> to see all groups') ?>');
              
              if ( location.hash == '#debug' )
                console.error('Groups autocompletion of '+$.trim(object.find('h1 a').text())+' - input:'+input.length+' list:'+groups.length);
              
              // pre-adding a group
              input.keydown(function(event){
                if ( event.key != 'Enter' )
                  return true;
                if ( !$.trim($(this).val()) )
                  return true;
                if ( input.closest('ul').find('[name=group_id][value="'+$(this).attr('optval')+'"]').length > 0 )
                  return true;
                
                var li = $('<li></li>');
                $('<a></a>').prop('href', '#').text('x')
                  .click(LI.contact_tdp_group_removing_obj<?php echo $cpt.'_'.$obj->id ?>)
                  .appendTo(li);
                li.append(' ');
                $('<input />').prop('type','hidden')
                  .val($(this).attr('optval'))
                  .prop('name', 'group_id')
                  .appendTo(li);
                li.append(' ');
                $('<span></span>').text($(this).val())
                  .appendTo(li);
                li.insertBefore(input.closest('li'));
                
                // force adding the new group in the "to add" groups (select)
                $('#'+$(this).prop('id').replace(/^source_/g, '')).closest('.open_list')
                  .find('.open_list_add').click();
              });
            });
            
            // removing a group
            LI.contact_tdp_group_removing_obj<?php echo $cpt.'_'.$obj->id ?> = function(anchor)
            {
              var object = <?php echo $cpt == 1 ? "$('.sf_admin_edit.tdp-object')" : "$('#tdp-content [name=\"professional".($obj->isNew() ? '' : '_'.$obj->id)."[id]\"][value=".$obj->id."]').closest('.tdp-subobject')" ?>;
              var groups = <?php echo $cpt == 1 ? "$('.groups-object')" : "$('.groups-subobject-".$obj->id."')" ?>;
              
              object.find('.tdp-groups_list .open_list .open_list_selected').change()
                .find('option[value="'+$(anchor).closest('li').find('[name=group_id]').val()+'"]')
                .remove();
              $(anchor).closest('li').fadeOut('medium',function(){
                if ( $(this).closest('ul').find('li:not(.empty):not(.new)').length <= 1 )
                  $(this).closest('ul').find('.empty').fadeIn('slow');
                $(this).remove();
              });
              
              return false;
            }
            --></script>
          </li>
          <?php endif ?>
        </ul></form>
      </li>
      <?php endforeach ?>
    </ul>

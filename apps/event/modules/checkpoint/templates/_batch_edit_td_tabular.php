<td class="sf_admin_text sf_admin_list_td_name">
  <a href="<?php echo url_for('checkpoint/show?id='.$checkpoint->id) ?>"><?php echo $checkpoint->name ?>
</td>
<td class="sf_admin_text sf_admin_list_td_Organism">
  <a href="<?php echo cross_app_url_for('rp','organism/show?id='.$checkpoint->organism_id) ?>"><?php echo $checkpoint->Organism ?></a>
</td>
<td class="sf_admin_text sf_admin_list_td_type">
  <?php
    switch ( $checkpoint->type ) {
    case 'exit':
      echo '<span class="ui-icon ui-icon-radio-off" title="'.__('exit').'"></span>';
      break;
    case 'info':
      echo '<span class="ui-icon ui-icon-bullet" title="'.__('info').'"></span>';
      break;
    default:
      echo '<span class="ui-icon ui-icon-check" title="'.__('entrance').'"></span>';
      break;
    }
  ?>
</td>

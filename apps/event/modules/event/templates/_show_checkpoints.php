<ol class="show_checkpoint">
  <?php foreach ( $event->Checkpoints as $checkpoint ): ?>
  <li><?php echo $checkpoint ?> <?php echo $checkpoint->type ? '('.__($checkpoint->type).')' : '' ?></li>
  <?php endforeach ?>
</ol>

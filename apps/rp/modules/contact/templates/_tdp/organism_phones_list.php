<?php if ( $object->Organism ): ?>
<?php if ( $object->Organism->Phonenumbers->count() > 0 ): ?>
<ul class="tdp-<?php echo str_replace('.php','',basename(__FILE__)) ?>">
<?php foreach ( $object->Organism->Phonenumbers as $pn ): ?>
  <li><label><?php echo $pn->name ?></label> <span><?php echo $pn->number ?></span></li>
<?php endforeach ?>
</ul>
<?php endif ?>
<?php endif ?>

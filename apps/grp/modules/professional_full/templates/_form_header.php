<?php if ( is_object($professional) && !$professional->isNew() ): ?>
<?php include_partial('professional/show_contact', array('professional' => $professional)) ?>
<?php endif ?>

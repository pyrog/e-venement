<div>
  <p><label><?php echo __('Transaction') ?></label><span class="form_field_id">#<?php echo $transaction->id ?></span></p>
  <p>
    <label><?php echo __('Creation') ?></label>
    <span class="form_field_created_at"><?php echo format_datetime($transaction->created_at,'r') ?></span>
  </p>
  <p>
    <label><?php echo __('Last mod.') ?></label>
    <span class="form_field_updated_at"><?php echo format_datetime($transaction->updated_at,'r') ?></span>
  </p>
  <p>
    <label><?php echo __('By') ?></label>
    <span class="form_field_User"><?php echo $transaction->User ?></span>
  </p>
</div>
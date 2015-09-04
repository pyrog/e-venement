<div id="actions">
<?php if ( $transaction->id == $sf_user->getTransactionId() ): ?>
<?php if ( ($txt = pubConfiguration::getText('app_member_cards_complete_your_passes', false)) && $sf_user->getTransaction()->MemberCards->count() ): ?>
<?php if ( $txt === true ) $txt = __('Complete your passes'); ?>
<div class="actions mc_pending">
<?php echo link_to($txt,'manifestation/index?mc_pending=') ?>
</div>
<?php endif ?>
<div class="actions index">
<?php echo link_to(__('Continue shopping'),'@homepage') ?>
</div>
<div class="actions register">
<?php echo link_to(__('Checkout'),'cart/register') ?>
</div>
<div class="actions empty">
<?php echo link_to(__('Empty your cart'),'cart/empty') ?>
</div>
<?php else: ?>
<div class="actions register">
<?php echo link_to(__('Payment'),'cart/register?transaction_id='.$transaction->id) ?>
</div>
<?php endif ?>
</div>

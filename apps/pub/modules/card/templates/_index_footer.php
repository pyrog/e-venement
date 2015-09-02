<?php if ( sfConfig::get('app_member_cards_complete_your_passes', false)
        && $sf_user->getTransaction()->MemberCards->count() > 0
        && !$sf_request->hasParameter('mc_pending') ): ?>
  <form method="get" action="<?php echo url_for('manifestation/index') ?>" class="mc_pending">
    <button name="complete_mc"><?php echo pubConfiguration::getText('app_member_cards_complete_your_passes', __('Complete your passes')) ?></button>
  </form>
<?php endif ?>
<div class="text_config card_bottom">
  <?php echo nl2br(pubConfiguration::getText('app_texts_card_bottom')) ?>
</div>

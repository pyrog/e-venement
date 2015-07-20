<?php $ofc = isset($ofc) ? $ofc : true ?>
<?php $dl = !isset($dl) ? $sf_user->hasCredential('stats-csv') : $dl == 'from-csv' ? true : $dl ?>
<span class="arrow"></span>
<a
  href="#<?php echo isset($anchor) ? $anchor : '' ?>"
  title="<?php echo __('Chart') ?>"
  class="chart ui-corner-all"
  <?php if ( $ofc ): ?>onclick="javascript: LI.OFC.init($(this).closest('.ui-widget-content').find('embed')).popup(); return false;"<?php endif ?>
>
  <span><?php echo __('Chart') ?></span>
</a>
<?php if ( $dl ): ?>
<a
  <?php if ( $dl === true ): ?>
  href="#csv"
  <?php else: ?>
  target="_blank"
  href="<?php echo $dl ?>"
  <?php /*href="<?php echo url_for((isset($module) ? $module : $sf_context->getModuleName()).'/csv'.(isset($id) ? '?'.(isset($get_param) ? $get_param : 'id').'='.$id : '')) ?>"*/ ?>
  <?php endif ?>
  title="<?php echo __('Record') ?>"
  class="record ui-corner-all"
><span><?php echo __('Record') ?></span></a>
<?php endif ?>

<section class="srv-direct-survey-container ui-corner-all ui-tabs-panel ui-widget-content">
  <h1><?php echo $form->getObject()->getName() ?></h1>
  <p><?php echo $form->getObject()->description ?></p>

  <?php echo $form->renderFormTag(url_for('transaction/commitDirectSurvey'), array('class' => 'srv-direct-survey')) ?>
    <?php echo $form ?>
    <input type="hidden" name="transaction_id" value="<?php echo $transaction->id ?>" >
  </form>
</section>

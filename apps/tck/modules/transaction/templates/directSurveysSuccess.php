<div class="fg-toolbar ui-widget-header ui-corner-all">
    <h1>
        <?php echo __("Options pour les participants à l'opération #") . $transaction->id ?>
    </h1>
</div>

<?php include_partial('global/flashes') ?>


<?php foreach ($forms as $form): ?>

  <h1><?php echo $form->getObject()->getName() ?></h1>
  <p><?php echo $form->getObject()->description ?></p>

<?php echo $form->renderFormTag(url_for('transaction/commitDirectSurvey'), array('class' => 'srv-direct-survey')) ?>
    <?php echo $form ?>
<?php /*
    <ul>
    <?php foreach ( $form as $field ): ?>
    <?php if ( !$field->isHidden() ): ?>
      <li>
        <?php echo $field->renderRow() ?>
      </li>
    <?php endif ?>
    <?php endforeach; ?>
    </ul>
    <?php echo $form->renderHiddenFields() ?>
 *
 */ ?>

    <input type="hidden" name="transaction_id" value="<?php echo $transaction->id ?>" >
    <input type="submit" value="Enregistrer">
  </form>
<?php endforeach ?>


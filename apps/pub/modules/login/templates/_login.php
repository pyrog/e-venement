  <?php echo $form->renderHiddenFields() ?>
  <p class="<?php echo $fieldname = 'email' ?>">
    <?php echo $form[$fieldname]->renderLabel() ?>
    <?php echo $form[$fieldname] ?>
    <span class="error"><?php if ( isset($errors[$fieldname]) ) echo __($errors[$fieldname]) ?></span>
  </p>
  <p class="<?php echo $fieldname = 'password' ?>">
    <?php echo $form[$fieldname]->renderLabel() ?>
    <?php echo $form[$fieldname] ?>
    <span class="error"><?php if ( isset($errors[$fieldname]) ) echo __($errors[$fieldname]) ?></span>
    <a href="<?php echo url_for('login/forgot') ?>" class="forgot"><?php echo __('Send me a new password') ?></a></br>
    <a href="<?php echo url_for('contact/new') ?>" class="create"><?php echo __('Create an account') ?></a>
  </p>
  <?php if (!( isset($buttons) && !$buttons )): ?>
  <p class="submit">
    <label></label>
    <button name="<?php echo sprintf($form->getWidgetSchema()->getNameFormat(), 'url_back') ?>" value="<?php echo url_for('contact/index') ?>">
      <?php echo __('My orders') ?>
    </button>
    <input type="submit" value="<?php echo __('Continue shopping') ?>" name="continue" />
  </p>
  <?php endif ?>

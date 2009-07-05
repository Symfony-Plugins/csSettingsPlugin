
<?php if (csSettings::isAuthenticated($sf_user)): ?>
  <?php echo $helper->linkToNew(array(  'params' =>   array(  ),  'class_suffix' => 'new',  'label' => 'New',)) ?>
<?php endif ?>
  <?php echo submit_tag('Save Settings'); ?>

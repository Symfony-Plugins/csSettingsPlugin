<td>
  <ul class="sf_admin_td_actions">
    <?php if (csSettings::isAuthenticated($sf_user)): ?>
      <?php echo $helper->linkToEdit($cs_setting, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Edit',)) ?>
      <?php echo $helper->linkToDelete($cs_setting, array(  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',)) ?>
    <?php endif ?>    
  </ul>
</td>

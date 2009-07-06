<td class="sf_admin_text sf_admin_list_td_name">
  <?php echo link_to($cs_setting->getName(), 'cs_setting_edit', $cs_setting) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_value">
  <?php echo $form[$cs_setting['slug']] ?>
</td>
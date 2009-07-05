<td class="sf_admin_text sf_admin_list_td_name">
  <?php echo link_to($cs_setting->getName(), 'cs_setting_edit', $cs_setting) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_value_element">
  <?php echo get_partial('value_element', array('type' => 'list', 'cs_setting' => $cs_setting)) ?>
</td>

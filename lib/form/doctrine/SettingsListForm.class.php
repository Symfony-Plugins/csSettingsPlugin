<?php

class SettingsListForm extends sfForm
{
  public function configure()
  {
    $method = sfConfig::get('app_csSettingsPlugin_tableMethod');
    
    foreach (Doctrine::getTable('csSetting')->$method() as $setting) 
    {
      $form = new csSettingForm($setting);
      $this->widgetSchema[$setting['slug']] = $form->getSettingWidget();
      $this->validatorSchema[$setting['slug']] = $form->getSettingValidator();      
    }
  }
}
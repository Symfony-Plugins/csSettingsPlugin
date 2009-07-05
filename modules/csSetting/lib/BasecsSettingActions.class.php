<?php

/**
 * BasecsSettingsActions 
 * 
 * @uses autocsSettingsActions
 * @package 
 * @version $id$
 * @copyright 2006-2007 Chris Wage
 * @author Chris M. Wage <cwage@centresource.com>
 * @license See LICENSE that came packaged with this software
 */
class BasecsSettingActions extends AutocsSettingActions
{
  public function executeValue_element()
  {
    $setting_id = $this->getRequestParameter('setting_id');

    $this->cs_setting = Doctrine::getTable('csSetting')->find($setting_id);
    $this->cs_setting = $this->cs_setting ? $this->cs_setting:new csSetting();

    if( $this->getRequest()->hasParameter('type') )
    {
      $this->cs_setting->setType($this->getRequestParameter('type'));
    }

    if( $this->getRequest()->hasParameter('options') )
    {
      $this->cs_setting->setOptions($this->getRequestParameter('options'));
    }
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new SettingsListForm();
    return parent::executeIndex($request);
  }
  
  public function executeListSaveSettings(sfWebRequest $request)
  {
    if($settings = $this->getRequestParameter('cs_setting'))
    {
      foreach($settings AS $setting_id => $value)
      {
        $setting = Doctrine::getTable('csSetting')->find($setting_id);
        $setting->setValue($value);
        $setting->save();
      }
    }
    
    if($files = $request->getFiles('cs_setting'))
    {
      $this->processUpload($files);
    }
    $this->getUser()->setFlash('notice', 'Your settings have been saved.');
    $this->redirect('@cs_setting');
  }
  public function processUpload($files)
  {
    $default_path = csSettings::getDefaultUploadPath();
    
    foreach ($files as $setting_id => $file) 
    {
      if ($file['name']) 
      {
        $setting = Doctrine::getTable('csSetting')->find($setting_id);
        
        $target_path = $setting->getOption('upload_path');
        
        $target_path = $target_path ? $target_path : $default_path;
        
        //If target path does not exist, attempt to create it
        if(!file_exists($target_path))
        {
          $target_path = mkdir($target_path) ? $target_path : 'uploads';
        }
        
        $target_path = $target_path . DIRECTORY_SEPARATOR . basename( $file['name']); 
        
        if(!move_uploaded_file($file['tmp_name'], $target_path)) 
        {
          $this->getUser()->setFlash('error', 'There was a problem uploading your file!');
        }
        else
        {  
          $setting->setValue(basename($file['name']));
          $setting->save();
        }
      }
    }
  }
}

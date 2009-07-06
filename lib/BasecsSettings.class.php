<?php

/**
 * BasecsSettings 
 * 
 * @package 
 * @version $id$
 * @copyright 2006-2007 Brent Shaffer
 * @author Brent Shaffer <bshaffer@centresource.com> 
 * @license See LICENSE that came packaged with this software
 */
class BasecsSettings
{
  static function getDefaultUploadPath()
  {
    return 'uploads/setting';
  }
  
  static function isAuthenticated($user = null)
  {
    if (!$user) 
    {
      $user = sfContext::getInstance()->getUser();
    }
    
    $authMethod = sfConfig::get('app_csSettingsPlugin_authMethod');
    
    return $user->$authMethod();
  }


  /**
   * getSetting
  * pulls the csSetting object for a given setting
   * 
   * @param string $setting 
   * @static
   * @access public
   * @return object csSetting
   */
  static function getSetting($setting)
  {
    $query = Doctrine::getTable('csSetting')
                  ->createQuery('s')
                  ->addWhere("s.name = ?", $setting);
                  
    return $query->fetchOne();
  }

  /**
   * get 
  * Returns the string value of a particular setting.
   * 
   * @param string $setting 
   * @static
   * @access public
   * @return string
   */
  static function get($setting)
  {
    // Pull from cached settings array
    $settingsArray = self::getAll();
    if (isset($settingsArray[$setting])) 
    {
      return $settingsArray[$setting];
    }
    
    // If the key does not exist, it may be pulling by name instead of slug.  
    // Query the database
    $query = Doctrine::getTable('csSetting')
                  ->createQuery('s')
                  ->addWhere("s.name = ?", $setting);

    return $query->fetchOne();
  }

  /**
   * getAllSettings 
   * Returns an array of all setting objects 
   * @static
   * @access public
   * @return array
   */
  static function getAllSettings()
  {
    $result = Doctrine::getTable('csSetting')->findAll();
    $objArray = array();
    foreach ($result as $setting)
    {
      $objArray[$setting->getName()] = $setting;
    }
    return $objArray;
  }

  /**
   * getAll 
   * Returns an array of settings 
   * (key: setting slug, value: setting value)
   * 
   * @static
   * @access public
   * @return void
   */
  static function getAll()
  {
    $cachePath = sfConfig::get('sf_cache_dir').'/cs_settings.cache';
    if (!file_exists($cachePath))
    {
      $settingsArray = array();
      foreach (Doctrine::getTable('csSetting')->findAll() as $setting) 
      {
        $settingsArray[$setting['slug']] = $setting->getValue();
      }
      
      // Cache Settings
      $serialized = serialize($settingsArray);
      file_put_contents($cachePath, $serialized);
    } 
    else
    {
      // Pull settings array
      $settingsArray = unserialize(file_get_contents($settings));
    }
    return $settingsArray;
  }
}

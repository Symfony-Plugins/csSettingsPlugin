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
    $settingsArray = self::getSettingsArray();
    if (isset($settingsArray[$setting])) 
    {
      return $settingsArray[$setting];
    }
    
    //Look in app.ymls for setting
    return sfConfig::get('app_'.self::settingize($setting));
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
    $objArray = self::getAllSettings();
    if (isset($objArray[$setting])) 
    {
      $ret = new csSetting();
      $ret->fromArray($objArray[$setting]);
      return $ret;
    }
                  
    return null;
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
    $cachePath = sfConfig::get('sf_cache_dir').'/'.self::getCache('object_array');
    if (!file_exists($cachePath))
    {
      $objArray = array();
      foreach (Doctrine::getTable('csSetting')->findAll() as $setting)
      {
        $objArray[$setting['slug']] = $setting->toArray();
        $objArray[$setting['name']] = $setting->toArray();
      }
      
      // Cache Settings
      $serialized = serialize($objArray);
      file_put_contents($cachePath, $serialized);
    } 
    else
    {
      // Pull settings array
      $objArray = unserialize(file_get_contents($cachePath));
    }
    return $objArray;
  }

  /**
   * getAll 
   * Returns an array of settings 
   * (key: setting slug or name, value: setting value)
   * 
   * @static
   * @access public
   * @return void
   */
  static function getSettingsArray()
  {
    $cachePath = sfConfig::get('sf_cache_dir').'/'.self::getCache('settings_array');
    if (!file_exists($cachePath))
    {
      $settingsArray = array();
      foreach (Doctrine::getTable('csSetting')->findAll() as $setting) 
      {
        $settingsArray[$setting['slug']] = $setting->getValue();
        $settingsArray[$setting['name']] = $setting->getValue();
      }
      
      // Cache Settings
      $serialized = serialize($settingsArray);
      file_put_contents($cachePath, $serialized);
    } 
    else
    {
      // Pull settings array
      $settingsArray = unserialize(file_get_contents($cachePath));
    }
    return $settingsArray;
  }
  
  static public function clearSettingsCache()
  {
    foreach (self::getCache() as $cachedir) 
    {
      $cachePath = sfConfig::get('sf_cache_dir').'/'.$cachedir;
      if (file_exists($cachePath)) 
      {
        unlink($cachePath);
      }
    }
  }
  
  static public function getCache($key = '')
  {
    $cache = sfConfig::get('app_csSettingsPlugin_cachepaths');
    return $key ? $cache[$key] : $cache;
  }
  
  static public function settingize($anystring)
  {
    return str_replace('-', '_', Doctrine_Inflector::urlize(trim($anystring)));
  }
}

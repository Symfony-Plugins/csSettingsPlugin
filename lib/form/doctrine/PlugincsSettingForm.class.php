<?php

/**
 * PlugincsSetting form.
 *
 * @package    form
 * @subpackage csSetting
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PlugincsSettingForm extends BasecsSettingForm
{
  public function configure()
  { 
    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
                                       'choices' => sfConfig::get('app_csSettingsPlugin_types')
                                       ));
                                       
    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'csSetting', 'column' => array('name')), array('invalid' => 'Cannot use this name, a setting with this name already exists!'))
    );
  }

  public function getSettingWidget()
  {
    $type = $this->getObject()->getType();
    $method = 'get'.sfInflector::camelize($type).'SettingWidget';
    if (method_exists($this, $method))
    {
      return $this->$method();
    }
    return new sfWidgetFormInput();
  }

  public function getSettingValidator()
  {
    $type = $this->getObject()->getType();
    $method = 'get'.sfInflector::camelize($type).'SettingValidator';
    if (method_exists($this, $method))
    {
      return $this->$method();
    }
    return new sfValidatorString();
  }
}
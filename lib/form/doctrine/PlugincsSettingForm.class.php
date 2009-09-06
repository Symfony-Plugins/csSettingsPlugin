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
    $this->widgetSchema['type'] = new sfWidgetFormSelectRadio(array(
                                       'choices' => sfConfig::get('app_csSettingsPlugin_types'),
                                       ));
    
    $choices = Doctrine::getTable('csSetting')->getExistingGroupsArray();
    
    if ($choices) 
    {
      $choices = array_merge(array('' => ''), $choices);
      $this->widgetSchema['setting_group'] = new sfWidgetFormSelect(array(
                                         'choices' => $choices,
                                         ));
    }
    
    $this->widgetSchema->setLabel('setting_group', 'Group');
            
    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'csSetting', 'column' => array('name')), array('invalid' => 'Cannot use this name, a setting with this name already exists!'))
    );
    
    $help = array(
        'Text Field' => 'HTML Attributes', 
        'Text Area' => 'HTML Attributes', 
        'Checkbox' => 'HTML Attributes', 
        'Checkbox' => 'Choices (key=value)', 
        'Yes/No Radios' => 'HTML Attributes',
        'Database Model' => '<a href="http://www.symfony-project.org/api/1_2/sfWidgetFormDoctrineChoice" target="_blank">Widget Options</a> (*model=MyModel method=__toString add_empty=true)',
        'Upload' => '<a href="http://www.symfony-project.org/api/1_2/sfWidgetFormInputFileEditable" target="_blank">Widget Options</a>',
        );
        
    $helpStr = '<b>The following options are supported for each setting type</b>:<ul>';
    
    foreach ($help as $key => $value) 
    {
      $helpStr .= "<li>$key: $value</li>";
    }
    $helpStr .= '</ul>* required';
    
    $this->widgetSchema->setLabel('slug', 'Handle');
    $this->widgetSchema->setHelp('slug', 'This is used in your code to pull the value for this setting.  Use csSettings::get($handle);');
    $this->widgetSchema->setHelp('options', $helpStr);    
    $this->widgetSchema->setHelp('setting_group', 'Organize your settings into groups');
  }

  public function getSettingWidget()
  {
    $type = $this->getObject()->getType();
    $method = 'get'.sfInflector::camelize($type).'SettingWidget';
    if (method_exists($this, $method))
    {
      return $this->$method();
    }
    return new sfWidgetFormInput(array(), $this->getObject()->getOptionsArray());
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
  
  //Type Textarea
  public function getTextareaSettingWidget()
  {
    return new sfWidgetFormTextarea(array(), $this->getObject()->getOptionsArray());
  }
  
  // Type Checkbox
  public function getCheckboxSettingWidget()
  {
    return new sfWidgetFormInputCheckbox(array(), $this->getObject()->getOptionsArray());
  }
  
  // Type Yesno
  public function getYesnoSettingWidget()
  {
    return new sfWidgetFormSelectRadio(array('choices' => array('yes' => 'Yes', 'no' => 'No')), $this->getObject()->getOptionsArray());
  }
  public function getYesnoSettingValidator()
  {
    return new sfValidatorChoice(array('choices' => array('yes', 'no')));
  }
  
  //Type Select List
  public function getSelectSettingWidget()
  {
    return new sfWidgetFormSelect(array('choices' => $this->getObject()->getOptionsArray()));
  }
  public function getSelectSettingValidator()
  {
    return new sfValidatorChoice(array('choices' => $this->getObject()->getOptionsArray()));
  }
  
  //Type Model
  public function getModelSettingWidget()
  {
    return new sfWidgetFormDoctrineChoice($this->getObject()->getOptionsArray());
  }
  public function getModelSettingValidator()
  {
    return new sfValidatorDoctrineChoice($this->getObject()->getOptionsArray());
  }
  
  //Type Upload
  public function getUploadSettingWidget()
  {
    $path = $this->getObject()->getUploadPath() . '/' . $this->getObject()->getValue();
    $options = array(
          'file_src' => $this->getObject()->getValue(),
          'template' => "<a href='/$path'>%file%</a><br />%input%<br />%delete% %delete_label%",
      );
    
    // If you want to pass the widget custom settings, you can override in your setting's options  
    $options = array_merge($options, $this->getObject()->getOptionsArray());
    
    return new sfWidgetFormInputFileEditable($options);
  }
  
  // Overriding Bind in this case allows us to have the form field "setting_group_new" for usability
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $taintedValues['setting_group'] = (isset($taintedValues['setting_group_new']) && $taintedValues['setting_group_new']) ?  $taintedValues['setting_group_new'] : $taintedValues['setting_group'];
    unset($taintedValues['setting_group_new']);
    $ret = parent::bind($taintedValues, $taintedFiles);
    return $ret;
  }
}
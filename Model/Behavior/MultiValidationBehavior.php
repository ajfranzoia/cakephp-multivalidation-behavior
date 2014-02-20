<?php
/**
 * Copyright 2013 - 2014, QTS development (http://qtsdev.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2013 - 2014, QTS development (http://qtsdev.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * MultiValidation Behavior.
 * A CakePHP 2.x behavior that allows easy modifying of validation rules on the fly.
 *
 * @package       multivalidation
 * @subpackage    multivalidation.model.behavior
 */
class MultiValidationBehavior extends ModelBehavior {

/**
 * Defaults config.
 * 'types' key will hold the defined validation types.
 * Default validation will be identified by the '_default' key.
 * 
 * @var array
 */
    protected $_defaults = array(
        'types' => array(),
    );

/**
 * Used to preserve validation states between switchs.
 * 
 * @var array
 */
    protected $_runtime = array();

/**
 * Initiate Multivalidation behavior.
 * Stores defaultValidation and initializes loadedTypes
 *
 * @param Model $Model instance of model
 * @param array $config array of configuration settings.
 * @return void
 */
    public function setup(Model $Model, $config = array()) {
        $settings = array_merge($this->_defaults, $config);
        $this->settings[$Model->alias] = $settings;
        $this->_runtime[$Model->alias] = array(
            'defaultValidation' => $Model->validate,
            'loadedTypes' => array(
                '_default'
            ),
        );
    }
    
/**
 * Returns current loaded validation types.
 * 
 * @param Model $Model
 * @return array Array of the loaded types names
 */
    public function loadedValidation(Model $Model) {
        return $this->_runtime[$Model->alias]['loadedTypes'];
    }
    
/**
 * Adds a custom validation type to the current model validation.
 * 
 * @param Model $Model
 * @param string $type Validation type defined in types configuration
 * @return void
 */
    public function addValidation(Model $Model, $type) {
        $settings = $this->settings[$Model->alias];
        
        if (in_array($type, $this->_runtime[$Model->alias]['loadedTypes'])) {
            return;
        }
        
        $validationType = $settings['types'][$type];
        $Model->validate = Hash::merge($Model->validate, $validationType);
        $this->_runtime[$Model->alias]['loadedTypes'][] = $type;
    }
    
/**
 * Overwrites current model validation by chosen type.
 * 
 * @param string $type Tipo de validacion definida en _setupValidation()
 * @return void
 */
    public function loadValidation(Model $Model, $type) {
        $settings = $this->settings[$Model->alias];
        
        if (in_array($type, $this->_runtime[$Model->alias]['loadedTypes'])) {
            return;
        }
        
        $typeValidation = $settings['types'][$type];
        $Model->validate = $typeValidation;
        $this->_runtime[$Model->alias]['loadedTypes'] = (array) $type;
    }
    
/**
 * Reset model validation to default state.
 * 
 * @param string $type Tipo de validacion definida en _setupValidation()
 * @return void
 */
    public function resetValidation(Model $Model) {
        $settings = $this->settings[$Model->alias];
        $runtime = $this->_runtime[$Model->alias];
        
        $Model->validate = $this->_runtime[$Model->alias]['defaultValidation'];
        $this->_runtime[$Model->alias]['loadedTypes'] = array('_default');
    }
}
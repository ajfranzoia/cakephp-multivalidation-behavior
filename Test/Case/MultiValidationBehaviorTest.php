<?php

CakePlugin::load('MultiValidation');
App::uses('MultiValidationBehavior', 'MultiValidation.Model/Behavior');

/**
 * MultiValidation model enabled
 */
class MultiValidationModel extends CakeTestModel {
    
    public $useTable = null;
    
    public $validate = array(
        'field1' => array(
            'mustNotBeBlank' => array(
                'rule' => 'notEmpty'
            )
        ),
        'field2' => array(
            'mustBeANumber' => array(
                'rule' => 'numeric'
            )
        ),
    );

    public $actsAs = array(
        'MultiValidation.MultiValidation' => array(
            'types' => array( 
                'ValidationA' => array(
                    'field2' => array(
                        'mustBeAnUrl' => array(
                            'rule' => 'url'
                        )
                    )
                ),
                'ValidationB' => array(
                    'field3' => array(
                        'mustBeAnEmail' => array(
                            'rule' => 'email'
                        )
                    )
                ),
            )
        )
    );
}

/**
 * MultiValidation Test case
 */
class MultiValidationTest extends CakeTestCase {

/**
 * Creates the model and behavior instance
 *
 * @return void
 */
    public function setUp() {
        $this->Model = new MultiValidationModel();
        $this->Behavior = new MultiValidationBehavior();
    }

/**
 * Destroys the model and behavior instance
 *
 * @return void
 */
    public function tearDown() {
        unset($this->Model);
        unset($this->Behavior);
        ClassRegistry::flush();
    }

/**
 * Test addValidation method
 *
 * @return void
 */
    public function testAddValidation() {
        $target = array(
            'field1' => array(
                'mustNotBeBlank' => array(
                    'rule' => 'notEmpty'
                )
            ),
            'field2' => array(
                'mustBeANumber' => array(
                    'rule' => 'numeric'
                ),
                'mustBeAnUrl' => array(
                    'rule' => 'url'
                )
            ),
        );
        $this->Model->addValidation('ValidationA');
        $this->assertEqual($this->Model->validate, $target);
        $this->assertEqual($this->Model->loadedValidation(), array('_default', 'ValidationA'));
        
        $target = array(
            'field1' => array(
                'mustNotBeBlank' => array(
                    'rule' => 'notEmpty'
                )
            ),
            'field2' => array(
                'mustBeANumber' => array(
                    'rule' => 'numeric'
                ),
                'mustBeAnUrl' => array(
                    'rule' => 'url'
                )
            ),
            'field3' => array(
                'mustBeAnEmail' => array(
                    'rule' => 'email'
                )
            )
        );
        $this->Model->addValidation('ValidationB');
        $this->assertEqual($this->Model->validate, $target);
        $this->assertEqual($this->Model->loadedValidation(), array('_default', 'ValidationA', 'ValidationB'));
    }

/**
 * Test loadValidation method
 *
 * @return void
 */
    public function testLoadValidation() {
        $target = array(
            'field2' => array(
                'mustBeAnUrl' => array(
                    'rule' => 'url'
                )
            ),
        );
        $this->Model->loadValidation('ValidationA');
        $this->assertEqual($this->Model->validate, $target);
        $this->assertEqual($this->Model->loadedValidation(), array('ValidationA'));
        
        $target = array(
            'field3' => array(
                'mustBeAnEmail' => array(
                    'rule' => 'email'
                )
            )
        );
        $this->Model->loadValidation('ValidationB');
        $this->assertEqual($this->Model->validate, $target);
        $this->assertEqual($this->Model->loadedValidation(), array('ValidationB'));
    }

/**
 * Test resetValidation method
 *
 * @return void
 */
    public function testResetValidation() {
        $target = array(
            'field1' => array(
                'mustNotBeBlank' => array(
                    'rule' => 'notEmpty'
                )
            ),
            'field2' => array(
                'mustBeANumber' => array(
                    'rule' => 'numeric'
                )
            ),
        );
        $this->Model->addValidation('ValidationA');
        $this->Model->addValidation('ValidationB');
        $this->Model->resetValidation('ValidationB');
        
        $this->assertEqual($this->Model->validate, $target);
        $this->assertEqual($this->Model->loadedValidation(), array('_default'));
    }
}
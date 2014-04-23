<?php
/**
 * MultiValidationBehaviorTest file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Codaxis (http://codaxis.com)
 * @author        augusto-cdxs (https://github.com/augusto-cdxs/
 * @link          https://github.com/Codaxis/parsley-helper
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

CakePlugin::load('MultiValidation');
App::uses('MultiValidationBehavior', 'MultiValidation.Model/Behavior');

/**
 * MultiValidation model for testing
 */
class MultiValidationModel extends CakeTestModel {

/**
 * @var string
 */
    public $useTable = null;

/**
 * @var array
 */
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

/**
 * @var array
 */
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
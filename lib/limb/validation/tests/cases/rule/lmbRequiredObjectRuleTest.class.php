<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequiredObjectRuleTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
lmb_require(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbRequiredObjectRule.class.php');

class TestObjectForThisRule{}

class lmbRequiredObjectRuleTest extends lmbValidationRuleTestCase
{
  function testValid()
  {
    $rule = new lmbRequiredObjectRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', new TestObjectForThisRule());

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidIfDataspaceIsEmpty()
  {
    $rule = new lmbRequiredObjectRule('testfield');

    $dataspace = new lmbDataspace();

    $this->error_list->expectOnce('addError', array(tr('/validation', 'Object {Field} is required'),
                                                         array('Field'=>'testfield')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testInvalidIfFieldIsNotAnObject()
  {
    $rule = new lmbRequiredObjectRule('testfield');

    $dataspace = new lmbDataspace(array('testfield' => 'whatever_and_not_object'));

    $this->error_list->expectOnce('addError', array(tr('/validation', 'Object {Field} is required'),
                                                         array('Field'=>'testfield')));

    $rule->validate($dataspace, $this->error_list);
  }

  function testNotValidWithClassRestriction()
  {
    $rule = new lmbRequiredObjectRule('testfield', 'Foo');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', new TestObjectForThisRule());

    $this->error_list->expectOnce('addError', array(tr('/validation', 'Object {Field} is required'),
                                                         array('Field'=>'testfield')));
    $rule->validate($dataspace, $this->error_list);
  }
}

?>
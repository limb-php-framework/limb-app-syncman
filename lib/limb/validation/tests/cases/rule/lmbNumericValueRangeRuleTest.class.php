<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbNumericValueRangeRuleTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbNumericValueRangeRule.class.php');

class lmbNumericValueRangeRuleTest extends lmbValidationRuleTestCase
{
  function testInRange()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 0, 5);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 1);

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testLessThanMin()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 5);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', -10);

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be not less than {value}.'),
                                        array('Field' => 'testfield'),
                                        array('value' => 1)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testGreaterThanMax()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 5);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 10);

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be not greater than {value}.'),
                                        array('Field' => 'testfield'),
                                        array('value' => 5)));

    $rule->validate($dataspace, $this->error_list);
  }

  function testOnlyDigitsAllowedNumeric()
  {
    $rule = new lmbNumericValueRangeRule('testfield', 1, 4);

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', '4fdfasd');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be a valid number.'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

}
?>
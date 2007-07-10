<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NSizeRangeTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/validation/rule/lmbI18NSizeRangeRule.class.php');
lmb_require('limb/validation/tests/cases/rule/lmbValidationRuleTestCase.class.php');

class lmbI18NSizeRangeTest extends lmbValidationRuleTestCase
{
  function testSizeRangeRuleEmpty()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 10);

    $data = new lmbDataspace();

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleBlank()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 5, 10);

    $data = new lmbDataspace(array('testfield' => ''));

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleZero()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 5, 10);

    $data = new lmbDataspace(array('testfield' => '0'));

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be greater than {min} characters.'),
                                        array('Field'=>'testfield'),
                                        array('min' => 5)));

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleTooBig()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 3);

    $data = new lmbDataspace(array('testfield' => 'тест'));

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be less than {max} characters.'),
                                        array('Field' => 'testfield'),
                                        array('max' => 3)));

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleTooBig2()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 2, 4);

    $data = new lmbDataspace(array('testfield' => 'тесты'));

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be less than {max} characters.'),
                                        array('Field'=>'testfield'), array('max'=>4)));

    $rule->validate($data, $this->error_list);
  }

  function testSizeRangeRuleTooSmall()
  {
    $rule = new lmbI18NSizeRangeRule('testfield', 30, 100);

    $data = new lmbDataspace(array('testfield' => 'тест'));

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must be greater than {min} characters.'),
                                        array('Field'=>'testfield'),
                                        array('min' => 30)));

    $rule->validate($data, $this->error_list);
  }
}

?>
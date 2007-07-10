<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMatchRuleTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbMatchRule.class.php');

class lmbMatchRuleTest extends lmbValidationRuleTestCase
{
  function testMatchRule()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');
    $dataspace->set('testmatch', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleEmpty()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleEmpty2()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleEmpty3()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testmatch', 'peaches');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testMatchRuleFailure()
  {
    $rule = new lmbMatchRule('testfield', 'testmatch');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'peaches');
    $dataspace->set('testmatch', 'cream');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} does not match {MatchField}.'),
                                        array('Field' => 'testfield', 'MatchField' => 'testmatch')));

    $rule->validate($dataspace, $this->error_list);
  }
}
?>
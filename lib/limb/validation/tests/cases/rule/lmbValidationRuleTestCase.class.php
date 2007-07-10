<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbValidationRuleTestCase.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
require_once('limb/validation/i18n.inc.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/datasource/src/lmbDataspace.class.php');

Mock::generate('lmbErrorList', 'MockErrorList');

abstract class lmbValidationRuleTestCase extends UnitTestCase
{
  protected $error_list;

  function setUp()
  {
    $this->error_list = new MockErrorList();
  }
}
?>
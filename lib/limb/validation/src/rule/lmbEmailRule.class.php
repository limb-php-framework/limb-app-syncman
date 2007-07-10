<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmailRule.class.php 5108 2007-02-19 09:07:53Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

/**
* Checks that field value is a valid Email address.
*/
class lmbEmailRule extends lmbDomainRule
{
  function check($value)
  {
    if (is_integer(strpos($value, '@')))
    {
      list($user, $domain) = split('@', $value, 2);
      $this->_checkUser($user);
      $this->_checkDomain($domain);
    }
    else
      $this->error(tr('/validation', '{Field} must contain a @ character.'));
  }

  protected function _checkUser($value)
  {
    if (!preg_match('/^[a-z0-9]+([_.-][a-z0-9]+)*$/i', $value))
        $this->error(tr('/validation', 'Invalid user in {Field}.'));
  }

  protected function _checkDomain($value)
  {
    parent :: check($value);
  }
}
?>
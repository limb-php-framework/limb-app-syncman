<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDelegate.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require('limb/classkit/src/lmbBaseDelegate.interface.php');
lmb_require('limb/classkit/src/lmbDelegateList.class.php');

class lmbDelegate implements lmbBaseDelegate
{
  protected $object;
  protected $method;

  function __construct(&$object, $method)
  {
    $this->object =& $object;
    $this->method = $method;
  }

  function invoke($args)
  {
    return call_user_func_array(array(&$this->object, $this->method), $args);
  }
}
?>

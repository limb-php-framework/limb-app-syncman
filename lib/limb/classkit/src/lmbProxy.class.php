<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbProxy.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require(dirname(__FILE__) . '/lmbProxyable.interface.php');

abstract class lmbProxy implements lmbProxyable
{
  protected $is_resolved = false;
  protected $original;

  function getHash()
  {
    if(!$this->is_resolved)
      return md5(serialize($this));

    return $this->resolve()->getHash();
  }

  abstract protected function _createOriginalObject();

  function resolve()
  {
    if($this->is_resolved)
      return $this->original;

    $this->original = $this->_createOriginalObject();
    $this->is_resolved = true;

    return $this->original;
  }

  function __call($method, $args = array())
  {
    $this->resolve();
    if(method_exists($this->original, $method))
      return call_user_func_array(array($this->original, $method), $args);
  }

   function __get($attr)
   {
     $this->resolve();
     return $this->original->$attr;
   }

   function __set($attr, $val)
   {
     $this->resolve();
     $this->original->$attr = $val;
   }
}
?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRegistry.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

class lmbRegistry
{
  protected static $cache = array(array());

  static function set($name, $value)
  {
    self :: $cache[$name][0] = $value;
  }

  static function get($name)
  {
    if(isset(self :: $cache[$name][0]))
      return self :: $cache[$name][0];
  }

  static function save($name)
  {
    if(isset(self :: $cache[$name]))
      array_unshift(self :: $cache[$name], array());
    else
      throw new lmbException('no such registry entry', array('name' => $name));
  }

  static function restore($name)
  {
    if(isset(self :: $cache[$name]))
      array_shift(self :: $cache[$name]);
    else
      throw new lmbException('no such registry entry', array('name' => $name));
  }
}
?>

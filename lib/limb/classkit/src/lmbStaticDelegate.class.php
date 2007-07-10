<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticDelegate.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require('limb/classkit/src/lmbBaseDelegate.interface.php');

class lmbStaticDelegate implements lmbBaseDelegate
{
  protected $file;
  protected $method;
  protected $class;

  function __construct($class, $method, $file = NULL)
  {
    $this->class = $class;
    $this->method = $method;
    $this->file = $file;
  }

  function invoke($args)
  {
    if (!is_null($this->file))
    {
      lmb_require($this->file);
      $this->file = NULL;
    }
    return call_user_func_array(array($this->class, $this->method), $args);
  }
}
?>

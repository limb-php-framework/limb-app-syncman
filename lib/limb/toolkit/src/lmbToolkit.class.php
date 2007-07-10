<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbToolkit.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
lmb_require(dirname(__FILE__) . '/lmbToolkitTools.interface.php');
lmb_require(dirname(__FILE__) . '/lmbRegistry.class.php');
lmb_require(dirname(__FILE__) . '/lmbEmptyToolkitTools.class.php');
lmb_require(dirname(__FILE__) . '/lmbCompositeToolkitTools.class.php');
lmb_require(dirname(__FILE__) . '/lmbCompositeNonItersectingToolkitTools.class.php');

class lmbToolkit
{
  protected $tools_copy;
  protected $tools;
  protected $tools_signatures = array();
  protected $signatures_loaded = false;
  static    $class_map = array();

  protected function __construct($tools)
  {
    $this->tools_copy = clone($tools);
    $this->tools = $tools;
  }

  static function instance()
  {
    $instance = lmbRegistry :: get(__CLASS__);

    if(!is_object($instance))
    {
      $instance = new lmbToolkit(new lmbEmptyToolkitTools());
      lmbRegistry :: set(__CLASS__, $instance);
    }
    return $instance;
  }

  static function setup($tools)
  {
    $instance = new lmbToolkit($tools);
    lmbRegistry :: set(__CLASS__, $instance);
    return $instance;
  }

  static function save()
  {
    $toolkit = lmbToolkit :: instance();
    $instance = new lmbToolkit(clone($toolkit->tools_copy));
    lmbRegistry :: save(__CLASS__);
    lmbRegistry :: set(__CLASS__, $instance);
    return $instance;
  }

  static function extend($tools)
  {
    $toolkit = lmbToolkit :: instance();
    return self :: setup(new lmbCompositeNonItersectingToolkitTools($toolkit->tools_copy, $tools));
  }

  static function merge($tools)
  {
    $toolkit = lmbToolkit :: instance();
    return self :: setup(new lmbCompositeToolkitTools($toolkit->tools_copy, $tools));
  }

  static function restore()
  {
    lmbRegistry :: restore(__CLASS__);
    return lmbRegistry :: get(__CLASS__);
  }

  function __call($method, $args)
  {
    $this->_ensureSignatures();

    if(!isset($this->tools_signatures[$method]))
      throw new lmbException('toolkit does not support method "' . $method . '" (no such signature)',
                              array('method' => $method));


    return call_user_func_array(array($this->tools_signatures[$method], $method), $args);
  }

  protected function _ensureSignatures()
  {
    if($this->signatures_loaded)
      return;

    $this->tools_signatures = $this->tools->getToolsSignatures();
    $this->signatures_loaded = true;
  }
}
?>

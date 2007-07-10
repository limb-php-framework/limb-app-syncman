<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMockToolsWrapper.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
class lmbMockToolsWrapper implements lmbToolkitTools
{
  protected $toolkit;
  protected $mock;
  protected $use_only_methods;

  function __construct($mock, $use_only_methods = array())
  {
    $this->mock = $mock;
    $this->use_only_methods = $use_only_methods;
  }

  function getToolsSignatures()
  {
    $signatures = array();
    foreach(get_class_methods(get_class($this->mock)) as $method)
    {
      if($this->use_only_methods && !in_array($method, $this->use_only_methods))
        continue;

      $signatures[$method] = $this->mock;
    }
    return $signatures;
  }
}

?>

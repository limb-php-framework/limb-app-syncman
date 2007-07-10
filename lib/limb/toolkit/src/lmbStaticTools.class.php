<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticTools.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
class lmbStaticTools implements lmbToolkitTools
{
  protected $toolkit;
  protected $call_results;

  function __construct($call_results)
  {
    $this->call_results = $call_results;
  }

  function getToolsSignatures()
  {
    $signatures = array();
    foreach(array_keys($this->call_results) as $method)
    {
      $signatures[$method] = $this;
    }
    return $signatures;
  }

  function __call($method, $args)
  {
    return $this->call_results[$method];
  }
}

?>

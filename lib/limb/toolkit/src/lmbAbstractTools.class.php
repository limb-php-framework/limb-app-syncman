<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAbstractTools.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');

abstract class lmbAbstractTools implements lmbToolkitTools
{
  function getToolsSignatures()
  {
    $methods = get_class_methods(get_class($this));
    $signatures = array();
    foreach($methods as $method)
    {
      $signatures[$method] = $this;
    }

    foreach(get_class_methods('lmbToolkitTools') as $method)
    {
      unset($signatures[$method]);
    }

    return $signatures;
  }
}

?>

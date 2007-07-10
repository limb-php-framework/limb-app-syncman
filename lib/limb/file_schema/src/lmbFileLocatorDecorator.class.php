<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileLocatorDecorator.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/file_schema/src/lmbLocator.interface.php');

class lmbFileLocatorDecorator implements lmbLocator
{
  protected $locator = null;

  function __construct($locator)
  {
    $this->locator = $locator;
  }

  function locate($alias, $params = array())
  {
    return $this->locator->locate($alias, $params);
  }

  function locateAll($prefix = '/')
  {
    return $this->locator->locateAll($prefix);
  }

  function getFileLocations()
  {
    return $this->locator->getFileLocations();
  }
}

?>
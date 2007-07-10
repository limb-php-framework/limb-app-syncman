<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileLocator.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/util/src/exception/lmbFileNotFoundException.class.php');//why there?
lmb_require('limb/file_schema/src/lmbLocator.interface.php');

class lmbFileLocator implements lmbLocator
{
  protected $locations;
  protected $alias_format;

  function __construct($locations, $alias_format = '*')
  {
    $this->locations = $locations;
    $this->alias_format = $alias_format;
  }

  function locate($alias, $params = array())
  {
    if(lmbFs :: isPathAbsolute($alias))
    {
       if(file_exists($alias))
         return $alias;
       else
         $this->_handleNotResolvedAlias($alias);
    }

    $alias = $this->_processAlias($alias, $params);

    $paths = $this->locations->getLocations($params);
    foreach($paths as $path)
    {
      if(file_exists($path . '/' . $alias))
        return $path . '/' . $alias;
    }

    $this->_handleNotResolvedAlias($alias);
  }

  function getFileLocations()
  {
    return $this->locations;
  }

  function locateAll($prefix = '')
  {
    $result = array();

    $paths = $this->locations->getLocations();
    foreach($paths as $path)
    {
      if($files = glob($path . $prefix . $this->alias_format))
        $result = array_merge($result, $files);
    }

    return array_unique($result);
  }

  protected function _processAlias($alias, $params)
  {
    return str_replace('*', $alias, $this->alias_format);
  }

  protected function _handleNotResolvedAlias($alias)
  {
    throw new lmbFileNotFoundException($alias, 'file alias not resolved');
  }
}

?>
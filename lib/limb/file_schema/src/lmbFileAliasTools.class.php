<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileAliasTools.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/file_schema/src/lmbFileLocator.class.php');
lmb_require('limb/file_schema/src/lmbCachingFileLocator.class.php');

class lmbFileAliasTools extends lmbAbstractTools
{
  protected $file_locators = array();

  function findFileAlias($name, $paths, $file_group)
  {
    $locator = lmbToolkit :: instance()->getFileLocator($paths, $file_group);
    return $locator->locate($name);
  }

  function getFileLocator($path, $file_group)
  {
    if(isset($this->file_locators[$file_group]))
       return $this->file_locators[$file_group];

    lmb_require('limb/file_schema/src/lmbIncludePathFileLocations.class.php');
    $file_locations = new lmbIncludePathFileLocations(explode(';', $path));
    $locator = new lmbCachingFileLocator(new lmbFileLocator($file_locations), $file_group);

    $this->file_locators[$file_group] = $locator;
    return $locator;
  }

  //???
  static function createFileLocations($path)
  {
    lmb_require('limb/file_schema/src/lmbIncludePathFileLocations.class.php');
    return new lmbIncludePathFileLocations(explode(';', $path));
  }
}
?>

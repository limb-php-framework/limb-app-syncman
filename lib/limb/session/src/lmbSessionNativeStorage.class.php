<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionNativeStorage.class.php 5070 2007-02-15 10:14:50Z serega $
 * @package    session
 */
lmb_require('limb/session/src/lmbSessionStorage.interface.php');

/**
 * lmbSessionNativeStorage does nothing thus keeping native file-based php session storage to be used.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionNativeStorage.class.php 5070 2007-02-15 10:14:50Z serega $
 */
class lmbSessionNativeStorage implements lmbSessionStorage
{
  /**
   * Does nothing
   * @see lmbSessionStorage :: storageInstall()
   */
  function storageInstall()
  {
  }
}
?>
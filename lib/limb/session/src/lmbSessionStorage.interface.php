<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionStorage.interface.php 5070 2007-02-15 10:14:50Z serega $
 * @package    session
 */

/**
 * Very simple interface for session storage driver classes.
 * @version $Id: lmbSessionStorage.interface.php 5070 2007-02-15 10:14:50Z serega $
 */
interface lmbSessionStorage
{
  /**
   * Installs specific session storage functions
   */
  function storageInstall();
}
?>
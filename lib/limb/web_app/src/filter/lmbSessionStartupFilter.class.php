<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionStartupFilter.class.php 5069 2007-02-15 10:14:34Z serega $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/session/src/lmbSession.class.php');


/**
 * Tells lmbSessionStartupFilter filter to use either native (file based) session storage driver (default) or database session storage driver
 * @see lmbSessionStartupFilter
 */
@define('LIMB_USE_DB_DRIVER', false);

/**
 * lmbSessionStartupFilter installs session storage driver and starts session.
 *
 * What session storage driver will be used is depend on {@link LIMB_USE_DB_DRIVER} constant value.
 * If LIMB_USE_DB_DRIVER has FALSE value or not defined - native file based session storage will be used.
 * Otherwise database storage driver will be installed.
 * @see lmbSessionNativeStorage
 * @see lmbSessionDbStorage
 *
 * @version $Id: lmbSessionStartupFilter.class.php 5069 2007-02-15 10:14:34Z serega $
 */
class lmbSessionStartupFilter implements lmbInterceptingFilter
{
  /**
   * @see lmbInterceptingFilter :: run()
   * @uses LIMB_USE_DB_DRIVER
   */
  function run($filter_chain)
  {
    if(constant('LIMB_USE_DB_DRIVER'))
      $driver =  $this->_createDBSessionStorage();
    else
      $driver =  $this->_createNativeSessionStorage();

    $driver->storageInstall();

    lmbSession :: start();

    $filter_chain->next();
  }

  protected function _createNativeSessionStorage()
  {
    lmb_require('limb/session/src/lmbSessionNativeStorage.class.php');
    return new lmbSessionNativeStorage();
  }

  /**
   * Creates object of {@link lmbSessionDbStorage} class.
   * If constant LIMB_SESSION_DB_MAX_LIFE_TIME is defined passed it's value as session max life time
   * @see lmbInterceptingFilter :: run()
   * @uses LIMB_SESSION_DB_MAX_LIFE_TIME
   */
  protected function _createDBSessionStorage()
  {
    if(defined('LIMB_SESSION_DB_MAX_LIFE_TIME') &&  constant('LIMB_SESSION_DB_MAX_LIFE_TIME'))
      $max_life_time = constant('LIMB_SESSION_DB_MAX_LIFE_TIME');
    else
      $max_life_time = null;

    lmb_require('limb/session/src/lmbSessionDbStorage.class.php');
    $db_connection = lmbToolkit :: instance()->getDefaultDbConnection();
    return new lmbSessionDbStorage($db_connection, $max_life_time);
  }
}
?>

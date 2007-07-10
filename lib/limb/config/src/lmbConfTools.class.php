<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbConfTools.class.php 4990 2007-02-08 15:35:31Z pachanga $
 * @package    config
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/config/src/lmbIni.class.php');

@define('LIMB_INI_INCLUDE_PATH', 'settings;limb/*/settings');
@define('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings');

class lmbConfTools extends lmbAbstractTools
{
  protected $ini = array();
  protected $confs = array();

  function createIni($name)
  {
    return $this->getIni($name);
  }

  function getIni($name)
  {
    if(isset($GLOBALS['testing_ini'][$name]))
      return new lmbIni(LIMB_VAR_DIR . '/' . $name);

    if(isset($this->ini[$name]))
      return $this->ini[$name];

    if(!defined('LIMB_INI_INCLUDE_PATH'))
       throw new lmbException('LIMB_INI_INCLUDE_PATH constant is not defined!');

    lmb_require('limb/config/src/lmbCachedIni.class.php');
    $file = lmbToolkit :: instance()->findFileAlias($name, LIMB_INI_INCLUDE_PATH, 'ini');

    $this->ini[$name] = new lmbCachedIni($file);
    return $this->ini[$name];
  }

  function setTestingIni($ini_path, $content)
  {
    if(isset($GLOBALS['testing_ini'][$ini_path]))
      throw new Exception("Duplicate testing ini file registration('$ini_path') not allowed.");

    $GLOBALS['testing_ini'][$ini_path] = 1;

    lmbFs :: mkdir(dirname(LIMB_VAR_DIR . '/' . $ini_path));

    $f = fopen(LIMB_VAR_DIR . '/' . $ini_path, 'w');
    fwrite($f, $content, strlen($content));
    fclose($f);
  }

  function clearTestingIni($ini_path = null)
  {
    if(!isset($GLOBALS['testing_ini']) ||  !count($GLOBALS['testing_ini']))
      return;

    lmbFs :: rm(LIMB_VAR_DIR . '/ini/');

    if(!is_null($ini_path))
    {
      $this->_doClearTestingIni($ini_path);
    }
    else
    {
      foreach(array_keys($GLOBALS['testing_ini']) as $ini_path)
        $this->_doClearTestingIni($ini_path);
    }
    clearstatcache();
  }

  protected function _doClearTestingIni($file)
  {
    if(isset($GLOBALS['testing_ini'][$file]) && file_exists(LIMB_VAR_DIR . '/' . $file))
    {
      unlink(LIMB_VAR_DIR . '/' . $file);
      unset($GLOBALS['testing_ini'][$file]);
    }
  }

  function getConf($conf)
  {
    if(isset($this->confs[$conf]))
      return $this->confs[$conf];

    if(!defined('LIMB_CONF_INCLUDE_PATH'))
       throw new lmbException('LIMB_CONF_INCLUDE_PATH constant is not defined!');

    lmb_require('limb/config/src/lmbConf.class.php');
    $file = lmbToolkit :: instance()->findFileAlias("$conf.conf.php", LIMB_CONF_INCLUDE_PATH, 'config');

    $this->confs[$conf] = new lmbConf($file);
    return $this->confs[$conf];
  }
}
?>

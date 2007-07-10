<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestShellUI.class.php 5058 2007-02-13 13:25:05Z pachanga $
 * @package    tests_runner
 */
require_once('Console/Getopt.php');
require_once(dirname(__FILE__) . '/lmbTestTree.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreePath.class.php');
require_once(dirname(__FILE__) . '/lmbTestShellReporter.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreeDirNode.class.php');
require_once(dirname(__FILE__) . '/lmbFile2TestNodeMapper.class.php');

class lmbTestShellUI
{
  protected $tree;
  protected $argv;

  function __construct($argv = null)
  {
    $this->argv = is_null($argv) ? Console_Getopt::readPHPArgv() : $argv;

    if(PEAR::isError($this->argv))
    {
      echo('Fatal Error: ' . $this->argv->getMessage() . "\n");
      exit(1);
    }
  }

  protected function _usage($code = 0)
  {
    $script = $this->argv[0];
    $usage = <<<EOD
Usage: $script [OPTION] <file/dir>
  -c, --config=/file.php  PHP configuration path
  -h, --help              display this help and exit

EOD;
    echo $usage;
    exit($code);
  }

  static function getShortOpts()
  {
    return 'ht:b:c:';
  }

  static function getLongOpts()
  {
    return array('help', 'config=');
  }

  function run()
  {
    $short_opts = self :: getShortOpts();
    $long_opts = self :: getLongOpts();
    $options = Console_Getopt::getopt($this->argv, $short_opts, $long_opts);
    if(PEAR::isError($options))
      $this->_usage(1);

    foreach($options[0] as $option)
    {
      switch($option[0])
      {
        case 'h':
        case '--help':
          $this->_usage(0);
          break;
        case 'c':
        case '--config':
          include_once(realpath($option[1]));
          break;
      }
    }

    $res = true;
    $found = false;

    if(!isset($options[1][0]))
      $this->_usage(1);

    foreach(glob($this->_normalizePath($options[1][0])) as $file)
    {
      $found = true;
      $root_dir = $this->_getRootDir($file);
      $node = $this->_mapFileToNode($root_dir, $file);
      $tree = $this->_initTree($root_dir);
      $res = $res & $tree->perform($node, $this->_getReporter());
    }

    if(!$found)
      $this->_usage(1);

    exit($res ? 0 : 1);
  }

  protected function _normalizePath($path)
  {
    if($this->_isAbsolutePath($path))
      return $path;
    else
      return $this->_getcwd() . DIRECTORY_SEPARATOR . $path;
  }

  /**
   * Due to require_once error in PHP before 5.2 version this method 'strtolowers' paths under windows
   */
  protected function _getcwd()
  {
    $wd = getcwd();
    //win32 check
    if(DIRECTORY_SEPARATOR == '\\')
      $wd = strtolower($wd);
    return $wd;
  }

  protected function _isAbsolutePath($path)
  {
    return $path{0} == '/' || preg_match('~^[a-z]:~i', $path);
  }

  protected function _initTree($root_node)
  {
    return new lmbTestTree(new lmbTestTreeDirNode($root_node));
  }

  protected function _mapFileToNode($root_dir, $file)
  {
    $mapper = new lmbFile2TestNodeMapper();
    return $mapper->map($root_dir, $file);
  }

  protected function _getRootDir($file)
  {
    $path_items = explode(DIRECTORY_SEPARATOR, $file);
    //windows/linux filesystem paths style check
    return empty($path_items[0]) ?
              DIRECTORY_SEPARATOR . $path_items[1] :  //unix
              $path_items[0] . DIRECTORY_SEPARATOR;   //windows
  }

  protected function _getReporter()
  {
    return new lmbTestShellReporter();
  }
}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeShellUI.class.php 5057 2007-02-13 13:06:19Z pachanga $
 * @package    tests_runner
 */
require_once('Console/Getopt.php');
require_once(dirname(__FILE__) . '/lmbTestTree.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreePath.class.php');
require_once(dirname(__FILE__) . '/lmbTestShellReporter.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreeDirNode.class.php');

class lmbTestTreeShellUI
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

  static function getUsage()
  {
    $usage = <<<EOD
[OPTION] <dir>
  -c, --config=/file.php  PHP configuration path
  -b, --browse=PATH       list available tests cases at specified node
  -t, --test=PATH         test specified test cases node
  -h, --help              display this help and exit

EOD;
    return $usage;
  }

  protected function _usage($code = 0)
  {
    $script = $this->argv[0];
    echo self :: getUsage();
    exit($code);
  }

  static function getShortOpts()
  {
    return 'ht:b:c:';
  }

  static function getLongOpts()
  {
    return array('help', 'test=', 'browse=', 'config=');
  }

  function run()
  {
    $browse_path = false;
    $perform_path = false;

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
        case 't':
        case '--test':
          $perform_path = lmbTestTreePath :: normalize($option[1]);
          break;
        case 'b':
        case '--browse':
          $browse_path = lmbTestTreePath :: normalize($option[1]);
          break;
      }
    }

    $this->_initTree($options[1][0]);

    if($browse_path)
    {
      $res = $this->_browse($browse_path);
    }
    elseif($perform_path)
    {
      $res = $this->_perform($perform_path);
    }
    elseif(!$browse_path && !$perform_path)
    {
      $res = $this->_browse();
    }
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

  protected function _perform($path)
  {
    return $this->tree->perform($path, $this->_getReporter());
  }

  protected function _browse($path='/')
  {
    $node = $this->tree->find($path);

    echo "Available test cases in \n'=== " . $node->getTestLabel() . " ===' :\n";
    $sub_nodes = $node->getChildren();

    if(sizeof($sub_nodes))
    {
      foreach($sub_nodes as $index => $node)
        echo $this->_normalizeNodePath($path . '/' . $index) . ' ' . $node->getTestLabel() . "\n";

      echo "\n";
    }
    else
      echo "No tests available.\n";

    return true;
  }

  protected function _initTree($dir)
  {
    $this->tree = new lmbTestTree(new lmbTestTreeDirNode($this->_normalizePath($dir)));
  }

  protected function _getReporter()
  {
    return new lmbTestShellReporter();
  }

  protected function _normalizeNodePath($path)
  {
    return lmbTestTreePath :: normalize($path);
  }
}

?>
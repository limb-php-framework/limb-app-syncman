<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeShellUITest.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestTreeShellUI.class.php');

class lmbTestTreeShellUITest extends lmbTestsUtilitiesBase
{
  function setUp()
  {
    $this->_rmdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR . '/cases');
    $this->_createRunScript(LIMB_VAR_DIR . '/cases');
  }

  function tearDown()
  {
    $this->_rmdir(LIMB_VAR_DIR);
  }

  function testPerformAllAbsolutePath()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $run_dir = LIMB_VAR_DIR . '/cases';
    $ret = $this->_execScript('-t / ' . $run_dir, $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+3\s+done\(' . $zoo->getClass() . '\)~', $screen);
    $this->assertPattern('~2\s+of\s+3\s+done\(' . $bar->getClass() . '\)~', $screen);
    $this->assertPattern('~3\s+of\s+3\s+done\(' . $foo->getClass() . '\)~', $screen);
    $this->assertPattern('~OK~i', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function testPerformAllRelativePath()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $cwd = getcwd();
    chdir(LIMB_VAR_DIR);
    $ret = $this->_execScript('-t / cases', $screen);

    chdir($cwd);

    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+3\s+done\(' . $zoo->getClass() . '\)~', $screen);
    $this->assertPattern('~2\s+of\s+3\s+done\(' . $bar->getClass() . '\)~', $screen);
    $this->assertPattern('~3\s+of\s+3\s+done\(' . $foo->getClass() . '\)~', $screen);
    $this->assertPattern('~OK~i', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function testPerformNode()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $run_dir = LIMB_VAR_DIR . '/cases';
    $ret = $this->_execScript('-t/0 ' . $run_dir, $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+2\s+done\(' . $zoo->getClass() . '\)~', $screen);
    $this->assertPattern('~2\s+of\s+2\s+done\(' . $bar->getClass() . '\)~', $screen);
    $this->assertPattern('~OK~i', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function testBrowse()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $run_dir = LIMB_VAR_DIR . '/cases';
    $ret = $this->_execScript('-b/ ' . $run_dir, $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~\n\/0\s+Group\s+test\s+in\s+".*/cases/a"~', $screen);
    $this->assertPattern('~\n\/1\s+foo_test\.php~', $screen);
  }

  function testBrowseTerminal()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');

    $run_dir = LIMB_VAR_DIR . '/cases';
    $ret = $this->_execScript('-b/0 ' . $run_dir, $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~No\s+tests\s+available~', $screen);
  }

  function _createRunScript($tests_dir)
  {
    $dir = dirname(__FILE__);
    $simpletest = SIMPLE_TEST;

    $script = <<<EOD
<?php
define('SIMPLE_TEST', '$simpletest');
define('LIMB_VAR_DIR', dirname(__FILE__) . '/var');
require_once('$dir/../../common.inc.php');
require_once('$dir/../../src/lmbTestTreeShellUI.class.php');

\$ui = new lmbTestTreeShellUI();
\$ui->run();
?>
EOD;
    file_put_contents($this->_runScript(), $script);
  }

  function _runScript()
  {
    return LIMB_VAR_DIR . '/runtests.php ';
  }

  function _execScript($extra, &$screen)
  {
    exec('php ' . $this->_runScript() . ' ' . $extra, $out, $ret);
    $screen = implode("\n", $out);
    return $ret;
  }

  function _createTestCase($file)
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass();
    file_put_contents($file, $generated->generate());
    return $generated;
  }
}

?>

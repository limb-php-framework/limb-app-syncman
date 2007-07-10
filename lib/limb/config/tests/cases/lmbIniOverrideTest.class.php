<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIniOverrideTest.class.php 4990 2007-02-08 15:35:31Z pachanga $
 * @package    config
 */
lmb_require('limb/config/src/lmbIni.class.php');

class lmbIniOverrideTest extends UnitTestCase
{
  function setUp()
  {
    $this->toolkit = lmbToolkit :: instance();
  }

  function tearDown()
  {
    $this->toolkit->clearTestingIni();
  }

  function testOverrideGroupValuesProperly()
  {
    $this->toolkit->setTestingIni(
      'testing2.ini',
      '
        [Templates]
        conf = 1
        force_compile = 0
        path = design/templates/      '
    );

    $this->toolkit->setTestingIni(
      'testing2.override.ini',
      '
        [Templates]
        conf =
        force_compile = 1
      '
    );

    $ini = new lmbIni(LIMB_VAR_DIR . '/testing2.ini');

    $this->assertEqual($ini->getOption('conf', 'Templates'), null);
    $this->assertEqual($ini->getOption('path', 'Templates'), 'design/templates/');
    $this->assertEqual($ini->getOption('force_compile', 'Templates'), 1);
  }

  /*
  function testOverrideUseRealFile()
  {
    $ini = new lmbIni('limb/core/tests/cases/util/ini_test2.ini');

    $this->assertTrue($ini->hasGroup('test1'));
    $this->assertTrue($ini->hasGroup('test2'));

    $this->assertEqual($ini->getOption('v1', 'test1'), 1);
    $this->assertEqual($ini->getOption('v2', 'test1'), 2);
    $this->assertEqual($ini->getOption('v3', 'test1'), 3);
    $this->assertEqual($ini->getOption('v1', 'test2'), 1);
  }*/
}

?>
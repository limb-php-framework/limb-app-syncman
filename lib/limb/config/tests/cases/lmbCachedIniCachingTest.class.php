<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedIniCachingTest.class.php 4990 2007-02-08 15:35:31Z pachanga $
 * @package    config
 */
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('limb/config/src/lmbIni.class.php');

Mock :: generatePartial('lmbCachedIni',
                        'CachedIniTestVersion',
                        array('_createIni'));

class lmbCachedIniCachingTest extends UnitTestCase
{
  var $cache_dir;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: instance();
    $this->cache_dir = LIMB_VAR_DIR . '/ini/';
    lmbFs :: rm($this->cache_dir);
  }

  function tearDown()
  {
    $this->toolkit->clearTestingIni();
  }

  function testCacheMissHit()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini1 = new CachedIniTestVersion($this);

    $ini1->expectOnce('_createIni');
    $ini1->setReturnValue('_createIni', new lmbIni($file = LIMB_VAR_DIR . '/testing.ini'), array($file));

    $ini1->__construct($file, $this->cache_dir);

    $this->assertEqual($ini1->getOption('test'), 1);

    $ini2 = new CachedIniTestVersion($this);

    $ini2->expectNever('_createIni');

    $ini2->__construct($file, $this->cache_dir);

    $this->assertEqual($ini2->getOption('test'), 1);

    $ini2->removeCache();
  }

  function testCacheHitOriginalFileWasModified()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini1 = new CachedIniTestVersion($this);

    $ini1->expectOnce('_createIni');
    $ini1->setReturnValue('_createIni', new lmbIni($file = LIMB_VAR_DIR . '/testing.ini'), array($file));

    $ini1->__construct($file, $this->cache_dir);

    $this->assertEqual($ini1->getOption('test'), 1);

    touch($file, filemtime($file) + 100);

    $ini2 = new CachedIniTestVersion($this);

    $ini2->expectOnce('_createIni');
    $ini2->setReturnValue('_createIni', new lmbIni($file), array($file));

    $ini2->__construct($file, $this->cache_dir);

    $this->assertEqual($ini2->getOption('test'), 1);

    $ini2->removeCache();
  }

  function testCacheHitOverrideFileWasModified()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini1 = new CachedIniTestVersion();

    $ini1->expectOnce('_createIni');
    $ini1->setReturnValue('_createIni', new lmbIni($file = LIMB_VAR_DIR . '/testing.ini'), array($file));

    $ini1->__construct($file, $this->cache_dir);

    $this->assertEqual($ini1->getOption('test'), 1);

    //making sure about cache hit
    $ini2 = new CachedIniTestVersion();
    $ini2->__construct($file, $this->cache_dir);
    $this->assertEqual($ini2->getOption('test'), 1);

    $this->toolkit->setTestingIni(
      'testing.override.ini',
      'test = 2'
    );

    touch(LIMB_VAR_DIR . '/testing.override.ini', filemtime(LIMB_VAR_DIR . '/testing.override.ini') + 100);

    $ini3 = new CachedIniTestVersion();

    $ini3->expectOnce('_createIni');
    $ini3->setReturnValue('_createIni', new lmbIni($file), array($file));

    $ini3->__construct($file, $this->cache_dir);

    $this->assertEqual($ini3->getOption('test'), 2);

    $ini3->removeCache();
  }
}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NSyncUtilitySynchMethodsTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/utility/lmbI18NSyncUtility.class.php');
lmb_require('limb/i18n/src/dictionary/lmbQtXmlDictionaryManager.class.php');

Mock :: generate('lmbCliResponse', 'MockCliResponse');

class lmbI18NSyncUtilitySynchMethodsTest extends UnitTestCase
{
  function setUp()
  {
    $this->_cleanUp();
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations/i18n/ru');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations/i18n/de');
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/translations');
  }

  function testSynchTranslationsFiles()
  {
    $starting_dir = LIMB_VAR_DIR . '/translations/';
    $prefix = '/i18n/';
    $original_file = $starting_dir . $prefix . '/translation.ts';
    $ru_file = $starting_dir . $prefix .'/ru/translation.ts';
    $de_file = $starting_dir . $prefix .'/de/translation.ts';

    $original = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
<message>
    <source>Cat</source>
    <translation>Cat</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($original_file, $original);

    $ru = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $ru);

    $de = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Cat</source>
    <translation>Katze</translation>
</message>
</context>
</TS>
EOD;

    file_put_contents($de_file, $de);

    $cli_responce = new MockCliResponse();
    $checker = new lmbI18NSyncUtility($prefix, $cli_responce);
    $cli_responce->expectCallCount('write', 2);
    $checker->synchTranslationsFiles($starting_dir);

    $qt_manager = new lmbQtXmlDictionaryManager();

    $ru_dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($ru_dictionary, $ru_file);
    $this->assertTrue($ru_dictionary->hasEntry('/foo/bar', 'Dog'));
    $this->assertTrue($ru_dictionary->hasEntry('/foo/bar', 'Cat'));
    $this->assertEqual($ru_dictionary->translate('/foo/bar', 'Dog'), 'Собака');

    $de_dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($de_dictionary, $de_file);
    $this->assertTrue($de_dictionary->hasEntry('/foo/bar', 'Dog'));
    $this->assertTrue($de_dictionary->hasEntry('/foo/bar', 'Cat'));
    $this->assertEqual($de_dictionary->translate('/foo/bar', 'Cat'), 'Katze');
  }

  function testGetNotSynchedFiles()
  {
    $starting_dir = LIMB_VAR_DIR . '/translations/';
    $prefix = '/i18n/';
    $original_file = $starting_dir . $prefix . 'translation.ts';
    $ru_file = $starting_dir . $prefix . '/ru/translation.ts';
    $de_file = $starting_dir . $prefix . '/de/translation.ts';

    $original = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
<message>
    <source>Cat</source>
    <translation>Cat</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($original_file, $original);

    $ru = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $ru);

    $de = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Cat</source>
    <translation>Katze</translation>
</message>
</context>
</TS>
EOD;

    file_put_contents($de_file, $de);

    $checker = new lmbI18NSyncUtility($prefix,  null);
    $not_synched_files = $checker->getNotSynchedTranslationsFiles($starting_dir);

    foreach($not_synched_files as $id => $path)
      $not_synched_files[$id] = lmbFs::normalizePath($path);

    $expected = array(lmbFs::normalizePath($de_file), lmbFs::normalizePath($ru_file));
    sort($not_synched_files);
    sort($expected);
    $i = 0;
    foreach($expected as $file)
    {
      $this->assertEqual($file, $not_synched_files[$i]);
      $i++;
    }
  }

  function testFilesAreSynched()
  {
    $starting_dir = LIMB_VAR_DIR . '/translations/';
    $prefix = '/i18n/';
    $original_file = $starting_dir . $prefix . 'translation.ts';
    $ru_file = $starting_dir . $prefix . '/ru/translation.ts';

    $original = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
<message>
    <source>Cat</source>
    <translation>Cat</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($original_file, $original);

    $ru = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
<message>
    <source>Cat</source>
    <translation>Кошка</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $ru);

    $checker = new lmbI18NSyncUtility($prefix, null);
    $not_syncked_files = $checker->getNotSynchedTranslationsFiles($starting_dir);

    $this->assertFalse($not_syncked_files);
  }
}
?>
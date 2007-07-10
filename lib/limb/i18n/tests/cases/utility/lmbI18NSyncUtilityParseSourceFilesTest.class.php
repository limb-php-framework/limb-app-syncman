<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NSyncUtilityParseSourceFilesTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/utility/lmbI18NSyncUtility.class.php');
lmb_require('limb/i18n/src/dictionary/lmbQtXmlDictionaryManager.class.php');

Mock :: generate('lmbCliResponse', 'MockCliResponse');

class lmbI18NSyncUtilityParseSourceFilesTest extends UnitTestCase
{
  function setUp()
  {
    $this->_cleanUp();
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations/i18n');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/parse');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/parse1');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/parse2');
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/translations');
    lmbFs :: rm(LIMB_VAR_DIR . '/parse');
    lmbFs :: rm(LIMB_VAR_DIR . '/parse1');
    lmbFs :: rm(LIMB_VAR_DIR . '/parse2');
  }

  function testUpdateDictionaryFromSourceFiles()
  {
    $tranlations_dir = LIMB_VAR_DIR . '/translations/';
    $prefix = '/i18n/';
    $original_file = $tranlations_dir . $prefix . 'translation.ts';
    $parse_dir = LIMB_VAR_DIR . '/parse/';
    $html_file = $parse_dir . 'hourse.html';
    $php_file = $parse_dir . 'cat.php';

    $original = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($original_file, $original);

    $php = <<< EOD
<?php
tr('/foo','Cat');
?>
EOD;
    file_put_contents($php_file, $php);

    $html = <<< EOD
{\$'Hourse'|i18n:'/foo'}
EOD;
    file_put_contents($html_file, $html);

    $cli_responce = new MockCliResponse();
    $updater = new lmbI18NSyncUtility($prefix, $cli_responce);
    $cli_responce->expectCallCount('write', 3);
    $updater->updateDictionaryFromSourceFiles($tranlations_dir, $parse_dir);

    $qt_manager = new lmbQtXmlDictionaryManager();

    $dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($dictionary, $original_file);
    $this->assertTrue($dictionary->hasEntry('/foo', 'Hourse'));
    $this->assertTrue($dictionary->hasEntry('/foo', 'Cat'));
    $this->assertTrue($dictionary->hasEntry('/foo/bar', 'Dog'));
  }

  function testUpdateDictionaryFromPackagesSourceFiles()
  {
    $tranlations_dir = LIMB_VAR_DIR . '/translations/';
    $prefix = '/i18n/';
    $original_file = $tranlations_dir . $prefix . 'translation.ts';
    $parse_dir1 = LIMB_VAR_DIR . '/parse1/';
    $html_file1 = $parse_dir1 . 'hourse.html';
    $php_file1 = $parse_dir1 . 'cat.php';

    $parse_dir2 = LIMB_VAR_DIR . '/parse2/';
    $html_file2 = $parse_dir2 . 'dog.html';
    $php_file2 = $parse_dir2 . 'boy.php';

    $original = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<name>/foo/bar</name>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($original_file, $original);

    $php1 = <<< EOD
<?php
tr('/foo','Cat');
?>
EOD;
    file_put_contents($php_file1, $php1);

    $html1 = <<< EOD
{\$'Hourse'|i18n:'/foo'}
EOD;
    file_put_contents($html_file1, $html1);

    $php2 = <<< EOD
<?php
tr('/foo','Dog');
?>
EOD;
    file_put_contents($php_file2, $php2);

    $html2 = <<< EOD
{\$'Boy'|i18n:'/foo'}
EOD;
    file_put_contents($html_file2, $html2);

    $cli_responce = new MockCliResponse();
    $updater = new lmbI18NSyncUtility($prefix, $cli_responce);
    $updater->updateDictionaryFromPackagesSourceFiles($tranlations_dir, array($parse_dir1, $parse_dir2));

    $qt_manager = new lmbQtXmlDictionaryManager();

    $dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($dictionary, $original_file);
    $this->assertTrue($dictionary->hasEntry('/foo', 'Hourse'));
    $this->assertTrue($dictionary->hasEntry('/foo', 'Cat'));
    $this->assertTrue($dictionary->hasEntry('/foo/bar', 'Dog'));
    $this->assertTrue($dictionary->hasEntry('/foo', 'Boy'));
    $this->assertTrue($dictionary->hasEntry('/foo', 'Dog'));
  }
}
?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NSyncUtilityUpdateMethodsTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/utility/lmbI18NSyncUtility.class.php');
lmb_require('limb/i18n/src/dictionary/lmbQtXmlDictionaryManager.class.php');

Mock :: generate('lmbCliResponse', 'MockCliResponse');

class lmbI18NSyncUtilityUpdateMethodsTest extends UnitTestCase
{
  function setUp()
  {
    $this->_cleanUp();
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations/application/i18n/');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations/packages/package1/i18n/');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations/packages/package2/i18n/');
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR. '/translations');
  }

  function testUpdateApplicationDictionary()
  {
    $application_path = LIMB_VAR_DIR . '/translations/application/';
    $translation_file = $application_path . '/i18n/translation.ts';
    $packages_path = LIMB_VAR_DIR . '/translations/packages/';
    $package1_path = $packages_path . 'package1/';
    $package1_tr_file_path = $package1_path . '/i18n/translation.ts';

    $package2_path = $packages_path . 'package2/';
    $package2_tr_file_path = $package2_path . '/i18n/translation.ts';

    $no_existing_package_path = $packages_path . 'package3/';

    $application_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/section1</name>
<message>
    <source>value1</source>
    <translation type="unfinished"/>
</message>
</context>
</TS>
EOD;
    file_put_contents($translation_file, $application_tr_file);

    $package1_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/section1</name>
<message>
    <source>value1</source>
    <translation>value1 translation</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($package1_tr_file_path, $package1_tr_file);

    $package2_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/bar/section1</name>
<message>
    <source>value2</source>
    <translation>value2</translation>
</message>
</context>
</TS>
EOD;

    file_put_contents($package2_tr_file_path, $package2_tr_file);

    $packages_paths = array($package1_path, $package2_path, $no_existing_package_path);

    $response = new MockCliResponse();
    $application_update = new lmbI18NSyncUtility($prefix = '/i18n/', $response);
    $response->expectCallCount('write', 3);
    $application_update->updateApplicationDictionary($application_path, $packages_paths);

    $qt_manager = new lmbQtXmlDictionaryManager();

    $dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($dictionary, $translation_file);
    $this->assertTrue($dictionary->hasEntry('/foo/section1', 'value1'));
    $this->assertTrue($dictionary->isTranslated('/foo/section1', 'value1'));
    $this->assertEqual($dictionary->translate('/foo/section1', 'value1'), 'value1 translation');
    $this->assertTrue($dictionary->hasEntry('/bar/section1', 'value2'));
  }

  function testUpdateApplicationDictionaryDontMergeWithItself()
  {
    $application_path = LIMB_VAR_DIR . '/translations/application/';
    $translation_file = $application_path . '/i18n/translation.ts';
    $packages_path = LIMB_VAR_DIR . '/translations/packages/';
    $package1_path = $packages_path . 'package1/';
    $package1_tr_file_path = $package1_path . '/i18n/translation.ts';

    $package2_path = $packages_path . 'package2/';
    $package2_tr_file_path = $package2_path . '/i18n/translation.ts';

    $application_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/section1</name>
<message>
    <source>value1</source>
    <translation type="unfinished"/>
</message>
</context>
</TS>
EOD;
    file_put_contents($translation_file, $application_tr_file);

    $package1_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/section1</name>
<message>
    <source>value1</source>
    <translation>value1 translation</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($package1_tr_file_path, $package1_tr_file);

    $package2_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/bar/section1</name>
<message>
    <source>value2</source>
    <translation>value2</translation>
</message>
</context>
</TS>
EOD;

    file_put_contents($package2_tr_file_path, $package2_tr_file);

    $packages_paths = array($package1_path, $package2_path, $application_path);

    $response = new MockCliResponse();
    $application_update = new lmbI18NSyncUtility($prefix = '/i18n/', $response);
    $response->expectCallCount('write', 3);
    $application_update->updateApplicationDictionary($application_path, $packages_paths);

    $qt_manager = new lmbQtXmlDictionaryManager();

    $dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($dictionary, $translation_file);
    $this->assertTrue($dictionary->hasEntry('/foo/section1', 'value1'));
    $this->assertTrue($dictionary->isTranslated('/foo/section1', 'value1'));
    $this->assertEqual($dictionary->translate('/foo/section1', 'value1'), 'value1 translation');
    $this->assertTrue($dictionary->hasEntry('/bar/section1', 'value2'));
  }

  function testUpdatePackageDictionariesFromApplicationDictionary()
  {
    $application_path = LIMB_VAR_DIR . '/translations/application/';
    $translation_file = $application_path . '/i18n/translation.ts';
    $packages_path = LIMB_VAR_DIR . '/translations/packages/';
    $package1_path = $packages_path . 'package1/';
    $package1_tr_file_path = $package1_path . '/i18n/translation.ts';

    $application_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/section1</name>
<message>
    <source>value1</source>
    <translation>value1</translation>
</message>
</context>
<context>
<name>/foo/section2</name>
<message>
    <source>value2</source>
    <translation>value2</translation>
</message>
</context>
<context>
<name>/bar/section1</name>
<message>
    <source>value2</source>
    <translation>value2</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translation_file, $application_tr_file);

    $package1_tr_file = <<< EOD
<!DOCTYPE TS><TS>
<context>
<name>/foo/section1</name>
<message>
    <source>value1</source>
    <translation>value1</translation>
</message>
</context>
<context>
<name>/foo/section2</name>
<message>
    <source>value2</source>
    <translation>value2</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($package1_tr_file_path, $package1_tr_file);

    $packages_paths = array($package1_path, $no_such_packages = $packages_path . 'package2/');

    $response = new MockCliResponse();
    $application_update = new lmbI18NSyncUtility($prefix = '/i18n/', $response);
    $response->expectCallCount('write', 1);
    $application_update->updatePackageDictionaries($application_path, $packages_paths);

    $qt_manager = new lmbQtXmlDictionaryManager();

    $dictionary = new lmbDictionary();
    $qt_manager->loadFromFile($dictionary, $package1_tr_file_path);
    $this->assertTrue($dictionary->hasEntry('/foo/section1', 'value1'));
    $this->assertTrue($dictionary->hasEntry('/foo/section2', 'value2'));
    $this->assertFalse($dictionary->hasEntry('/bar/section1', 'value2'));
  }
}
?>

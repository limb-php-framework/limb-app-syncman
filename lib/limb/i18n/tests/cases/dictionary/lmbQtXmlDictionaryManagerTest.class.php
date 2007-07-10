<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbQtXmlDictionaryManagerTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/dictionary/lmbQtXmlDictionaryManager.class.php');

class lmbQtXmlDictionaryManagerTest extends UnitTestCase
{
  function testLoadXML()
  {
    $d = new lmbDictionary();
    $mgr = new lmbQtXmlDictionaryManager();

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<name>/</name>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Hi</source>
    <translation>Привет</translation>
</message>
</context>
<context>
<name>/Zoo</name>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;

    $mgr->loadXML($d, $xml);

    $this->assertEqual($d->translate('/', 'Hello'), 'Привет');
    $this->assertEqual($d->translate('/', 'Hi'), 'Привет');
    $this->assertEqual($d->translate('/Zoo', 'Dog'), 'Собака');
  }

  function testLoadFromFile()
  {
    $d = new lmbDictionary();
    $mgr = new lmbQtXmlDictionaryManager();

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<name>/</name>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Hi</source>
    <translation>Привет</translation>
</message>
</context>
<context>
<name>/Zoo</name>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;

    file_put_contents($file = LIMB_VAR_DIR . '/dictionary.xml', $xml);

    $mgr->loadFromFile($d, $file);

    $this->assertEqual($d->translate('/', 'Hello'), 'Привет');
    $this->assertEqual($d->translate('/', 'Hi'), 'Привет');
    $this->assertEqual($d->translate('/Zoo', 'Dog'), 'Собака');

    unlink($file);
  }

  function testFullCycle()
  {
    $old_dict = new lmbDictionary();
    $new_dict = new lmbDictionary();

    $mgr = new lmbQtXmlDictionaryManager();

    $old_dict->addEntry('/foo', 'Foo');
    $old_dict->addEntry('/bar', 'Bar', 'Бар');

    $mgr->saveToFile($old_dict, $file = LIMB_VAR_DIR . '/dictionary.xml');

    $mgr->loadFromFile($new_dict, $file);

    $this->assertEqual($old_dict, $new_dict);
  }

  function testUnfinishedTranslations()
  {
    $d = new lmbDictionary();
    $mgr = new lmbQtXmlDictionaryManager();

    $d->addEntry('/foo', 'Foo');
    $d->addEntry('/bar', 'Bar', 'Бар');

    $dom = $mgr->getDOMDocument($d);
    $translations = $dom->getElementsByTagName('translation');

    $this->assertEqual($translations->item(0)->getAttribute('type'), 'unfinished');
    $this->assertFalse($translations->item(1)->hasAttribute('type'));
  }
}

?>
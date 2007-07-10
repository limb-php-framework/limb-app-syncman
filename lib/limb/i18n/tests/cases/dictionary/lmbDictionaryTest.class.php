<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDictionaryTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');

class lmbDictionaryTest extends UnitTestCase
{
  function testIsEmpty()
  {
    $d = new lmbDictionary();
    $this->assertTrue($d->isEmpty());
    $d->addEntry('/', 'Hello', 'Привет');
    $this->assertFalse($d->isEmpty());
  }

  function testTranslateFailed()
  {
    $d = new lmbDictionary();
    $this->assertFalse($d->hasEntry('/', 'Hello'));
    $this->assertEqual($d->translate('/', 'Hello'), 'Hello');
  }

  function testAddTranslation()
  {
    $d = new lmbDictionary();
    $d->addEntry('/', 'Hello', 'Привет');
    $this->assertTrue($d->hasEntry('/', 'Hello'));
    $this->assertEqual($d->translate('/', 'Hello'), 'Привет');
  }

  function testSetDictionary()
  {
    $d = new lmbDictionary();
    $d->setTranslations(array('/' => array('Hello' => 'Привет')));
    $this->assertEqual($d->translate('/', 'Hello'), 'Привет');
  }

  function testGetSectionTranslations()
  {
    $d = new lmbDictionary();
    $d->setTranslations(array('/foo' => array('Hello' => 'Привет'),
                              '/foo/section' => array('a1' => '', 'a2' => ''),
                              '/bar' => array('Dog' => 'Собака')));
    $this->assertEqual($d->getSectionTranslations('/foo'), array('/foo' => array('Hello' => 'Привет'),
                                                                 '/foo/section' => array('a1' => '', 'a2' => '')));
  }

  function testMergeAppend()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/foo', 'Hello', 'Привет');

    $d2 = new lmbDictionary();
    $d2->addEntry('/bar', 'Test', 'Тест');

    $d3 = $d1->merge($d2);

    $this->assertEqual($d3->translate('/foo', 'Hello'), 'Привет');
    $this->assertEqual($d3->translate('/bar', 'Test'), 'Тест');
  }

  function testMergeReplace()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/', 'Hello', 'Привет');

    $d2 = new lmbDictionary();
    $d2->addEntry('/', 'Hello', 'Привет снова');

    $d3 = $d1->merge($d2);

    $this->assertEqual($d3->translate('/', 'Hello'), 'Привет снова');
  }

  function testMergeOneContext()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/', 'Hello1', 'Привет');

    $d2 = new lmbDictionary();
    $d2->addEntry('/', 'Hello2', 'Привет снова');

    $d3 = $d1->merge($d2);

    $this->assertEqual($d3->translate('/', 'Hello1'), 'Привет');
    $this->assertEqual($d3->translate('/', 'Hello2'), 'Привет снова');
  }

  function testMergeSome()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/foo', 'Hello1');

    $d2 = new lmbDictionary();
    $d2->addEntry('/foo', 'Hello2');
    $d2->addEntry('/bar', 'Hello3');

    $d3 = $d1->mergeForExistingSections($d2);

    $this->assertTrue($d3->hasEntry('/foo', 'Hello1'));
    $this->assertTrue($d3->hasEntry('/foo', 'Hello2'));
    $this->assertFalse($d3->hasEntry('/bar', 'Hello3'));
  }

  function testMergeFromUntranlatedToTranslated()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/foo', 'Hello1');

    $d2 = new lmbDictionary();
    $d2->addEntry('/foo', 'Hello1', 'Translation');

    $d3 = $d1->mergeForExistingSections($d2);

    $this->assertTrue($d3->hasEntry('/foo', 'Hello1'));
    $this->assertTrue($d3->isTranslated('/foo', 'Hello1'));
    $this->assertTrue($d3->translate('/foo', 'Hello1'), 'Translation');
  }

  function testIsTranslated()
  {
    $d = new lmbDictionary();
    $d->addEntry('/', 'Hello', 'Привет');
    $d->addEntry('/', 'Test');

    $this->assertTrue($d->isTranslated('/', 'Hello'));
    $this->assertFalse($d->isTranslated('/', 'Test'));

    $this->assertEqual($d->translate('/', 'Test'), 'Test');
  }

  function testIsInSync()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/', 'Hello', 'Привет');
    $d1->addEntry('/', 'Test');

    $d2 = new lmbDictionary();
    $d2->addEntry('/', 'Test');
    $d2->addEntry('/', 'Hello');

    $this->assertTrue($d1->isInSync($d2));
  }

  function testIsNotInSync()
  {
    $d1 = new lmbDictionary();
    $d1->addEntry('/', 'Foo', 'Foo');
    $d1->addEntry('/', 'Test');

    $d2 = new lmbDictionary();
    $d2->addEntry('/', 'Test');
    $d2->addEntry('/', 'Bar');

    $this->assertFalse($d1->isInSync($d2));
  }
}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTranslationDictionaryLoadingFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/web_app/src/filter/lmbTranslationDictionaryLoadingFilter.class.php');

Mock :: generate('lmbFilterChain', 'MockFilterChain');

class lmbTranslationDictionaryLoadingFilterTest extends UnitTestCase
{
  function testToolkitGotAllDictionaries()
  {
    $dictionary1 = new lmbDictionary();
    $dictionary1->addEntry('/foo/bar', 'Dog', 'Собака');

    $dictionary2 = new lmbDictionary();
    $dictionary2->addEntry('/foo/bar', 'Dog', 'Dog');

    $dir = dirname(__FILE__) . '/stuff/i18n/';
    $filter = new lmbTranslationDictionaryLoadingFilter($dir);

    $chain = new MockFilterChain();
    $chain->expectOnce('next');

    $toolkit = lmbToolkit :: instance();

    $filter->run($chain);

    $this->assertEqual($toolkit->getDictionary('ru'), $dictionary1);
    $this->assertEqual($toolkit->getDictionary('en'), $dictionary2);
  }
}

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NStringFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');

class lmbI18NStringFilterTest extends lmbWactTestCase
{
  function testDefault()
  {
    $dictionary = new lmbDictionary();
    $dictionary->addEntry('/foo/bar', 'Apply filter', 'Применить фильтр');

    $this->toolkit->setDictionary('ru', $dictionary);

    $template = '{$"Apply filter"|i18n:"/foo/bar", "ru"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_default.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_default.html');

    $this->assertEqual($page->capture(), 'Применить фильтр');
  }

  function testUseCurrentLocale()
  {
    $dictionary = new lmbDictionary();
    $dictionary->addEntry('/foo/bar', 'Apply filter', 'Применить фильтр');

    $this->toolkit->setDictionary('ru', $dictionary);
    $this->toolkit->setLocale($this->toolkit->createLocale('ru'));

    $template = '{$"Apply filter"|i18n:"/foo/bar"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_locale.html');

    $this->assertEqual($page->capture(), 'Применить фильтр');
  }

  function testWithAttributes()
  {
    $dictionary = new lmbDictionary();
    $dictionary->addEntry('/foo/bar', 'Apply %1 filter and %2', 'Применить фильтр %1 и %2');

    $this->toolkit->setDictionary('ru', $dictionary);

    $template = '{$"Apply %1 filter and %2"|i18n:"/foo/bar", "ru", "%1", "1", "%2", "2"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_with_attributes.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_with_attributes.html');

    $this->assertEqual($page->capture(), 'Применить фильтр 1 и 2');
  }

  function testDefaultDBE() // DataBindingExpression
  {
    $dictionary = new lmbDictionary();
    $dictionary->addEntry('/foo/bar', 'Apply filter', 'Применить фильтр');

    $this->toolkit->setDictionary('ru', $dictionary);

    $template = '{$var|i18n:"/foo/bar", "ru"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_dbe.html');

    $page->set('var', 'Apply filter');

    $this->assertEqual($page->capture(), 'Применить фильтр');
  }


  function testDBEUseCurrentLocale() // DataBindingExpression
  {
    $dictionary = new lmbDictionary();
    $dictionary->addEntry('/foo/bar', 'Apply filter', 'Применить фильтр');

    $this->toolkit->setDictionary('ru', $dictionary);
    $this->toolkit->setLocale($this->toolkit->createLocale('ru'));

    $template = '{$var|i18n:"/foo/bar"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_dbe.html');

    $page->set('var', 'Apply filter');

    $this->assertEqual($page->capture(), 'Применить фильтр');
  }

  function testDefaultDBEForAttribute()
  {
    $dictionary = new lmbDictionary();
    $dictionary->addEntry('/foo/bar', 'Apply filter', 'Apply Filter');

    $this->toolkit->setDictionary('en', $dictionary);

    $template = '<form id="test_form" name="test_form" runat="server">'.
                '<input id="test_input" type="text" value="{$^var|i18n:"/foo/bar", "en"|uppercase}">' .
                '</form>';

    $this->registerTestingTemplate('/limb/locale_string_filter_dbe_for_attribute.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_dbe_for_attribute.html');

    $page->set('var', 'Apply filter');

    $expected = '<form id="test_form" name="test_form">'. //please note the second value attribute!
                '<input id="test_input" type="text" value="APPLY FILTER">' .
                '</form>';

    $this->assertEqual($page->capture(), $expected);
  }
}
?>

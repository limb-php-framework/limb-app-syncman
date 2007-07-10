<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleSpecTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/lmbLocaleSpec.class.php');

class lmbLocaleSpecTest extends UnitTestCase
{
  function testParseOnlyLanguage()
  {
    $spec = new lmbLocaleSpec('ru');

    $this->assertEqual($spec->getLocaleString(), 'ru');
    $this->assertEqual($spec->getLanguage(), 'ru');
    $this->assertFalse($spec->getCountry());
    $this->assertFalse($spec->getCountryVariation());
    $this->assertFalse($spec->getCharset());
    $this->assertEqual($spec->getLocale(), 'ru');
  }

  function testParseLanguageAndCountry()
  {
    $spec = new lmbLocaleSpec('ru-RU');

    $this->assertEqual($spec->getLocaleString(), 'ru-RU');
    $this->assertEqual($spec->getLanguage(), 'ru');
    $this->assertEqual($spec->getCountry(), 'RU');
    $this->assertFalse($spec->getCountryVariation());
    $this->assertFalse($spec->getCharset());
    $this->assertEqual($spec->getLocale(), 'ru-RU');
  }

  function testParseLanguageAndCountryAndVariation()
  {
    $spec = new lmbLocaleSpec('eng-GB@euro');

    $this->assertEqual($spec->getLocaleString(), 'eng-GB@euro');
    $this->assertEqual($spec->getLanguage(), 'eng');
    $this->assertEqual($spec->getCountry(), 'GB');
    $this->assertEqual($spec->getCountryVariation(), 'euro');
    $this->assertFalse($spec->getCharset());
    $this->assertEqual($spec->getLocale(), 'eng-GB');
  }

  function testParseLanguageAndCountryAndVariationAndCharset()
  {
    $spec = new lmbLocaleSpec('eng-GB.utf8@euro');

    $this->assertEqual($spec->getLocaleString(), 'eng-GB.utf8@euro');
    $this->assertEqual($spec->getLanguage(), 'eng');
    $this->assertEqual($spec->getCountry(), 'GB');
    $this->assertEqual($spec->getCountryVariation(), 'euro');
    $this->assertEqual($spec->getCharset(), 'utf8');
    $this->assertEqual($spec->getLocale(), 'eng-GB');
  }
}

?>

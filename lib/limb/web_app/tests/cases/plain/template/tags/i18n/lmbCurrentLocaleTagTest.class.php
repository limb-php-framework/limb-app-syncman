<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCurrentLocaleTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

class lmbCurrentLocaleTagTest extends lmbWactTestCase
{
  function testUseEnglishLocale()
  {
    $this->toolkit->setLocale(lmbLocale :: create('en'));
    $template = '<limb:current_locale name="en">Some text</limb:current_locale>' .
                '<limb:current_locale name="ru">Other text</limb:current_locale>';

    $this->registerTestingTemplate('/limb/locale_default.html', $template);

    $page = $this->initTemplate('/limb/locale_default.html');

    $this->assertEqual($page->capture(), 'Some text');
  }

  function testUseRussianLocale() // Just to be sure
  {
    $this->toolkit->setLocale(lmbLocale :: create('ru'));
    $template = '<limb:current_locale name="ru">Some text</limb:current_locale>' .
                '<limb:current_locale name="en">Other text</limb:current_locale>';

    $this->registerTestingTemplate('/limb/locale_default_use_russian.html', $template);

    $page = $this->initTemplate('/limb/locale_default_use_russian.html');

    $this->assertEqual($page->capture(), 'Some text');
  }
}
?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleDateTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/datetime/lmbLocaleDate.class.php');

class lmbLocaleDateTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testCreateByLocaleString()
  {
    $locale = $this->toolkit->createLocale('en');

    $date = lmbLocaleDate :: createByLocaleString($locale, 'Thursday 20 January 2005', '%A %d %B %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testCreateByAnotherLocaleString()
  {
    $locale = $this->toolkit->createLocale('en');

    $date = lmbLocaleDate :: createByLocaleString($locale, 'Thu 20 Jan 2005', '%a %d %b %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testCreateByWrongStringThrowsException()
  {
    $locale = $this->toolkit->createLocale('en');

    try
    {
      $date = lmbLocaleDate :: createByLocaleString($locale, '02-29-2003', '%a %d %b %Y');
      $this->assertTrue(false);
    }
    catch(lmbException $e)
    {
    }
  }

  function testLocalizedDateStringToISODateString()
  {
    $this->toolkit->setLocale($this->toolkit->createLocale('ru'));
    $date_string = '24.10.2005';
    $this->assertEqual(lmbLocaleDate :: localizedDateStringToISODateString($date_string), '2005-10-24 00:00:00');
  }

  function testIsoDateStringToLocalizedDateString()
  {
    $iso_date_string = '2005-10-24 00:00:00';
    $this->toolkit->setLocale($this->toolkit->createLocale('ru'));
    $this->assertEqual(lmbLocaleDate :: isoDateStringToLocalizedDateString($iso_date_string), '24.10.2005');
  }
}
?>
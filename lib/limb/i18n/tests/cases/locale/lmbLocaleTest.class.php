<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/i18n/src/lmbLocaleSpec.class.php');
lmb_require('limb/i18n/src/lmbLocale.class.php');

class lmbLocaleTest extends UnitTestCase
{
  function testGetLocaleSpec()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');

    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getLocaleSpec(), $locale_spec);
  }

  function testGetMonthName()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getMonthName(0), 'January');
    $this->assertEqual($locale->getMonthName(11), 'December');
    $this->assertNull($locale->getMonthName(12));
  }

  function testGetDayName()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getDayName(0, $short = false), 'Monday');
    $this->assertEqual($locale->getDayName(0, $short = true), 'Mon');
    $this->assertEqual($locale->getDayName(6, $short = false), 'Sunday');
    $this->assertEqual($locale->getDayName(6, $short = true), 'Sun');
  }

  function testGetOtherOptions()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getCharset(), 'utf-8');
    $this->assertEqual($locale->getLanguageDirection(), 'ltr');
  }

  function testGetCountryOptions()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getCountryName(), 'USA');
    $this->assertEqual($locale->getCountryComment(), '');
  }

  function testGetLanguageOptions()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getLanguageName(), 'English (American)');
    $this->assertEqual($locale->getIntlLanguageName(), 'English (American)');
  }

  function testGetCurrencyOptions()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getCurrencySymbol(), '$');
    $this->assertEqual($locale->getCurrencyName(), 'US Dollar');
    $this->assertEqual($locale->getCurrencyShortName(), 'USD');
  }

  function testGetDateTimeFormatOptions()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertEqual($locale->getTimeFormat(), '%H:%M:%S %p');
    $this->assertEqual($locale->getShortTimeFormat(), '%H:%M %p');
    $this->assertEqual($locale->getDateFormat(), '%A %d %B %Y');
    $this->assertEqual($locale->getShortDateFormat(), '%m/%d/%Y');
    $this->assertEqual($locale->getShortDateTimeFormat(), '%m/%d/%Y %H:%M:%S');
    $this->assertEqual($locale->getDateTimeFormat(), '%A %d %B %Y %H:%M:%S');
  }

  function testGetWeekDaysOptions()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $ini = new lmbIni(dirname(__FILE__) . '/en.ini');
    $locale = new lmbLocale($ini, $locale_spec);

    $this->assertFalse($locale->isMondayFirst());
    $this->assertEqual($locale->getWeekDays(), array(0, 1, 2, 3, 4, 5, 6));
    $this->assertEqual($locale->getMonths(), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11));
    $this->assertEqual($locale->getWeekDayNames(), array('Monday',
                                                         'Tuesday',
                                                         'Wednesday',
                                                         'Thursday',
                                                         'Friday',
                                                         'Saturday',
                                                         'Sunday'));

    $this->assertEqual($locale->getWeekDayNames($short = true), array('Mon',
                                                                      'Tue',
                                                                      'Wed',
                                                                      'Thu',
                                                                      'Fri',
                                                                      'Sat',
                                                                      'Sun'));

    $this->assertEqual($locale->getMonthNames(), array('January',
                                                       'February',
                                                       'March',
                                                       'April',
                                                       'May',
                                                       'June',
                                                       'July',
                                                       'August',
                                                       'September',
                                                       'October',
                                                       'November',
                                                       'December'));

    $this->assertEqual($locale->getMonthNames($short = true), array('Jan',
                                                                    'Feb',
                                                                    'Mar',
                                                                    'Apr',
                                                                    'May',
                                                                    'Jun',
                                                                    'Jul',
                                                                    'Aug',
                                                                    'Sep',
                                                                    'Oct',
                                                                    'Nov',
                                                                    'Dec'));

    $this->assertEqual($locale->getMeridiemName(10), 'am');
    $this->assertEqual($locale->getMeridiemName(22), 'pm');
  }

  function testCreate()
  {
    $locale_spec = new lmbLocaleSpec('en');
    $locale = lmbLocale :: create('en', dirname(__FILE__));
    $this->assertEqual($locale, new lmbLocale(new lmbIni(dirname(__FILE__) . '/en.ini'),
                                           $locale_spec));
  }

  function testCreateWithVariation()
  {
    $locale = lmbLocale :: create('en@euro', dirname(__FILE__));
    $this->assertEqual($locale->getCurrencySymbol(), '¤');
    $this->assertEqual($locale->getCurrencyName(), 'Euro');
  }
}
?>
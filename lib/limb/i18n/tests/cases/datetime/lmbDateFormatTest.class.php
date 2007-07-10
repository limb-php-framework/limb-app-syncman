<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDateFormatTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/i18n/toolkit.inc.php');
lmb_require('limb/i18n/src/datetime/lmbDateFormat.class.php');

class lmbDateFormatTest extends UnitTestCase
{
   /**
   *  strftime().  Most strftime() attributes are supported.
   *  %a    abbreviated weekday name (Sun, Mon, Tue)
   *  %A    full weekday name (Sunday, Monday, Tuesday)
   *  %b    abbreviated month name (Jan, Feb, Mar)
   *  %B    full month name (January, February, March)
   *  %C    century number (the year divided by 100 and truncated to an integer, range 00 to 99)
   *  %d    day of month (range 00 to 31)
   *  %D    same as "%m/%d/%y"
   *  %e    day of month, single digit (range 0 to 31)
   *  %E    number of days since unspecified epoch
   *  %H    hour as decimal number (00 to 23)
   *  %I    hour as decimal number on 12-hour clock (01 to 12)
   *  %j    day of year (range 001 to 366)
   *  %m    month as decimal number (range 01 to 12)
   *  %M    minute as a decimal number (00 to 59)
   *  %n    newline character (\n)
   *  %O    dst-corrected timezone offset expressed as "+/-HH:MM"
   *  %o    raw timezone offset expressed as "+/-HH:MM"
   *  %p    either 'am' or 'pm' depending on the time
   *  %P    either 'AM' or 'PM' depending on the time
   *  %r    time in am/pm notation, same as "%I:%M:%S %p"
   *  %R    time in 24-hour notation, same as "%H:%M"
   *  %S    seconds as a decimal number (00 to 59)
   *  %t    tab character (\t)
   *  %w    weekday as decimal (0 = Sunday)
   *  %U    week number of current year, first sunday as first week
   *  %y    year as decimal (range 00 to 99)
   *  %Y    year as decimal including century (range 0000 to 9999)
   *  %%    literal '%'
   */

  function testFormat()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toString($date, '%C %d %D %e %E %H %I %j %m %M %n %R %S %U %y %Y %t %%');

    $this->assertEqual($string, "20 02 01/02/05 2 2453373 23 11 002 01 05 \n 23:05 03 53 05 2005 \t %");
  }

  function testLocalizedFormat()
  {
    $date = new lmbDate('2005-01-20 10:15:30');

    $toolkit = lmbToolkit :: instance();
    $locale = $toolkit->createLocale('en');
    $printer = new lmbDateFormat();

    $toStringed_date = $printer->toString($date, $locale->getDateFormat(), $locale);

    $expected = 'Thursday 20 January 2005';
    $this->assertEqual($toStringed_date, $expected);
  }

  function testToISO()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toISO($date);

    $this->assertEqual($string, '2005-01-02 23:05:03');
  }

  function testToDateISO()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toDateISO($date);

    $this->assertEqual($string, '2005-01-02');
  }

  function testToTimeISO()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toTimeISO($date);

    $this->assertEqual($string, '23:05:03');
  }
}
?>

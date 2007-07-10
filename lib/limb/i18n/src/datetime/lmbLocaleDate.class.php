<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleDate.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/core/src/exception/lmbException.class.php');
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/i18n/src/datetime/lmbDateFormat.class.php');

class lmbLocaleDate extends lmbDate
{
  function createByLocaleString($locale, $string, $format)
  {
    $arr = self :: parseTimeString($locale, $string, $format);

    return new lmbDate($arr['hour'], $arr['minute'], $arr['second'], $arr['day'], $arr['month'], $arr['year']);
  }

  /**
  *  Tries to guess time values in time string $time_string formatted with $fmt
  *  Returns an array('hour','minute','second','month','day','year')
  *  At this moment only most common tags are supported.
  */
  static function parseTimeString($locale, $time_string, $fmt)
  {
    $hour = 0;
    $minute = 0;
    $second = 0;
    $month = 0;
    $day = 0;
    $year = 0;

    if(!($time_array = self :: explodeTimeStringByFormat($time_string, $fmt)))
      return -1;

    foreach($time_array as $time_char => $value)
    {
      switch($time_char)
      {
        case '%p':
        case '%P':
          if(strtolower($value) == $locale->getPmName())
            $hour += 12;
        break;

        case '%I':
        case '%H':
          $hour = (int)$value;
        break;

        case '%M':
          $minute = (int)$value;
        break;

        case '%S':
          $second = (int)$value;
        break;

        case '%m':
          $month = (int)$value;
        break;

        case '%b':
        case '%h':
          if(($index = array_search($value, $locale->getMonthNames(true))) !== false)
          {
            if($index !== false)
              $month = $index + 1;
          }
        break;

        case '%B':
          if(($index = array_search($value, $locale->getMonthNames())) !== false)
          {
            if($index !== false)
              $month = $index + 1;
          }
        break;

        case '%d':
          $day = (int)$value;
        break;

        case '%Y':
          $year = (int)$value;
        break;
        case '%y':
          if($value < 40)
            $year = 2000 + $value;
          else
            $year = 1900 + $value;
        break;

        case '%T':
          if ($regs = explode(':', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
            $second = (int)$regs[3];
          }
        break;

        case '%D':
          if ($regs = explode('/', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
            $second = (int)$regs[3];
          }
        break;

        case '%R':
          if ($regs = explode(':', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
          }
        break;
      }
    }

    return array('hour' => $hour, 'minute' => $minute, 'second' => $second, 'month' => $month, 'day' => $day, 'year' => $year);
  }

  static function explodeTimeStringByFormat($time_string, $fmt)
  {
    $fmt_len = strlen($fmt);
    $time_string_len = strlen($time_string);

    $time_array = array();

    $fmt_pos = 0;
    $time_string_pos = 0;

    while(($fmt_pos = strpos($fmt, '%', $fmt_pos)) !== false)
    {
      $current_time_char = $fmt{++$fmt_pos};

      if(($fmt_pos+1) >= $fmt_len)
        $delimiter_pos = $time_string_len;
      elseif($time_string_pos <= $time_string_len)
      {
        $current_delimiter = $fmt{++$fmt_pos};
        $delimiter_pos = strpos($time_string, $current_delimiter, $time_string_pos);
        if($delimiter_pos === false)
          $delimiter_pos = $time_string_len;
      }

      $delimiter_len = $delimiter_pos - $time_string_pos;

      $value = substr($time_string, $time_string_pos, $delimiter_len);

      if(preg_match("/[-\/]/", $value))
        throw new lmbException("Wrong date format: $time_string does not matches $fmt format");

      $time_array['%' . $current_time_char] = $value;

      $time_string_pos += ($delimiter_len + 1);
    }

    return $time_array;
  }

  static function localizedDateStringToISODateString($localized_date)
  {
    $date = self :: localizedDateStringToDate($localized_date);
    return $date->toString();
  }

  static function isoDateStringToLocalizedDateString($iso_date)
  {
    $date = new lmbDate($iso_date);
    $locale = lmbToolkit :: instance()->getLocale();
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateFormat());
  }

  static function timestampToLocalizedDateString($stamp)
  {
    $date = new lmbDate((int)$stamp);
    $locale = lmbToolkit :: instance()->getLocale();
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateFormat());
  }

  static function localizedDateStringToTimestamp($localized_date)
  {
    $date = self :: localizedDateStringToDate($localized_date);
    return $date->toTimestamp();
  }

  static function localizedDateStringToDate($localized_date)
  {
    $locale = lmbToolkit :: instance()->getLocale();
    return lmbLocaleDate :: createByLocaleString($locale,
                                                 $localized_date,
                                                 $locale->getShortDateFormat());
  }

  static function getLocalizedCurrentDateString()
  {
    $date = new lmbDate();
    $locale = lmbToolkit :: instance()->getLocale();
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateFormat());
  }

  static function timestampToLocalizedDateTimeString($stamp)
  {
    $date = new lmbDate((int)$stamp);
    $locale = lmbToolkit :: instance()->getLocale();
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateTimeFormat());
  }

  static function localizedDateTimeStringToTimestamp($localized_date)
  {
    $date = self :: localizedDateTimeStringToDate($localized_date);
    return $date->toTimestamp();
  }

  static function localizedDateTimeStringToDate($localized_date)
  {
    $locale = lmbToolkit :: instance()->getLocale();
    return lmbLocaleDate :: createByLocaleString($locale,
                                                 $localized_date,
                                                 $locale->getShortDateTimeFormat());
  }
}

?>

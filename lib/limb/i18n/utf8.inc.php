<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: utf8.inc.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/charset/lmbUTF8BaseDriver.class.php');
lmb_require('limb/i18n/src/charset/lmbUTF8MbstringDriver.class.php');
lmb_require('limb/i18n/src/charset/strings.inc.php');

if (!defined('LIMB_UTF8_IGNORE_MBSTRING') && function_exists('mb_strlen'))
{
  include_once('limb/i18n/src/charset/lmbUTF8MbstringDriver.class.php');
  installStringsDriver(new lmbUTF8MbstringDriver());
}
else
{
  include_once('limb/i18n/src/charset/lmbUTF8BaseDriver.class.php');
  installStringsDriver(new lmbUTF8BaseDriver());
}

function utf8_to_win1251($str)
{
   static $conv = '';
   if(!is_array($conv))
   {
     $conv = array();
     for($x = 128; $x <= 143; $x++)
     {
       $conv['utf'][] = chr(209) . chr($x);
       $conv['win'][] = chr($x + 112);
     }

     for($x = 144; $x <= 191; $x++)
     {
       $conv['utf'][] = chr(208) . chr($x);
       $conv['win'][] = chr($x + 48);
     }

     $conv['utf'][] = chr(208) . chr(129);
     $conv['win'][] = chr(168);
     $conv['utf'][] = chr(209) . chr(145);
     $conv['win'][] = chr(184);
   }

   return str_replace($conv['utf'], $conv['win'], $str);
}

function win1251_to_utf8($s) {
    $c209 = chr(209);
    $c208 = chr(208);
    $c129 = chr(129);
    $t = '';
    for($i = 0; $i < strlen($s); $i++) {
      $c = ord($s[$i]);
      if ($c >= 192 && $c <= 239)
          $t .= $c208 . chr($c-48);
      elseif ($c > 239)
          $t .= $c209 . chr($c-112);
      elseif ($c == 184)
          $t .= $c209 . $c209;
      elseif ($c == 168)
          $t .= $c208 . $c129;
      else
          $t .= $s[$i];
    }
    return $t;
}

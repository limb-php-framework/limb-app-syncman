<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: strings.inc.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/charset/lmbSingleByteStringsDriver.class.php');

function installStringsDriver($driver) {
    $prev_driver = getStringsDriver();
    $GLOBALS['STRINGS_DRIVER'] = &$driver;
    return $prev_driver;
}

function getStringsDriver() {
    if (isset($GLOBALS['STRINGS_DRIVER'])) {
        return $GLOBALS['STRINGS_DRIVER'];
    }
}

if (getStringsDriver() === null) {
    include_once(dirname(__FILE__) . '/lmbSingleByteStringsDriver.class.php');
    installStringsDriver(new lmbSingleByteStringsDriver());
}

/**
* multibyte aware replacement for strlen()
*/
function _strlen($string) {
    return $GLOBALS['STRINGS_DRIVER']->_strlen($string);
}

/**
* multibyte aware replacement for substr()
*/
function _substr($str, $start, $length=null) {
    return $GLOBALS['STRINGS_DRIVER']->_substr($str, $start, $length);
}

/**
* multibyte aware replacement for strrepalce()
*/
function _str_replace($s, $r, $str) {
    return $GLOBALS['STRINGS_DRIVER']->_str_replace($s, $r, $str);
}

/**
* multibyte aware replacement for ltrim()
*/
function _ltrim($str, $charlist = '') {
    return $GLOBALS['STRINGS_DRIVER']->_ltrim($str, $charlist);
}

/**
* multibyte aware replacement for ltrim()
*/
function _rtrim($str, $charlist = '') {
    return $GLOBALS['STRINGS_DRIVER']->_rtrim($str, $charlist);
}

/**
* multibyte aware replacement for trim()
*/
function _trim($str, $charlist = '') {
    if($charlist == '')
      return $GLOBALS['STRINGS_DRIVER']->_trim($str);
    else
      return $GLOBALS['STRINGS_DRIVER']->_trim($str, $charlist);
}

/**
* This is a unicode aware replacement for strtolower()
*/
function _strtolower($string) {
    return $GLOBALS['STRINGS_DRIVER']->_strtolower($string);
}

/**
* This is a unicode aware replacement for strtoupper()
*/
function _strtoupper($string) {
    return $GLOBALS['STRINGS_DRIVER']->_strtoupper($string);
}

/**
* Multibyte aware replacement for strpos
*/
function _strpos($haystack, $needle, $offset=null) {
    return $GLOBALS['STRINGS_DRIVER']->_strpos($haystack, $needle, $offset);
}

/**
* Multibyte aware replacement for strrpos
*/
function _strrpos($haystack, $needle, $offset=null) {
    return $GLOBALS['STRINGS_DRIVER']->_strrpos($haystack, $needle, $offset);
}

/**
* Multibyte aware replacement for ucfirst
*/
function _ucfirst($str) {
    return $GLOBALS['STRINGS_DRIVER']->_ucfirst($str);
}

/*
* Multibyte aware replacement for strcasecmp
*/
function _strcasecmp($strX, $strY) {
    return $GLOBALS['STRINGS_DRIVER']->_strcasecmp($strX, $strY);
}

/*
* Multibyte aware replacement for substr_count
*/
function _substr_count($haystack, $needle) {
    return $GLOBALS['STRINGS_DRIVER']->_substr_count($haystack, $needle);
}

/*
* Multibyte aware replacement for str_split
*/
function _str_split($str, $split_len=1) {
    return $GLOBALS['STRINGS_DRIVER']->_str_split($strX, $strY);
}

/*
* This is multibyte aware alternative to preg_match
*/
function _preg_match($pattern, $subject, &$matches, $flags=null, $offset=null) {
    return $GLOBALS['STRINGS_DRIVER']->_preg_match($pattern, $subject, $matches, $flags, $offset);
}

/*
* This is multibyte aware alternative to preg_match_all
*/
function _preg_match_all($pattern, $subject, &$matches, $flags=null, $offset=null) {
    return $GLOBALS['STRINGS_DRIVER']->_preg_match_all($pattern, $subject, $matches, $flags, $offset);
}

/*
* This is multibyte aware alternative to preg_replace
*/
function _preg_replace($pattern, $replacement, $subject, $limit=null) {
    return $GLOBALS['STRINGS_DRIVER']->_preg_replace($pattern, $replacement, $subject, $limit);
}

/*
* This is multibyte aware alternative to preg_replace_callback
*/
function _preg_replace_callback($pattern, $callback, $subject, $limit=null) {
    return $GLOBALS['STRINGS_DRIVER']->_preg_replace_callback($pattern, $callback, $subject, $limit);
}

/*
* This is multibyte aware alternative to preg_split
*/
function _preg_split($pattern, $subject, $limit=null, $flags=null) {
    return $GLOBALS['STRINGS_DRIVER']->_preg_split($pattern, $subject, $limit, $flags);
}

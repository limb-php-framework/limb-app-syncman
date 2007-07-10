<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSingleByteStringsDriverTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/charset/lmbSingleByteStringsDriver.class.php');

class lmbSingleByteStringsDriverTest extends UnitTestCase
{
  function test_substr() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_substr("just a test", 1), "ust a test");
      $this->assertEqual($driver->_substr("hello", 0, 400), "hello");
      $this->assertEqual($driver->_substr("foobar", 1, 4), "ooba");
      $this->assertEqual($driver->_substr("foo", -1), "o");
      $this->assertEqual($driver->_substr("foo", 0, -1), "fo");
      $this->assertEqual($driver->_substr("foo", 1, -1), "o");
  }

  function test_rtrim() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_rtrim("foo\n\n\t"), "foo");
      $this->assertEqual($driver->_rtrim("bar?++.*?", ".*?+"), "bar");
  }

  function test_ltrim() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_ltrim("\n\n\tfoo"), "foo");
      $this->assertEqual($driver->_ltrim("?+.*+?baz", "?.*+"), "baz");
  }

  function test_trim() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_trim(" \n\t\0 foo\0\n\n\t"), "foo");
      $this->assertEqual($driver->_trim("pbazp", "p"), "baz");
      $this->assertEqual($driver->_trim("?*++?bar?+.+?", "?.+*"), "bar");
  }

  function test_str_replace() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_str_replace("aaa", "", "fooaaabar"),
                         "foobar");
      $this->assertEqual($driver->_str_replace("a", "b", "foobaz"),
                         "foobbz");
      $search = array("v", "x");
      $this->assertEqual($driver->_str_replace($search, "d", "vxdddxv"),
                         "ddddddd");
      $replace = array("a", "w");
      $this->assertEqual($driver->_str_replace($search, $replace, "vfooxbar"),
                         "afoowbar");
  }

  function test_strlen() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_strlen("foo"), 3);
      $this->assertEqual($driver->_strlen("\nfoo bar "), 9);
  }

  function test_strpos() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_strpos("foo", "f"), 0);
      $this->assertEqual($driver->_strpos("foo", "o"), 1);
      $this->assertEqual($driver->_strpos("foo", "o", 2), 2);
  }

  function test_strrpos() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_strrpos("foo", "o"), 2);
      $this->assertEqual($driver->_strrpos("foo", "o", 2), 2);
  }

  function test_strtolower() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_strtolower("TEST"), "test");
      $this->assertEqual($driver->_strtolower("tEsT"), "test");
  }

  function test_strtoupper() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_strtoupper("test"), "TEST");
      $this->assertEqual($driver->_strtoupper("tEsT"), "TEST");
  }

  function test_ucfirst() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_ucfirst("test"), "Test");
  }

  function test_strcasecmp() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_strcasecmp("test", "test"), 0);
      $this->assertEqual($driver->_strcasecmp("test", "TesT"), 0);
      $this->assertTrue($driver->_strcasecmp("test", "TESTS") < 0);
      $this->assertTrue($driver->_strcasecmp("tests", "TEST") > 0);
  }

  function test_substr_count() {
      $driver = new lmbSingleByteStringsDriver();

      $str = "This is a test";

      $this->assertEqual($driver->_substr_count($str, "is"), 2);
  }

  function test_str_split() {
      if(phpversion() < 5)
          return;

      $driver = new lmbSingleByteStringsDriver();

      $str = 'Internationalization';
      $array = array(
          'I','n','t','e','r','n','a','t','i','o','n','a','l','i',
          'z','a','t','i','o','n',
      );
      $this->assertEqual($driver->_str_split($str), $array);
  }

  function test_preg_match() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertTrue($driver->_preg_match("/^(.)/", "test", $matches));
      $this->assertEqual($matches[1], "t");
  }

  function test_preg_match_all() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertTrue($driver->_preg_match_all("/(.)/", "test", $matches));

      $this->assertEqual($matches[1][0], "t");
      $this->assertEqual($matches[1][1], "e");
      $this->assertEqual($matches[1][2], "s");
      $this->assertEqual($matches[1][3], "t");
  }

  function test_preg_replace() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_preg_replace("/cat./", "dogs", "cats"), "dogs");
  }

  function test_preg_replace_callback() {
      $driver = new lmbSingleByteStringsDriver();

      $this->assertEqual($driver->_preg_replace_callback("/(cat)(.)/",
                                                         create_function('$m','return "dog".$m[2];'),
                                                         "cats"), "dogs");
  }

  function test_preg_split() {
      $driver = new lmbSingleByteStringsDriver();

      $pieces = $driver->_preg_split("/an./", "foo and bar");
      $this->assertEqual($pieces[0], "foo ");
      $this->assertEqual($pieces[1], " bar");
  }
}

?>
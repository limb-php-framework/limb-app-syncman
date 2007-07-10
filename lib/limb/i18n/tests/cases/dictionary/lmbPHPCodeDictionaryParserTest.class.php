<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPHPCodeDictionaryParserTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/i18n/src/dictionary/lmbPHPCodeDictionaryParser.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');

class lmbPHPCodeDictionaryParserTest extends UnitTestCase
{
  function testLoadSimple()
  {
    $src = <<< EOD
<?php
tr("/", "Hello");
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/', 'Hello'));
  }

  function testLoadSeveral()
  {
    $src = <<< EOD
<?php
tr("/foo", "Hello");
tr("/bar", "Dog");
tr("/zzz", "Apple");
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/foo', 'Hello'));
    $this->assertTrue($t->hasEntry('/bar', 'Dog'));
    $this->assertTrue($t->hasEntry('/zzz', 'Apple'));
  }

  function testLoadWithLang()
  {
    $src = <<< EOD
<?php
tr("/", "Hello", "ru");
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/', 'Hello'));
  }

  function testLoadParametrizized()
  {
    $src = <<< EOD
<?php
tr("/", "Hello %1 %2", null, array('1' => 'foo', '2' => 'bar'));
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/', 'Hello %1 %2'));
  }

  function testLoadSkipVariables()
  {
    $src = <<< EOD
<?php
tr("/", \$a);
tr(\$b, \$a);
tr(\$b, 'Hello');
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertFalse($t->hasEntry('/', 'Hello'));
  }

  function testLoadSkipContacanated()
  {
    $src = <<< EOD
<?php
tr("/", "Hello" . \$a . "dog");
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->isEmpty());
  }

  function testLoadSkipFunctionDeclaration()
  {
    $src = <<< EOD
<?php
function tr(\$b = '\', \$a = 'Hello'){}
?>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbPHPCodeDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertFalse($t->hasEntry('/', 'Hello'));
  }

}

?>
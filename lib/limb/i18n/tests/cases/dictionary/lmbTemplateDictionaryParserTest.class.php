<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTemplateDictionaryParserTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/dictionary/lmbTemplateDictionaryParser.class.php');

class lmbTemplateDictionaryParserTest extends UnitTestCase
{
  function testLoadSimple()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n:"/"}
</html>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbTemplateDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/', 'Hello'));
  }

  function testLoadSeveral()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n:"/foo"}
{\$"Dog"|i18n:"/bar"}
{\$"Apple"|i18n:"/zzz"}
</html>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbTemplateDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/foo', 'Hello'));
    $this->assertTrue($t->hasEntry('/bar', 'Dog'));
    $this->assertTrue($t->hasEntry('/zzz', 'Apple'));
  }

  function testLoadSeveralFilters()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n:"/"|trim|hex}
</html>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbTemplateDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->hasEntry('/', 'Hello'));
  }

  function testI18NFilterMustBeFirst()
  {
    $src = <<< EOD
<html>
{\$hello|trim|i18n:"/foo"}
</html>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbTemplateDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->isEmpty());
  }

  function testSkipVariable()
  {
    $src = <<< EOD
<html>
{\$hello|i18n:"/foo"}
</html>
EOD;

    $t = new lmbDictionary();
    $loader = new lmbTemplateDictionaryParser();
    $loader->parse($src, $t, new lmbCliResponse());

    $this->assertTrue($t->isEmpty());
  }

}

?>
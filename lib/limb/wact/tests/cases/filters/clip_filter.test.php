<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: clip_filter.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactTemplateClipFilterTestCase extends WactTemplateTestCase
{
  function testSimpleClipVar()
  {
    $template = '{$val|clip:5}';

    $this->registerTestingTemplate('/template/filter/clipvar.html', $template);
    $page = $this->initTemplate('/template/filter/clipvar.html');
    $page->set('val', 'abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde');
  }

  function testSimpleClipLiteral()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:5}';

    $this->registerTestingTemplate('/template/filter/clipstr.html', $template);
    $page = $this->initTemplate('/template/filter/clipstr.html');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde');
  }

  function testSimpleClipVarStart()
  {
    $template = '{$val|clip:5,2}';

    $this->registerTestingTemplate('/template/filter/clipvarstart.html', $template);
    $page = $this->initTemplate('/template/filter/clipvarstart.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, 'cdefg');
  }

  function testSimpleClipLiteralStart()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:5,2}';

    $this->registerTestingTemplate('/template/filter/clipstrstart.html', $template);
    $page = $this->initTemplate('/template/filter/clipstrstart.html');

    $output = $page->capture();
    $this->assertEqual($output, 'cdefg');
  }

  function testSimpleClipVarSuffix()
  {
    $template = '{$val|clip:5,0,"..."} {$val|clip:12,0,"..."}';

    $this->registerTestingTemplate('/template/filter/clipvarsuf.html', $template);
    $page = $this->initTemplate('/template/filter/clipvarsuf.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde... abcdefgh');
  }

  function testSimpleClipLiteralSuffix()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:5,0,"..."} {$str|clip:12,0,"..."}';

    $this->registerTestingTemplate('/template/filter/clipstrsuf.html', $template);
    $page = $this->initTemplate('/template/filter/clipstrsuf.html');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde... abcdefgh');
  }

  function testSimpleClipLiteralLenVarSuffix()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:len,0,"..."} {$str|clip:len2,0,"..."}';

    $this->registerTestingTemplate('/template/filter/clipstrsuflenvar.html', $template);
    $page = $this->initTemplate('/template/filter/clipstrsuflenvar.html');
    $page->set('len', 5);
    $page->set('len2', 12);

    $output = $page->capture();
    $this->assertEqual($output, 'abcde... abcdefgh');
  }

  function testLongStringWordBoundary()
  {
    $template = '{$val|clip:35,0,"...","n"} {$val|clip:35,0,"...","y"}';

    $this->registerTestingTemplate('/template/filter/clipvarwordbound.html', $template);
    $page = $this->initTemplate('/template/filter/clipvarwordbound.html');
    $page->set('val','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In auctor sem vitae ante.');

    $output = $page->capture();
    $this->assertEqual($output, 'Lorem ipsum dolor sit amet, consect... Lorem ipsum dolor sit amet, consectetuer...');
  }

  function testLongLiteralStringWordBoundary()
  {
    $template = '<core:set str="Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In auctor sem vitae ante." />'
        .'{$str|clip:35,0,"...","No"} {$str|clip:35,0,"...","Yes"}';

    $this->registerTestingTemplate('/template/filter/clipstrwordbound.html', $template);
    $page = $this->initTemplate('/template/filter/clipstrwordbound.html');

    $output = $page->capture();
    $this->assertEqual($output, 'Lorem ipsum dolor sit amet, consect... Lorem ipsum dolor sit amet, consectetuer...');
  }

  function testOneAttributeDoubleQuoteVar()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,"..."}"/>';

    $this->registerTestingTemplate('/template/filter/testoneattributedoublequote.html', $template);

    try {
      $page = $this->initTemplate('/template/filter/testoneattributedoublequote.html');
      $this->assertTrue(false);
    }
    catch (WactException $e)
    {
      $this->assertWantedPattern('/Attribute syntax error/', $e->getMessage());
    }
  }

  function testOneAttributeSingleQuoteVar()
  {
    $template = '<img src=\'img.gif\' alt=\'{$val|clip:5,0,\'...\'}\'/>';

    $this->registerTestingTemplate('/template/filter/testoneattributesinglequote.html',$template);
    try
    {
      $page = $this->initTemplate('/template/filter/testoneattributesinglequote.html');
      $this->assertTrue(false);
    }
    catch (WactException $e)
    {
      $this->assertWantedPattern('/Attribute syntax error/', $e->getMessage());
    }
  }

  function testOneAttributeMixedQuote1Var()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,\'...\'}"/>';

    $this->registerTestingTemplate('/template/filter/testoneattributemixedquote1.html',$template);
    $page = $this->initTemplate('/template/filter/testoneattributemixedquote1.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt="abcde..." />');
  }

  function testOneAttributeMixedQuote2Var()
  {
    $template = '<img src=\'img.gif\' alt=\'{'.'$val|clip:5,0,"..."}\'/>';

    $this->registerTestingTemplate('/template/filter/testoneattributemixedquote2.html',$template);
    $page = $this->initTemplate('/template/filter/testoneattributemixedquote2.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt=\'abcde...\' />');
  }

  function testTwoAttributeDoubleQuoteVar()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,"..."}" title="{$val|clip:5,0,"..."}"/>';

    $this->registerTestingTemplate('/template/filter/testtwoattributedoublequote.html',$template);
    try
    {
      $page = $this->initTemplate('/template/filter/testtwoattributedoublequote.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Attribute syntax error/', $e->getMessage());
    }
  }

/*
preg_match hangs with this input cause by this test case:
string(53) "/^((?Us).*)\{\$(([^"'}]+|('|")(?U).*\4)+)\}((?s).*)$/"
string(74) "<img src="img.gif" alt="{$val|clip:5,0," ...'}' title="{$val|clip:5,0," />"

  function testTwoAttributeSingleQuoteVar() {
      $template = '<img src=\'img.gif\' alt=\'{$val|clip:5,0,\'...\'}\' title=\'{$val|clip:5,0,\'...\'}\' />';

      $this->registerTestingTemplate('/template/filter/testtwoattributesinglequote.html',$template);
      $page = $this->initTemplate('/template/filter/testtwoattributesinglequote.html');
      $page->set('val','abcdefgh');

      $output = $page->capture();
      $this->assertEqual($output, "<img src='img.gif' alt='abcde...' title='abcde...' />");
  }
*/
  function testTwoAttributeMixedQuote1Var()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,\'...\'}" title="{$val|clip:5,0,\'...\'}"/>';

    $this->registerTestingTemplate('/template/filter/testtwoattributemixedquote1.html',$template);
    $page = $this->initTemplate('/template/filter/testtwoattributemixedquote1.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt="abcde..." title="abcde..." />');
  }

  function testTwoAttributeMixedQuote2Var()
  {
    $template = '<img src=\'img.gif\' alt=\'{'.'$val|clip:5,0,"..."}\' title=\''.'{$val|clip:5,0,"..."}\'/>';

    $this->registerTestingTemplate('/template/filter/testtwoattributemixedquote2.html',$template);
    $page = $this->initTemplate('/template/filter/testtwoattributemixedquote2.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt=\'abcde...\' title=\'abcde...\' />');
  }
}
?>
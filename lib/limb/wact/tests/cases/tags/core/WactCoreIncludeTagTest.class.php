<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreIncludeTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactCoreIncludeTagTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();

    $template = 'Terminal';
    $this->registerTestingTemplate('/tags/core/include/terminal.html', $template, 'terminal.html');

    $template = 'Nested-<core:include file="terminal.html">-Nested';
    $this->registerTestingTemplate('/tags/core/include/nested.html', $template, 'nested.html');
  }

  function testInclude()
  {
    $page = $this->initTemplate('nested.html');
    $output = $page->capture();
    $this->assertEqual($output, 'Nested-Terminal-Nested');
  }

  function testIncludeMissingFile()
  {
    $template = '<core:INCLUDE file="/no/such/file/huh">';
    $this->registerTestingTemplate('/tags/core/include/missing_file.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/include/missing_file.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
        $this->assertWantedPattern('/Template source file not found/', $e->getMessage());
    }
  }

  function testMissingFileAttribute()
  {
    $template = 'Include-<core:include>-Include';
    $this->registerTestingTemplate('/tags/core/include/missing_file_attribute.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/include/missing_file_attribute.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
    }
  }

  function testEmptyFileAttribute()
  {
    $template = 'Include-<core:include file>-Include';
    $this->registerTestingTemplate('/tags/core/include/test_empty_file_attribute.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/include/test_empty_file_attribute.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
    }
  }

  function testNestedInclude()
  {
    $template = 'Include-<core:include file="nested.html">-Include';
    $this->registerTestingTemplate('/tags/core/include/nested_include.html', $template);

    $page = $this->initTemplate('/tags/core/include/nested_include.html');
    $output = $page->capture();
    $this->assertEqual($output, 'Include-Nested-Terminal-Nested-Include');
  }

  function testIncludeFileAttributeVariable()
  {
    $template = '<core:SET FILENAME="terminal.html"><core:include file="{$FILENAME}">';
    $this->registerTestingTemplate('/tags/core/include/file_attribute_variable.html', $template);

    $page = $this->initTemplate('/tags/core/include/file_attribute_variable.html');
    $output = $page->capture();
    $this->assertEqual($output, 'Terminal');
  }

  function testIncludedVariableReference()
  {
    $template = '{$Variable}';
    $this->registerTestingTemplate('/tags/core/include/varref.html', $template);

    $template = 'Include-<core:include file="/tags/core/include/varref.html">-Include';
    $this->registerTestingTemplate('/tags/core/include/includedvarref.html', $template);

    $page = $this->initTemplate('/tags/core/include/includedvarref.html');
    $page->set('Variable', 'Here');
    $output = $page->capture();
    $this->assertEqual($output, 'Include-Here-Include');
  }

  function testIncludeLiteral()
  {
    $literal_template = '{$Ref}<core:block>{$Ref}</core:block>';
    $this->registerTestingTemplate('/tags/core/include/literal.html', $literal_template);

    $template = '<core:include file="/tags/core/include/literal.html" literal>';
    $this->registerTestingTemplate('/tags/core/include/includeliteral.html', $template);

    $page = $this->initTemplate('/tags/core/include/includeliteral.html');
    $output = $page->capture();
    $this->assertEqual($output, $literal_template);
  }

  function testIncludedTemplateWithNonClosedTagGeneratesProperErrorException()
  {
    $include_template = '<div>';
    $this->registerTestingTemplate('/tags/core/include/included_template.html', $include_template);

    $template = '<a><core:include file="/tags/core/include/included_template.html"></a>';
    $this->registerTestingTemplate('/tags/core/include/main_template.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/include/main_template.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing close tag/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), '/tags/core/include/included_template.html');
      $this->assertEqual($e->getParam('line'), 1);
    }
  }
}
?>
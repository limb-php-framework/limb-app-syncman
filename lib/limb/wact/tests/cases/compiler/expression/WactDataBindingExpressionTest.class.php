<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDataBindingExpressionTest.class.php 5071 2007-02-16 09:09:35Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::Generate('WactCompileTreeNode', 'MockWactCompileTreeNode');
Mock::Generate('WactCompilerProperty', 'MockWactCompilerProperty');
Mock::Generate('WactCodeWriter', 'MockWactCodeWriter');

class WactCompilerPropertyDBETestVersion extends WactCompilerProperty
{
  function generateScopeEntry($code_writer)
  {
    $code_writer->writePHP('generateScopeEntryOutput');
  }

  function generatePreStatement($code_writer)
  {
    $code_writer->writePHP('generatePreStatementOutput');
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('generateExpressionOutput');
  }

  function generatePostStatement($code_writer)
  {
    $code_writer->writePHP('generatePostStatementOutput');
  }

  function generateScopeExit($code_writer)
  {
    $code_writer->writePHP('generateScopeExitOutput');
  }
}

class WactDataBindingExpressionTest extends UnitTestCase
{
  function testAnalizeSimpleExpression()
  {
    $context = new WactCompileTreeNode();

    $DBE = new WactDataBindingExpression('Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), 'Test');
  }

  function testAnalizeExpressionWithRootDatasourceSymbol()
  {
    $root = new WactCompileTreeRootNode();

    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpression('#Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), 'Test');
  }

  function testAnalizeExpressionWithParentDatasourceSymbol()
  {
    $parent = new WactCompileTreeRootNode();

    $context = new WactCompileTreeRootNode(); // use root node as a regular node.
    $context->parent = $parent;

    $DBE = new WactDataBindingExpression('^Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), 'Test');
  }

  function testThrowExceptionIfDatasourceContextIsNotFound()
  {
    $location = new WactSourceLocation('my_file', 10);
    $context = new WactCompileTreeNode($location);

    $DBE = new WactDataBindingExpression('^Test', $context);
    try
    {
      $DBE->prepare();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Expression datasource context not found/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testBadExpression()
  {
    $expression = '&$|%';

    $context = new WactCompileTreeNode(new WactSourceLocation('my_file', 10));

    $DBE = new WactDataBindingExpression($expression, $context);
    try
    {
      $DBE->prepare();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid data binding/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), $expression);
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testGetValueUnresolvedBinding()
  {
    $expression = 'Test';

    $context = new WactCompileTreeRootNode(new WactSourceLocation('my_file', 10));

    $DBE = new WactDataBindingExpression('Test', $context);
    $this->assertFalse($DBE->isConstant());
    try
    {
      $DBE->getValue('Test');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Cannot resolve data binding/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), $expression);
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testBingingIsAlwaysNotConstantForNonPropertyExpression()
  {
    $context = new WactCompileTreeNode();

    $DBE = new WactDataBindingExpression('Test', $context);

    $this->assertFalse($DBE->isConstant());
  }

  function testBindingIsConstantForConstantProperty()
  {
    $property = new WactConstantProperty('hello');

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpression('Test', $context);

    $this->assertTrue($DBE->isConstant());
  }

  function testBindingIsNotConstantForNonConstantProperty()
  {
    $property = new WactCompilerProperty();

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpression('Test', $context);

    $this->assertFalse($DBE->isConstant());
  }

  function testGetValueForConstantProperty()
  {
    $property = new WactConstantProperty('hello');

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpression('Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGenerateCicleForPropertyExpression()
  {
    $code_writer = new WactCodeWriter();
    $property = new WactCompilerPropertyDBETestVersion();

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpression('Test', $context);
    $DBE->generatePreStatement($code_writer);
    $DBE->generateExpression($code_writer);
    $DBE->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php generatePreStatementOutput'.
                                                 'generateExpressionOutput'.
                                                 'generatePostStatementOutput');
  }

  function testGenerateExpressionForRegularCompileTreeNode()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();

    $context = new MockWactCompileTreeNode();
    $context->setReturnReference('getDataSource', $root);

    $DBE = new WactDataBindingExpression('Test', $context);
    $DBE->generateExpression($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $root->get(\'Test\')');
  }

  function testGenerateExpressionWithRootModifier()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();
    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpression('#Test', $context);
    $DBE->generateExpression($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $root->get(\'Test\')');
  }

  function testGenerateFullCicleForPathDBE()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();

    $context = new MockWactCompileTreeNode();
    $context->setReturnReference('getDataSource', $root);

    $DBE = new WactDataBindingExpression('Test.item1.item2', $context);
    $DBE->generatePreStatement($code_writer);
    $DBE->generateExpression($code_writer);
    $DBE->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $A= WactTemplate :: makeObject($root,\'Test\');'.
                                                '$B= WactTemplate :: makeObject($A,\'item1\');'.
                                                '$B->get(\'item2\')');
  }
}

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDataBindingExpression.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */


class WactDataBindingExpression
{
  protected $context;
  protected $datasource_context;
  protected $original_expression;
  protected $processed_expression;

  protected $path_to_target_datasource;
  protected $field_name;

  protected $datasource_ref_var;

  protected $expression_analyzed = FALSE;

  protected $property;

  function __construct($expression, $context_node)
  {
    $this->original_expression = $expression;
    $this->context = $context_node;
    $this->datasource_context = $context_node;
  }

  function analyzeExpression()
  {
    if ($this->expression_analyzed)
      return;

    $this->_findRealContext();

    if(!$this->datasource_context)
      $this->context->raiseCompilerError('Expression datasource context not found', array('expression' => $this->original_expression));

    $this->_extractPathToTargetDatasource();

    $this->_extractTargetFieldName();

    /* pre-defined properties will never be found inside a child datasource context */
    if (is_object($this->datasource_context))
    {
      $this->property = $this->datasource_context->getProperty($this->field_name);
      if (is_object($this->property))
        $this->property->activate();
    }

    $this->expression_analyzed = TRUE;
  }

  protected function _findRealContext()
  {
    $this->processed_expression = $this->original_expression;
    $prefix = substr($this->processed_expression, 0, 1);
    if ($prefix == "#")
    {
      $this->datasource_context = $this->datasource_context->getRootDataSource();
      $this->processed_expression = substr($this->processed_expression, 1);
    }
    else if ($prefix == "^")
    {
      while ($prefix == "^")
      {
        $this->datasource_context = $this->datasource_context->getParentDataSource();
        $this->processed_expression = substr($this->processed_expression, 1);
        $prefix = substr($this->processed_expression, 0, 1);
      }
    }
  }

  protected function _extractPathToTargetDatasource()
  {
    $pos = strpos($this->processed_expression, '.');
    if (!is_integer($pos))
      return;

    $this->path_to_target_datasource = array();
    while (preg_match('/^(\w+)\.((?s).*)$/', $this->processed_expression, $match))
    {
      $this->path_to_target_datasource[] = $match[1];
      $this->processed_expression = $match[2];
    }
  }

  protected function _extractTargetFieldName()
  {
    if (preg_match("/^\w+$/", $this->processed_expression))
      $this->field_name = $this->processed_expression;
    else
      $this->datasource_context->raiseCompilerError('Invalid data binding', array('expression' => $this->original_expression));
  }

  function prepare()
  {
    $this->analyzeExpression();
  }

  function getFieldName()
  {
    return $this->field_name;
  }

  function isConstant()
  {
    $this->analyzeExpression();

    if (is_null($this->datasource_context))
      return TRUE;

    if (is_object($this->property))
      return $this->property->isConstant();

    return FALSE;
  }

  /**
  * Return the value of this expression
  */
  function getValue()
  {
    $this->analyzeExpression();

    if (is_null($this->property) || !$this->property->isConstant())
      $this->datasource_context->raiseCompilerError('Cannot resolve data binding', array('expression' => $this->original_expression));
    else
      return $this->property->getValue();
  }

  /**
  * Generate setup code for an expression reference
  */
  function generatePreStatement($code_writer)
  {
    $this->analyzeExpression();

    if (is_object($this->property))
      $this->property->generatePreStatement($code_writer);

    $this->_generateReferencesChainToTargetDatasource($code_writer);
  }

  protected function _generateReferencesChainToTargetDatasource($code_writer)
  {
    if (!isset($this->path_to_target_datasource))
      return;

    $key = array_shift($this->path_to_target_datasource);

    $this->datasource_ref_var = $code_writer->getTempVarRef();

    $code_writer->writePHP($this->datasource_ref_var . '= WactTemplate :: makeObject(' . $this->datasource_context->getDataSource()->getComponentRefCode() . ',');
    $code_writer->writePHPLIteral($key);
    $code_writer->writePHP(');');

    foreach ($this->path_to_target_datasource as $key)
    {
      $datasource_ref_var = $code_writer->getTempVarRef();
      $code_writer->writePHP($datasource_ref_var . '= WactTemplate :: makeObject(' . $this->datasource_ref_var . ',');
      $code_writer->writePHPLIteral($key);
      $code_writer->writePHP(');');
      $this->datasource_ref_var = $datasource_ref_var;
    }
  }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  */
  function generateExpression($code_writer)
  {
     $this->analyzeExpression();

    if (is_object($this->property))
    {
      $this->property->generateExpression($code_writer);
      return;
    }

    if (isset($this->datasource_ref_var))
    {
      $code_writer->writePHP($this->datasource_ref_var . '->get(');
      $code_writer->writePHPLiteral($this->field_name);
      $code_writer->writePHP(')');
    }
    else
    {
      $code_writer->writePHP('' . $this->datasource_context->getDatasource()->getComponentRefCode() . '->get(');
      $code_writer->writePHPLiteral($this->field_name);
      $code_writer->writePHP(')');
    }
  }

  function generatePostStatement($code_writer)
  {
    $this->analyzeExpression();

    if (is_object($this->property))
      $this->property->generatePostStatement($code_writer);
  }
}

?>
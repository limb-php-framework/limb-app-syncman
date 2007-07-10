<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactAttributeProperty.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* A property linked to the value of an attribute.
*/
class WactAttributeProperty extends WactCompilerProperty
{
  var $attribute;

  function __construct($attribute)
  {
    $this->attribute = $attribute;
  }

  function isConstant()
  {
    return $this->attribute->isConstant();
  }

  function getValue()
  {
    return $this->attribute->getValue();
  }

  function generatePreStatement($code_writer)
  {
    $this->attribute->generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    $this->attribute->generateExpression($code_writer);
  }

  function generatePostStatement($code_writer)
  {
    $this->attribute->generatePostStatement($code_writer);
  }
}

?>
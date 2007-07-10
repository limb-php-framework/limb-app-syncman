<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: trim.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @filter trim
* @max_attributes 1
*/
class WactTrimFilter extends WactCompilerFilter
{
  function getValue()
  {
   if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $characters = $this->parameters[0]->getValue();
    else
      $characters = '';

    if (!$this->isConstant())
      $this->raiseUnresolvedBindingError();

    if($characters)
      return trim($this->base->getValue(), $characters);
    else
      return trim($this->base->getValue());
  }

  function generateExpression(&$code)
  {
    $code->writePHP('trim(');
    $this->base->generateExpression($code);

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $code->writePHP(',');
      $this->parameters[0]->generateExpression($code);
    }

    $code->writePHP(')');
  }
}

?>
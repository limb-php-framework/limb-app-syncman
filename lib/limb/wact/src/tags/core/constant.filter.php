<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: constant.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @filter const
*/
class WactConstantFilter extends WactCompilerFilter
{
  function getValue()
  {
    return constant($this->base->getValue());
  }

  function generateExpression($code)
  {
    $code->writePHP('@constant(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>
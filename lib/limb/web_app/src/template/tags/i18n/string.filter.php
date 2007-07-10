<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: string.filter.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @filter i18n
* @min_attributes 1
* @max_attributes 100
*/
class lmbI18NStringFilter extends WactCompilerFilter
{
  function getValue()
  {
    if(!isset($this->parameters[0]) || !$this->parameters[0]->getValue())
      throw new WactException('MISSING_FILTER_PARAMETER');
    else
      $context = $this->parameters[0]->getValue();

    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $locale = $this->parameters[1]->getValue();
    else
      $locale = '';

    $value = $this->base->getValue();

    if($this->isConstant())
    {
      require_once('limb/i18n/common.inc.php');
      return tr($context, $value, $locale, $this->_getAttributes());
    }
    else
      $this->raiseUnresolvedBindingError();
  }

  function _getAttributes()
  {
    $result = array();

    for($i=2; $i < sizeof($this->parameters); $i+=2)
    {
      $var = $this->parameters[$i]->getValue();
      $value = $this->parameters[$i+1]->getValue();
      $result[$var] = $value;
    }

    return $result;
  }

  function generateExpression($code)
  {
    $code->writePHP('tr(');

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $this->parameters[0]->generateExpression($code);
    else
      throw new WactException('MISSING_FILTER_PARAMETER');

    $code->writePHP(',');

    $this->base->generateExpression($code);
    $code->writePHP(',');

    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
    {
      $this->parameters[1]->generateExpression($code);
    }
    else
    {
      $code->writePHP('""');
    }

    $code->writePHP(')');
  }
}

?>
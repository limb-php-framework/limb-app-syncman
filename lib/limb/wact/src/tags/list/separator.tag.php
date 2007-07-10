<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: separator.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * Compile time component for separators in a list
 * The tag work depends on it's position.
 * You MUST place separator at the END of the list item tag content.
 * Step attribute is 1 by default
 * @tag list:SEPARATOR
 * @restrict_self_nesting
 * @parent_tag_class WactListItemTag
 */
class WactListSeparatorTag extends WactSilentCompilerTag
{
  protected $step;
  protected $counter_var;

  function preParse($compiler)
  {
    if (!$step = $this->getAttribute('step'))
      $this->step = 1;
    else
      $this->step = $step;

    if ($this->getBoolAttribute('literal'))
      return WACT_PARSER_FORBID_PARSING;
  }

  function generateConstructor($code)
  {
    $this->counter_var = $code->getTempVarRef();
    $code->writePhp($this->counter_var . ' = 0;' . "\n");
    parent :: generateConstructor($code);
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $this->counter_var = $code->getTempVarRef();
    $total_var = $code->getTempVarRef();

    $ListList = $this->findParentByClass('WactListListTag');

    $code->writePhp('if(empty(' . $this->counter_var . '))'. "\n");
    $code->writePhp($this->counter_var .' = 0; ' . "\n");

    $code->writePhp('if(empty(' . $total_var . '))'. "\n");
    $code->writePhp($total_var .' = ' . $ListList->getComponentRefCode() . '->count();' . "\n");

    $code->writePhp('if(' . $total_var . ' == ' . $this->counter_var . '){' . "\n");
    $code->writePhp($this->counter_var .' = 0; ' . "\n");
    $code->writePhp($total_var .' = 0;' . "\n");
    $code->writePhp('}' . "\n");


    $code->writePhp($this->counter_var . '++;'. "\n");

    $code->writePhp(
        'if (	(' . $this->counter_var . ' > 0) && (' . $ListList->getComponentRefCode() . '->valid()) '.
              '&& ' . $this->counter_var . '< ' . $total_var .
              '&& (' . $this->counter_var . ' % ' . $this->step . ' == 0)) {'. "\n");
  }

  function postGenerate($code)
  {
    parent::postGenerate($code);

    $code->writePhp('}'. "\n");
  }
}
?>
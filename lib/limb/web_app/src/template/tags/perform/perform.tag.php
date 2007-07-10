<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: perform.tag.php 5018 2007-02-09 15:13:19Z tony $
 * @package    web_app
 */
/**
* @tag perform
* @req_const_attributes command
*/
class lmbPerformTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'lmbPerformComponent';
  protected $runtimeIncludeFile = 'limb/web_app/src/template/components/lmbPerformComponent.class.php';

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->setCommandPath("' . $this->getAttribute('command') .'");' . "\n");

    if($this->hasAttribute('method'))
      $code->writePhp($this->getComponentRefCode() . '->setMethod("' . $this->getAttribute('method') .'");' . "\n");

    $code->writePhp(' echo ' . $this->getComponentRefCode() . '->process($template);' . "\n");
  }

}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: request_transfer.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag limb:REQUEST_TRANSFER
* @req_const_attributes attributes
*/
class lmbRequestTransferTag extends WactRuntimeComponentTag
{
  var $runtimeComponentName = 'lmbRequestTransferComponent';
  var $runtimeIncludeFile = 'limb/web_app/src/template/components/lmbRequestTransferComponent.class.php';

  function preGenerate($code)
  {
    //we override parent behavior
  }

  function postGenerate($code)
  {
    //we override parent behavior
  }

  function generateContents($code)
  {
    $content = $code->getTempVarRef();
    $attributes = $code->getTempVarRef();

    $code->writePhp('ob_start();');

    parent :: generateContents($code);

    $code->writePhp("{$attributes} = explode(',', '" . $this->getAttribute('attributes') . "');");
    $code->writePhp("{$content} = ob_get_contents();ob_end_clean();");

    $code->writePhp($this->getComponentRefCode() . "->setAttributesToTransfer({$attributes});");
    $code->writePhp($this->getComponentRefCode() . "->appendRequestAttributes({$content});");

    $code->writePhp("echo {$content};");
  }
}

?>
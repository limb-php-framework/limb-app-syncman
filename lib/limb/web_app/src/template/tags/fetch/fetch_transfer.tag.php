<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: fetch_transfer.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag fetch:TRANSFER
* @forbid_end_tag
* @req_const_attributes target from
*/
class lmbFetchTransferTag extends WactRuntimeComponentTag
{
  var $runtimeComponentName = 'lmbFetchTransferComponent';
  var $runtimeIncludeFile = 'limb/web_app/src/template/components/lmbFetchTransferComponent.class.php';

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->setSourceId("' . $this->getAttribute('from') .'");');

    $code->writePhp($this->getComponentRefCode() . '->setTargets("' . $this->getAttribute('target') .'");');

    if($this->getAttribute('first'))
      $code->writePhp($this->getComponentRefCode() . '->setOnlyFirstRecord();');

    $code->writePhp($this->getComponentRefCode() . '->process();');
  }
}

?>
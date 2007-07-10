<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: iterator_transfer.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag iterator:TRANSFER
* @req_const_attributes target from
*/
class lmbIteratorTransferTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'lmbIteratorTransferComponent';
  protected $runtimeIncludeFile = 'limb/web_app/src/template/components/lmbIteratorTransferComponent.class.php';

  function generateContents($code)
  {
    parent :: generateContents($code);

    $this->generateDereference($code);

    $code->writePhp($this->getComponentRefCode() . '->setTargets("' . $this->getAttribute('target') .'");');

    if($this->getAttribute('first'))
      $code->writePhp($this->getComponentRefCode() . '->setOnlyFirstRecord();');

    if($order = $this->getAttribute('order'))
      $code->writePhp($this->getComponentRefCode() . '->setOrder("' . $order .'");');

    if($offset = $this->getAttribute('offset'))
      $code->writePhp($this->getComponentRefCode() . '->setOffset("' . $offset .'");');

    if($limit = $this->getAttribute('limit'))
      $code->writePhp($this->getComponentRefCode() . '->setLimit("' . $limit .'");');

    $navigator = $this->getAttribute('navigator');
    if(!empty($navigator))
    {
      $code->writePhp($this->getComponentRefCode() . '->setNavigator("' . $navigator .'");');
    }

    $code->writePhp($this->getComponentRefCode() . '->process();');
  }

  function generateDereference($code_writer)
  {
    $from_dbe = new WactDataBindingExpression($this->getAttribute('from'), $this);
    $from_dbe->generatePreStatement($code_writer);

    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataset(');

    $from_dbe->generateExpression($code_writer);

    $code_writer->writePHP(');');

    $from_dbe->generatePostStatement($code_writer);
  }
}

?>
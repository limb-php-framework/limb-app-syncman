<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: fetch.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag fetch
* @req_const_attributes target using
*/
class lmbFetchTag extends WactRuntimeComponentTag
{
  var $runtimeComponentName = 'lmbFetchComponent';
  var $runtimeIncludeFile = 'limb/web_app/src/template/components/lmbFetchComponent.class.php';

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->setFetcherName("' . $this->getAttribute('using') .'");');

    $navigator = $this->getAttribute('navigator');
    if(!empty($navigator))
    {
      $code->writePhp($this->getComponentRefCode() . '->setNavigator("' . $navigator .'");');
    }

    if($order = $this->getAttribute('order'))
      $code->writePhp($this->getComponentRefCode() . '->setOrder("' . $order .'");');

    if($offset = $this->getAttribute('offset'))
      $code->writePhp($this->getComponentRefCode() . '->setOffset("' . $offset .'");');

    if($limit = $this->getAttribute('limit'))
      $code->writePhp($this->getComponentRefCode() . '->setLimit("' . $limit .'");');

    $code->writePhp($this->getComponentRefCode() . '->setTargets("' . $this->getAttribute('target') .'");');

    if($this->getAttribute('first') || $this->getAttribute('one'))
      $code->writePhp($this->getComponentRefCode() . '->setOnlyFirstRecord();');

    if($this->hasAttribute('cache_dataset') && !$this->getBoolAttribute('cache_dataset'))
    {
      $code->writePhp($this->getComponentRefCode() . '->setCacheDataset(false);');
    }

    $code->writePhp($this->getComponentRefCode() . '->process();');
  }

}

?>
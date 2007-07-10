<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetchTransferComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/template/components/lmbBaseIteratorComponent.class.php');

class lmbFetchTransferComponent extends lmbBaseIteratorComponent
{
  protected $source_id;

  function setSourceId($source_id)
  {
    $this->source_id = $source_id;
  }

  function getDataset()
  {
    $root = $this->getRootComponent($this);
    $fetcher = $root->getChild($this->source_id);

    return $fetcher->getDataset();
  }
}
?>
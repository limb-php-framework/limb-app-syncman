<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorTransferComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/template/components/lmbBaseIteratorComponent.class.php');

class lmbIteratorTransferComponent extends lmbBaseIteratorComponent
{
  protected $navigator_id;
  protected $dataset;

  function setNavigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;
  }

  function getDataset()
  {
    if($this->navigator_id)
      $this->_applyNavigator($this->dataset);

    return $this->_applyDecorators($this->dataset);
  }

  protected function _applyNavigator($dataset)
  {
    if(($navigator = $this->_getNavigatorComponent())  && method_exists($dataset, 'paginate'))
    {
      $navigator->setPagedDataset($dataset);
      $dataset->paginate($navigator->getStartingItem(), $navigator->getItemsPerPage());
    }
  }

  protected function _getNavigatorComponent()
  {
    if(!$this->navigator_id)
      return null;

    if(!$navigator = $this->parent->findChild($this->navigator_id))
      throw new WactException('Navigator component not found', array('navigator' => $this->navigator_id));

    return $navigator;
  }

  function registerDataset($dataset)
  {
    $this->dataset = WactTemplate :: castToIterator($dataset);
  }
}
?>
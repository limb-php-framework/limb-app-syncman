<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetchComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/classkit/src/lmbClassPath.class.php');
lmb_require('limb/web_app/src/template/components/lmbBaseIteratorComponent.class.php');

class lmbFetchComponent extends lmbBaseIteratorComponent
{
  protected $builder_name;
  protected $builder;
  protected $navigator_id;
  protected $params = array();
  protected $dataset = null;
  protected $cache_dataset = true;

  function setFetcherName($fetcher_name)
  {
    $this->fetcher_name = $fetcher_name;
  }

  function setAdditionalParam($param, $value)
  {
    $this->params[$param] = $value;
  }

  function setNavigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;
  }

  function setCacheDataset($flag)
  {
    $this->cache_dataset = $flag;
  }

  function getDataset()
  {
    if($this->dataset !== null && (boolean)$this->cache_dataset)
      return $this->dataset;

    $fetcher = $this->_createFetcher();

    $this->dataset = $fetcher->getDataset();

    if($this->navigator_id)
      $this->_applyNavigator($this->dataset);

    $this->dataset = $this->_applyDecorators($this->dataset);

    return $this->dataset;
  }

  protected function _applyNavigator($dataset)
  {
    if(($navigator = $this->_getNavigatorComponent()) && method_exists($dataset, 'paginate'))
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

  protected function _createFetcher()
  {
    $class_path = new lmbClassPath($this->fetcher_name);
    $fetcher = $class_path->createObject();

    foreach($this->params as $param => $value)
    {
      $method = toStudlyCaps('set_'.$param, false);
      if(method_exists($fetcher, $method))
        $fetcher->$method($value);
      else
        throw new lmbException('lmbFetcher "' .$this->fetcher_name. '" does not support method: '. $method);
    }

    return $fetcher;
  }
}
?>
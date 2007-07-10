<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbBaseIteratorComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/wact/src/WactTemplate.class.php');
lmb_require('limb/dbal/src/modifier/lmbOrderQueryModifier.class.php');

abstract class lmbBaseIteratorComponent extends WactRuntimeComponent
{
  protected $decorators = array();
  protected $order_params = array();
  protected $targets;
  protected $only_first_record;
  protected $offset = 0;
  protected $limit;

  function setOnlyFirstRecord($only_first_record = true)
  {
    $this->only_first_record = $only_first_record;
  }

  function setOffset($offset)
  {
    $this->offset = $offset;
  }

  function setLimit($limit)
  {
    $this->limit = $limit;
  }

  function addDataSetDecorator($class_name)
  {
    $this->decorators[$class_name] = array();
  }

  function setOrder($order)
  {
    if(is_array($order))
    {
      $this->order_params = $order;
      return;
    }
    else
      $this->order_params = lmbOrderQueryModifier :: extractOrderPairsFromString($order);
  }

  function addDataSetDecoratorParameter($class_name, $name, $value)
  {
    $this->decorators[$class_name][$name] = $value;
  }

  function setTargets($targets)
  {
    if(is_array($targets))
      $this->targets = $targets;
    elseif(is_string($targets))
    {
      $this->targets = array();

      $pieces = explode(',', $targets);
      foreach($pieces as $piece)
        $this->targets[] = trim($piece);
    }
  }

  function process()
  {
    $dataset = $this->getDataset();

    if(is_array($this->order_params) && count($this->order_params))
      $dataset->sort($this->order_params);

    if(($this->offset || $this->limit) && method_exists($dataset, 'paginate'))
    {
      if(!$this->limit)
        $this->limit = $dataset->count();

      $dataset->paginate($this->offset, $this->limit);
    }

    if($this->only_first_record)
      $this->_processForFirstRecord($dataset);
    else
    {
      $this->_processForFullDataSet($dataset);
    }
  }

  abstract function getDataset();

  protected function _processForFirstRecord($dataset)
  {
    if(method_exists('paginate', $dataset))
      $dataset->paginate(0, 1);

    $dataset->rewind();
    if($dataset->valid())
      $record = $dataset->current();
    else
      $record = new lmbDataspace();;

    foreach($this->targets as $target)
    {
      if($target_component = $this->parent->findChild($target))
      {
        if(!method_exists($target_component, 'registerDataSource'))
        {
          throw new WactException('target does not accept data source',
                                array('target' => $target));
        }

        $target_component->registerDataSource($record);
      }
      else
      {
        throw new WactException('Target component not found',
                                array('target' => $target));
      }
    }
  }

  protected function _processForFullDataSet($dataset)
  {
    foreach($this->targets as $target)
    {
      if($target_component = $this->parent->findChild($target))
      {
        if(!method_exists($target_component, 'registerDataSet'))
        {
          throw new WactException('target does not accept dataset',
                                array('target' => $target));
        }

        $target_component->registerDataSet($dataset);
      }
      else
      {
        throw new WactException('target component not found',
                                array('target' => $target));
      }
    }
  }

  protected function _applyDecorators($dataset)
  {
    foreach($this->decorators as $decorator_class => $decorator_params)
    {
      lmb_require('limb/classkit/src/lmbClassPath.class.php');
      $class_path = new lmbClassPath($decorator_class);
      $dataset = $class_path->createObject(array($dataset));
      $this->_addParamsToDataset($dataset, $decorator_params);
    }
    return $dataset;
  }

  protected function _addParamsToDataset($dataset, $params)
  {
    foreach($params as $param => $value)
    {
      $method = toStudlyCaps('set_'.$param, false);
      $dataset->$method($value);
    }
  }
}
?>
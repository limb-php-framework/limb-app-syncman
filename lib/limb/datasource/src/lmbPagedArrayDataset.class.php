<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPagedArrayDataset.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');

class lmbPagedArrayDataset extends lmbArrayDataset
{
  protected $iteratedDataset;
  protected $offset = 0;
  protected $limit = 0;

  function paginate($offset, $limit)
  {
    $this->iteratedDataset = null;
    $this->offset = $offset;
    $this->limit = $limit;
    return $this;
  }

  function getOffset()
  {
    return $this->offset;
  }

  function getLimit()
  {
    return $this->limit;
  }

  function rewind()
  {
    $this->_setupIteratedDataset();

    $values = reset($this->iteratedDataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->iteratedDataset);
    $this->valid = $this->_isValid($values);
  }

  function next()
  {
    $values = next($this->iteratedDataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->iteratedDataset);
    $this->valid = $this->_isValid($values);
  }

  function _setupIteratedDataset()
  {
    if(!is_null($this->iteratedDataset))
        return;

    if (!$this->limit)
    {
      $this->iteratedDataset = $this->dataset;
      return;
    }

    if($this->offset < 0 || $this->offset >= count($this->dataset)) {
        $this->iteratedDataset = array();
        return;
    }

    $to_splice_array = $this->dataset;
    $this->iteratedDataset = array_splice($to_splice_array, $this->offset, $this->limit);

    if(!$this->iteratedDataset)
      $this->iteratedDataset = array();
  }

  function add($item)
  {
    parent :: add($item);
    $this->iteratedDataset = null;
  }

  function sort($params)
  {
    parent :: sort($params);
    $this->iteratedDataset = null;
    return $this;
  }

  function count()
  {
    return count($this->dataset);
  }

  function countPaginated()
  {
    $this->_setupIteratedDataset();
    return count($this->iteratedDataset);
  }
}
?>
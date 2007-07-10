<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorBase.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */

abstract class lmbIteratorBase implements Iterator
{
  protected $current;
  protected $valid = false;

  function valid()
  {
    return $this->valid;
  }

  function getArray()
  {
    return array();
  }

  function sort($params)
  {
    return $this;
  }

  function current()
  {
    return $this->current;
  }

  function next()
  {
  }

  function rewind()
  {
  }

  function key()
  {
    return null;
  }

  function at($pos)
  {
    return null;
  }

  function count()
  {
    return 0;
  }
}
?>
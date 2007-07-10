<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactArrayIterator.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */


class WactArrayIterator extends ArrayIterator
{
  public $position = 0;
  public $offset = 0;
  public $limit = 0;

  function rewind()
  {
    $this->position = 0;
    parent :: rewind();
    if($this->offset)
      $this->seek($this->offset);
  }

  function current()
  {
    return new WactArrayObject(parent :: current());
  }

  function next()
  {
    $this->position++;
    return parent :: next();
  }

  function valid()
  {
    if($this->limit && ($this->position >= $this->limit))
      return false;
    return parent :: valid();
  }

  function getOffset()
  {
    return $this->offset;
  }

  function getLimit()
  {
    return $this->limit;
  }

  function paginate($offset, $limit)
  {
    $this->offset = $offset;
    $this->limit = $limit;
  }
}
?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactListComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Represents list tags at runtime, providing an API for preparing the data set
*/
class WactListComponent extends WactRuntimeComponent
{
  protected $dataset;

  function __construct($id)
  {
    parent :: __construct($id);

    $this->dataset = new WactArrayIterator(array());
  }

  function registerDataset($dataset)
  {
    $this->dataset = WactTemplate :: castToIterator($dataset);
 }

  function rewind()
  {
    $this->dataset->rewind();
  }

  function count()
  {
    return $this->dataset->count();
  }

  function next()
  {
    $this->dataset->next();
  }

  function valid()
  {
    return $this->dataset->valid();
  }

  function current()
  {
    return $this->dataset->current();
  }

  function getOffset()
  {
    return $this->dataset->getOffset();
  }
}
?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPagedDatasetDecorator.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbIteratorDecorator.class.php');

class lmbPagedDatasetDecorator extends lmbIteratorDecorator
{
  function paginate($offset, $limit)
  {
    $this->iterator->paginate($offset, $limit);
    return $this;
  }

  function sort($params)
  {
    $this->iterator->sort($params);
    return $this;
  }

  function getOffset()
  {
    return $this->iterator->getOffset();
  }

  function getLimit()
  {
    return $this->iterator->getLimit();
  }

  function countPaginated()
  {
    return $this->iterator->countPaged();
  }

  function count()
  {
    return $this->iterator->count();
  }
}
?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecordSet.interface.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

interface lmbDbRecordSet extends Iterator
{
  function paginate($offset, $limit);
  function sort($params);
  function freeQuery();
  function getArray();
  function at($pos);
  function countPaginated();
  function count();
}

?>

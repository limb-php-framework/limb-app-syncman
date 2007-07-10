<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFile2TestNodeMapper.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */

class lmbFile2TestNodeMapper
{
  function map($start_dir, $file)
  {
    $start_dir = realpath($start_dir);
    $file = realpath($file);
    $file = preg_replace('~^' . preg_quote($start_dir) . '~', '', $file);

    $path_items = explode(DIRECTORY_SEPARATOR, $file);

    if(empty($path_items[0]))
      array_shift($path_items);

    return '/' . $this->_doMap($start_dir, $path_items);
  }

  function _doMap($dir, $path_items)
  {
    $counter = 0;
    $current_item = reset($path_items);

    $node = new lmbTestTreeDirNode($dir);

    foreach($node->getDirItems() as $item => $full_path)
    {
      if($item == $current_item)
      {
        if(sizeof($path_items) > 1 && is_dir($full_path))
        {
          array_shift($path_items);
          return $counter . '/' . $this->_doMap($full_path, $path_items);
        }
        elseif(sizeof($path_items) == 1)
          return $counter;
      }
      $counter++;
    }
  }
}

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDelegateList.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
class lmbDelegateList
{
  static function invokeAll(&$list, $args)
  {
    if (is_object($list))
      $list->invoke($args);
    elseif (is_array($list))
      foreach(array_keys($list) as $key)
          $list[$key]->invoke($args);
  }

  static function invokeChain(&$list, $args)
  {
    if (is_object($list))
        return $list->invoke($args);
    elseif (is_array($list))
    {
      foreach(array_keys($list) as $key) {
          $result = $list[$key]->invoke($args);
          if (!is_null($result))
              return $result;
      }
    }
  }
}
?>

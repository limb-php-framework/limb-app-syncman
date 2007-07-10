<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLCriteria.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require(dirname(__FILE__) . '/lmbSQLRawCriteria.class.php');

class lmbSQLCriteria
{
  static function objectify($args)
  {
    if(is_null($args))
      return new lmbSQLRawCriteria("1 = 1");

    if(is_array($args))
    {
      if(is_object($args[0]))
        return $args[0];

      if(!isset($args[1]) && isset($args[0]))
        return new lmbSQLRawCriteria($args[0]);
      elseif(isset($args[0]) && is_array($args[1]))
        return new lmbSQLRawCriteria($args[0], $args[1]);
      elseif(isset($args[0]))
      {
        $sql = array_shift($args);
        return new lmbSQLRawCriteria($sql, $args);
      }
    }
    elseif(is_string($args))
    {
      return new lmbSQLRawCriteria($args);
    }
    elseif(is_object($args))
    {
      return $args;
    }
  }
}
?>

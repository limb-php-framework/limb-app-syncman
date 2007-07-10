<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: error.inc.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */

function isErrorHandlerInstalled($handler)
{
  $prev_handler = set_error_handler('dummyErrorHandler');
  $res = $prev_handler === $handler;
  set_error_handler($prev_handler);

  return $res;
}

function isPHPErrorHandlerInstalled()
{
  $prev_handler = set_error_handler('dummyErrorHandler');
  $res = $prev_handler === NULL;
  set_error_handler($prev_handler);

  return $res;
}

function dummyErrorHandler(){}

?>
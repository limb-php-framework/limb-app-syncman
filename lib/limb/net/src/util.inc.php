<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: util.inc.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */

function addUrlQueryItems($url, $items=array())
{
  $str_params = '';

  if(strpos($url, '?') === false)
    $url .= '?';
  else
    $url .= '&';

  $str_params_arr = array();
  foreach($items as $key => $val)
  {
    $url = preg_replace("/&*{$key}=[^&]*/", '', $url);
    $str_params_arr[] = "$key=$val";
  }

  $items = explode('#', $url);

  $url = $items[0];
  $fragment = isset($items[1]) ? '#' . $items[1] : '';

  return $url . implode('&', $str_params_arr) . $fragment;
}

?>

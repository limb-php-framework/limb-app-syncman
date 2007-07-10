<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRoutesRequestDispatcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');

class lmbRoutesRequestDispatcher implements lmbRequestDispatcher
{
  function dispatch($request)
  {
    $routes = lmbToolkit :: instance()->getRoutes();

    $uri = $request->getUri();
    $uri->normalizePath();
    $result = $routes->dispatch($uri->getPath());

    if($action = $request->get('action'))
      $result['action'] = $action;
    return $result;
  }
}

?>
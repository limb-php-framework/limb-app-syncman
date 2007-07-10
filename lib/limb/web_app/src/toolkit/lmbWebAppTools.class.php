<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppTools.class.php 5018 2007-02-09 15:13:19Z tony $
 * @package    web_app
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

@define('LIMB_CONTROLLERS_INCLUDE_PATH', 'src/controller;limb/*/src/controller');
@define('LIMB_TEMPLATES_INCLUDE_PATH', 'template;limb/*/template');
@define('LIMB_WACT_TAGS_INCLUDE_PATH', 'src/template/tags;limb/*/src/template/tags;limb/wact/src/tags');

class lmbWebAppTools extends lmbAbstractTools
{
  protected $session;
  protected $view;
  protected $dispatched_controller;
  protected $wact_locator;
  protected $routes;
  protected $message_box;
  protected $session_message_box;
  protected $default_db_config;

  function getSession()
  {
    if(is_object($this->session))
      return $this->session;

    lmb_require('limb/session/src/lmbSession.class.php');
    $this->session = new lmbSession();

    return $this->session;
  }

  function setSession($session)
  {
    $this->session = $session;
  }

  function setView($view)
  {
    $this->view = $view;
  }

  function getView()
  {
    if(is_object($this->view))
      return $this->view;

    lmb_require('limb/web_app/src/view/lmbWACTView.class.php');
    $this->view = new lmbWACTView();

    return $this->view;
  }

  function renderView($template)
  {
    $toolkit = lmbToolkit :: instance();
    $view = $toolkit->getView();
    $response = $toolkit->getResponse();
    $view->setTemplate($template);
    $response->write($view->render());
  }

  function setDispatchedController($dispatched)
  {
    $this->dispatched_controller = $dispatched;
  }

  function getDispatchedController()
  {
    return $this->dispatched_controller;
  }

  function setDefaultDbDSN($conf)
  {
    $this->default_db_config = new lmbDbDSN($conf);
  }

  function getDefaultDbDSN()
  {
    if(is_object($this->default_db_config))
      return $this->default_db_config;

    $conf = lmbToolkit :: instance()->getConf('db');
    $this->default_db_config = new lmbDbDSN($conf->get('dsn'));

    return $this->default_db_config;
  }

  function getWactLocator()
  {
    if(is_object($this->wact_locator))
      return $this->wact_locator;

    if(!defined('LIMB_TEMPLATES_INCLUDE_PATH'))
       throw new lmbException('LIMB_TEMPLATES_INCLUDE_PATH constant is not defined!');

    lmb_require('limb/web_app/src/template/lmbWactTemplateLocator.class.php');

    $locator = lmbToolkit :: instance()->getFileLocator(LIMB_TEMPLATES_INCLUDE_PATH, 'template');
    $this->wact_locator = new lmbWactTemplateLocator($locator);

    return $this->wact_locator;
  }

  function setWactLocator($wact_locator)
  {
    $this->wact_locator = $wact_locator;
  }

  function getRoutesUrl($params = array(), $route_name = '')
  {
    $routes = lmbToolkit :: instance()->getRoutes();
    if(!isset($params['controller']))
      $params['controller'] = lmbToolkit :: instance()->getDispatchedController()->getName();

    return $routes->toUrl($params, $route_name);
  }

  function getRoutes()
  {
    if(!$this->routes)
    {
      $config = lmbToolkit :: instance()->getConf('routes');

      lmb_require('limb/web_app/src/request/lmbRoutes.class.php');
      $this->routes = new lmbRoutes($config->export());
    }

    return $this->routes;
  }

  function setRoutes($routes)
  {
    $this->routes = $routes;
  }

  function getMessageBox()
  {
    if(!is_object($this->message_box))
    {
      lmb_require('limb/web_app/src/util/lmbMessageBox.class.php');
      $this->message_box = new lmbMessageBox();
    }

    return $this->message_box;
  }

  function getSessionMessageBox()
  {
    if(!is_object($this->session_message_box))
    {
      lmb_require('limb/web_app/src/util/lmbSessionMessageBox.class.php');
      $this->session_message_box = lmbSessionMessageBox :: create(lmbToolkit :: instance()->getSession());
    }

    return $this->session_message_box;
  }

  function flashError($message)
  {
    lmbToolkit :: instance()->getSessionMessageBox()->addError($message);
  }

  function flashMessage($message)
  {
    lmbToolkit :: instance()->getSessionMessageBox()->addMessage($message);
  }

  function createController($controller_name)
  {
    if(!defined('LIMB_CONTROLLERS_INCLUDE_PATH'))
       throw new lmbException('LIMB_CONTROLLERS_INCLUDE_PATH constant is not defined!');

    $class_name = toStudlyCaps($controller_name) . 'Controller';
    $file = lmbToolkit :: instance()->findFileAlias("$class_name.class.php", LIMB_CONTROLLERS_INCLUDE_PATH, 'controller');
    lmb_require($file);
    return new $class_name;
  }

  function redirect($params_or_url = array(), $route_url = null, $append = '')
  {
    $toolkit = lmbToolkit :: instance();

    if(is_array($params_or_url))
      $toolkit->getResponse()->redirect($toolkit->getRoutesUrl($params_or_url, $route_url) . $append);
    else
      $toolkit->getResponse()->redirect($params_or_url . $append);
  }
}
?>

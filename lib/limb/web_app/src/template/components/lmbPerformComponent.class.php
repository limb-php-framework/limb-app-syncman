<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPerformComponent.class.php 5018 2007-02-09 15:13:19Z tony $
 * @package    web_app
 */
lmb_require('limb/classkit/src/lmbClassPath.class.php');
lmb_require('limb/web_app/src/template/components/lmbBaseIteratorComponent.class.php');

class lmbPerformComponent extends WactRuntimeComponent
{
  protected $command_path;
  protected $method_name = 'perform';
  protected $params = array();

  function setCommandPath($command_path)
  {
    $this->command_path = $command_path;
  }

  function setMethod($method)
  {
    $this->method_name = $method;
  }

  function addParam($value)
  {
    $this->params[] = $value;
  }

  function process($template)
  {
    $command = $this->_createCommand($template);

    $method = toStudlyCaps('do_'. $this->method_name, false);
    if(!method_exists($command, $method))
      throw new lmbException('TemplateCommand "' .$this->command_path. '" does not support method: '. $method);

    return call_user_func_array(array($command, $method), $this->params);
  }

  protected function _createCommand($template)
  {
    $class_path = new lmbClassPath($this->command_path);
    return $class_path->createObject(array($template, $this->parent));
  }
}
?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTemplateCommand.class.php 5018 2007-02-09 15:13:19Z tony $
 * @package    web_app
 */

class lmbTemplateCommand
{
  protected $template;
  protected $context_component;

  function __construct($template, $context_component)
  {
    $this->template = $template;
    $this->context_component = $context_component;
  }

  function doPerform() {}
}
?>
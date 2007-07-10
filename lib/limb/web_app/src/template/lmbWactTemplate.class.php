<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWactTemplate.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
require_once('limb/web_app/wact.inc.php');
require_once('limb/wact/src/WactTemplate.class.php');
lmb_require('limb/web_app/src/template/lmbWactTemplateConfig.class.php');

class lmbWactTemplate extends WactTemplate
{
  function __construct($template_path)
  {
    $config = new lmbWactTemplateConfig();
    $locator = lmbToolkit :: instance()->getWactLocator();
    parent :: __construct($template_path, $config, $locator);
  }
}
?>
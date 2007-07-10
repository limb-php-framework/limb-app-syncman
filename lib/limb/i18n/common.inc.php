<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
require_once('limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/toolkit.inc.php');

if(!function_exists('tr'))
{
  function tr($context, $text, $lang = null, $attributes = null)
  {
    return lmbToolkit :: instance()->tr($context, $text, $lang, $attributes);
  }
}

?>

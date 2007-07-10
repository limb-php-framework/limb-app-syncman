<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: js_checkbox.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/tags/form/control.inc.php');
/**
* @tag js_checkbox
* @known_parent WactFormTag
* @suppress_attributes errorclass errorstyle displayname
* @forbid_end_tag
*/
class WactJSCheckboxTag extends WactControlTag
{
  var $runtimeComponentName = 'WactJSCheckboxComponent';
  var $runtimeIncludeFile = 'limb/wact/src/components/form/WactJSCheckboxComponent.class.php';

  function prepare()
  {
    $this->setAttribute('type', 'hidden');

    parent :: prepare();
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->renderJSCheckbox();');
  }
}

?>
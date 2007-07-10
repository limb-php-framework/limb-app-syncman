<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactJSCheckboxComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactJSCheckboxComponent extends WactInputFormElement
{
  function renderAttributes()
  {
    unset($this->attributes['value']);
    parent :: renderAttributes();
  }

  function renderJsCheckbox()
  {
    $id = $this->getAttribute('id');
    $name = $this->getAttribute('name');

    if ($this->getAttribute('value'))
      $checked = 'checked=\'on\'';
    else
      $checked = '';

    $js = "onclick=\"this.form.elements['{$name}'].value = 1*this.checked\"";

    echo "<input type='checkbox' id='{$id}_checkbox' {$checked} {$js}>";

  }

}
?>
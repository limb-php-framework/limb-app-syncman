<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileUploadRequiredRule.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

class lmbFileUploadRequiredRule implements lmbValidationRule
{
  protected $field_name;

  function __construct($field_name)
  {
    $this->field_name = $field_name;
  }

  function validate($datasource, $error_list)
  {
    $value = $datasource->get($this->field_name);

    if (empty($value['name']))
    {
      $error_list->addError(tr('/validation', '{Field} is required.'),
                                 array('Field' => $this->field_name));

      return FALSE;
    }
    return TRUE;
  }
}
?>
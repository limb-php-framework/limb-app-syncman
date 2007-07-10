<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileUploadMimeTypeRule.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbFileUploadMimeTypeRule extends lmbSingleFieldRule
{
  protected $mime_types = array();

  function __construct($field_name, $mime_types = array())
  {
    parent :: __construct($field_name);

    $this->mime_types = $mime_types;
  }

  function check($value)
  {
    if (! empty($value['type']) &&
        ! in_array($value['type'], $this->mime_types))
    {
      $this->error(tr('/validation', '{Field} - uploaded file must be of type: {mimetypes}.'),
                   array('mime_types' => implode(', ', $this->mime_types)));
    }
  }
}
?>
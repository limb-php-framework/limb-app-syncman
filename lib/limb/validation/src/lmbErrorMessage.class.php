<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorMessage.class.php 5106 2007-02-18 09:23:45Z serega $
 * @package    validation
 */

/**
* Single validation error message.
* Holds a reference to list of errors
* Extends ArrayObject just to simplify support for ArrayAccess interface
* Returns result of getErrorMessage() method on any field access
* @see lmbErrorList
*/
class lmbErrorMessage extends ArrayObject
{
  var $error_list;
  var $message;
  var $field_list = array();
  var $values = array();

  /**
  * Constructor.
  * Normally there is no need to initialize objects of lmbErrorMessage in client code since lmbErrorList :: addError() does this
  * @see lmbErrorList :: addError()
  * @param string Error message
  * @param array Array of aliases
  * @param array Array of aliases
  */
  function __construct($error_list, $message, $field_list = array(), $values = array())
  {
    $this->error_list = $error_list;
    $this->message = $message;
    $this->field_list = $field_list;
    $this->values = $values;

    parent :: __construct(array());
  }

  /**
  * Return processed error message
  * Replaces placeholders in {@link $message} with values from {@link $field_list} and {@link $values}
  * @see lmbErrorList :: addError()
  * @see lmbErrorList :: getFieldName()
  * @see __construct
  * @return string
  */
  function getErrorMessage()
  {
    $text = $this->message;

    foreach($this->field_list as $key => $fieldName)
    {
      $replacement = $this->error_list->getFieldName($fieldName);
      $text = str_replace('{' . $key . '}', $replacement, $text);
    }

    foreach($this->values as $key => $replacement)
      $text = str_replace('{' . $key . '}', $replacement, $text);

    return $text;
  }

  function getFieldsList()
  {
    return $this->field_list;
  }

  function getValuesList()
  {
    return $this->values;
  }

  function offsetExists($offset)
  {
    return true;
  }

  function offsetGet($offset)
  {
    return $this->get($name);
  }

  function get($name)
  {
    return $this->getErrorMessage();
  }
}

?>

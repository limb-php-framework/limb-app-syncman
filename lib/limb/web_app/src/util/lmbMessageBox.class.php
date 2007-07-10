<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMessageBox.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

class lmbMessageBox
{
  const ERRORS = 1;
  const MESSAGES = 2;

  protected $messages = array();

  function __construct()
  {
    $this->reset();
  }

  function reset()
  {
    $this->messages = array(self :: ERRORS => array(),
                            self :: MESSAGES => array());
  }

  function resetMessages()
  {
    $this->messages[self :: MESSAGES] = array();
  }

  function resetErrors()
  {
    $this->messages[self :: ERRORS] = array();
  }

  function addError($error)
  {
    $this->messages[self :: ERRORS][] = $error;
  }

  function addMessage($message)
  {
    $this->messages[self :: MESSAGES][] = $message;
  }

  function getErrors()
  {
    return $this->messages[self :: ERRORS];
  }

  function getMessages()
  {
    return $this->messages[self :: MESSAGES];
  }

  function hasErrors()
  {
    return sizeof($this->messages[self :: ERRORS]) > 0;
  }

  function hasMessages()
  {
    return sizeof($this->messages[self :: MESSAGES]) > 0;
  }
}

?>
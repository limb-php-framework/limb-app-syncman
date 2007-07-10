<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCliInput.class.php 4988 2007-02-08 15:35:19Z pachanga $
 * @package    cli
 */

class lmbCliInput
{
  protected $minimum_args = null;
  protected $options = array();
  protected $arguments = array();
  protected $throw_exception = false;

  function __construct()
  {
    $args = func_get_args();
    $this->_addOptions($args);
  }

  function setMinimumArguments($minimum_args)
  {
    $this->minimum_args = $minimum_args;
  }

  function read($argv = null)
  {
    try
    {
      if(is_null($argv))
        $argv = self :: readPHPArgv();

      array_shift($argv);
      $this->_parse($argv);
      $this->_validate();
    }
    catch(lmbCliException $e)
    {
      if($this->throw_exception)
        throw $e;

      return false;
    }
    return true;
  }

  function throwException($flag = true)
  {
    $this->throw_exception = $flag;
  }

  //idea taken from PEAR::Getopt
  function readPHPArgv()
  {
    global $argv;
    if(is_array($argv))
      return $argv;

    if(@is_array($_SERVER['argv']))
      return $_SERVER['argv'];

    if(@is_array($GLOBALS['HTTP_SERVER_VARS']['argv']))
      return $GLOBALS['HTTP_SERVER_VARS']['argv'];

    throw new lmbCliException('Could not read cmd args (register_argc_argv=Off?)');
  }

  function getOption($name)
  {
    foreach($this->options as $option)
    {
      if($option->match($name))
        return $option;
    }
  }

  function isOptionPresent($name)
  {
    if($option = $this->getOption($name))
      return $option->isPresent();

    return null;
  }

  function getOptionValue($name, $default = null)
  {
    if($option = $this->getOption($name))
      return $option->getValue();

    return $default;
  }

  function getArgument($index, $default = null)
  {
    return isset($this->arguments[$index]) ? $this->arguments[$index] : $default;
  }

  function getOptions()
  {
    return $this->options;
  }

  function getArguments()
  {
    return $this->arguments;
  }

  protected function _validate()
  {
    if(!is_null($this->minimum_args) && $this->minimum_args > sizeof($this->arguments))
      throw new lmbCliException("Minimum {$this->minimum_args} required");

    foreach($this->options as $option)
      $option->validate();
  }

  protected function _addOptions($args)
  {
    foreach($args as $arg)
      $this->options[] = $arg;
  }

  protected function _parse($argv)
  {
    $this->_reset();

    for($i=0;$i<sizeof($argv);$i++)
    {
      $arg = $argv[$i];

      if($this->_extractLongOption($arg, $name, $value))
      {
        $postponed_option = $this->_addLongOption($name);

        if(isset($value))
        {
          $postponed_option->setValue($value);
          unset($postponed_option);
        }
      }
      elseif($this->_extractShortOption($arg, $name, $value))
      {
        $postponed_option = $this->_addShortOption($name);

        if($value)
        {
          if(!$postponed_option->isValueForbidden())
            $postponed_option->setValue($value);
          elseif($this->_isArgumentNext($argv, $i))
            $this->arguments[] = $value;

          unset($postponed_option);
        }
        elseif($postponed_option->isValueForbidden())
          unset($postponed_option);
      }
      elseif(isset($postponed_option))
      {
        $postponed_option->setValue($arg);
        unset($postponed_option);
      }
      else
      {
        $this->arguments[] = $arg;
        if(!$this->_isArgumentNext($argv, $i))
          break;
      }
    }
  }

  protected function _extractLongOption($arg, &$option, &$value = null)
  {
    if(!preg_match('~^--([a-z]+)(=(.*))?$~', $arg, $m))
      return false;

    $option = $m[1];
    $value = isset($m[3]) ? $m[3] : null;
    return true;
  }

  protected function _extractShortOption($arg, &$option, &$value = null)
  {
    if(!preg_match('~^-([a-z][^=\s]*)((=|\s+)(.*))?$~', $arg, $m))
      return false;

    $option = $m[1];
    $value = isset($m[4]) ? $m[4] : null;
    return true;
  }

  protected function _isArgumentNext($argv, $i)
  {
    return (isset($argv[$i+1]) &&
            strpos($argv[$i+1], '-') === false);
  }

  protected function _reset()
  {
    $this->arguments = array();
    foreach($this->options as $option)
      $option->reset();
  }

  protected function _addLongOption($name)
  {
    $option = $this->_newOption($name);
    return $option;
  }

  protected function _addShortOption($name)
  {
    $options = $this->_getGluedOptions($name, $glued_value);
    foreach($options as $glued_option)
      $option = $this->_newOption($glued_option);

    if($glued_value)
      $option->setValue($glued_value);

    return $option;
  }

  protected function _newOption($name)
  {
    if(!$option = $this->getOption($name))
      throw new lmbCliException("Option '{$name}' is illegal");

    $option->touch();
    return $option;
  }

  protected function _getGluedOptions($glue, &$glued_value)
  {
    $glued = array();
    $glued_value = null;

    for($j=0;$j<strlen($glue);$j++)
    {
      $name = $glue{$j};

      if(!$this->getOption($name))
      {
        $glued_value = substr($glue, $j);
        break;
      }
      $glued[] = $name;
    }
    return $glued;
  }
}

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUploadedFilesParser.class.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */

class lmbUploadedFilesParser
{
  function parse($files)
  {
    $result = array();

    foreach($files as $key => $chunk)
    {
      if($this->_isSimple($chunk))
        $result[$key] = $chunk;
      else
        $result[$key] = $this->_parseComplexChunk($chunk);
    }

    return $result;

  }

  protected function _isSimple($chunk)
  {
    if((isset($chunk['name']) && !is_array($chunk['name'])) &&
       (isset($chunk['error']) && !is_array($chunk['error'])) &&
       (isset($chunk['type']) && !is_array($chunk['type'])) &&
       (isset($chunk['size']) && !is_array($chunk['size'])) &&
       (isset($chunk['tmp_name']) && !is_array($chunk['tmp_name'])))
      return true;
    else
      return false;
  }

  protected function _parseComplexChunk($chunk)
  {
    $result = array();
    foreach($chunk as $property_name => $data_set)
    {
      foreach($data_set as $arg_name => $value)
        $this->_parseRecursivePropertyValue($result[$arg_name], $property_name, $value);
    }
    return $result;
  }

  protected function _parseRecursivePropertyValue(&$result, $property_name, $data_set)
  {
    if(!is_array($data_set))
    {
      $result[$property_name] = $data_set;
      return;
    }

    foreach($data_set as $arg_name => $value)
    {
      $this->_parseRecursivePropertyValue($result[$arg_name], $property_name, $value);
    }
  }
}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTagInfoExtractor.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactTagInfoExtractor
{
  protected $dictionary;
  protected $file;
  protected $annotations = array();

  function __construct($dict, $file)
  {
    $this->dictionary = $dict;
    $this->file = $file;
  }

  function setCurrentFile($file)
  {
    $this->file = $file;
  }

  function annotation($name, $value)
  {
    $this->annotations[$name] = $value;
  }

  function beginClass($class, $parent_class)
  {
    $this->_validate();

    $info = new WactTagInfo($this->annotations['tag'], $class);

    if(isset($this->annotations['suppress_attributes']))
    {
      $attrs = $this->_processAttributesString($this->annotations['suppress_attributes']);
      $info->setSuppressAttributes($attrs);
    }

    if(isset($this->annotations['req_attributes']))
    {
      $attrs = $this->_processAttributesString($this->annotations['req_attributes']);
      $info->setRequiredAttributes($attrs);
    }

    if(isset($this->annotations['req_const_attributes']))
    {
      $attrs = $this->_processAttributesString($this->annotations['req_const_attributes']);
      $info->setRequiredConstantAttributes($attrs);
    }

    if(array_key_exists('forbid_parsing', $this->annotations))
      $info->setForbidParsing();

    if(isset($this->annotations['parent_tag_class']))
      $info->setParentTagClass($this->annotations['parent_tag_class']);

    if(array_key_exists('restrict_self_nesting', $this->annotations))
      $info->setRestrictSelfNesting();

    if(array_key_exists('forbid_end_tag', $this->annotations))
      $info->setForbidEndTag();

    if(isset($this->annotations['runat']))
      $info->setRunat($this->annotations['runat']);


    if(isset($this->annotations['runat_as']))
      $info->setRunatAs($this->annotations['runat_as']);

    $this->dictionary->registerWactTagInfo($info, $this->file);
  }

  function endClass()
  {
    $this->annotations = array();
  }

  function _processAttributesString($attributes_string)
  {
    return explode(' ', preg_replace('~\s+~', ' ', trim($attributes_string)));
  }

  function _validate()
  {
    if(!file_exists($this->file))
      throw new WactException('File not found', array('file' => $this->file));

    if(!isset($this->annotations['tag']))
      throw new WactException('Annotation not found in file',
                              array('annotation' => 'tag', 'file' => $this->file));
  }
}

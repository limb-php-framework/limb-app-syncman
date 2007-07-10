<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTemplateHighlightHandler.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
require_once(XML_HTMLSAX3 . '/HTMLSax3.php');

class lmbTemplateHighlightHandler
{
  var $html = '';
  var $current_tag = '';
  var $template_path_history = array();
  var $tag_dictionary = null;

  function lmbTemplateHighlightHandler($tag_dictionary)
  {
    $this->tag_dictionary = $tag_dictionary;
  }

  function setTemplatePathHistory($history)
  {
    $this->template_path_history = $history;
  }

  function writeAttributes($attributes)
  {
    if (is_array($attributes))
    {
      foreach ($attributes as $name => $value)
      {
        $name_html = $name;
        $value_html = $value;

        if($this->tag_dictionary->getTagInfo($this->current_tag))
        {
          $name_html = "<span style='color:red;'>{$name}</span>";
          $value_html = "<span style='color:brown;'>{$value}</span>";
        }

        if($this->current_tag == 'core:wrap' ||  $this->current_tag == 'core:include')
        {
          if($name == 'file')
          {
            $history = array();
            $history = $this->template_path_history;
            $history[] = $value;

            $history_string = 't[]=' . implode('&t[]=', $history);

            $href = "/root/template_source?{$history_string}";

            $value_html = "<a style='text-decoration:underline;font-weight:bold;' href={$href}>{$value}</a>";
          }
        }

        $this->html .= ' ' . $name_html . '="' . $value_html . '"';
      }
    }
  }

  function openHandler($parser, $name, $attrs)
  {
    $this->current_tag = strtolower($name);

    if($this->tag_dictionary->getTagInfo($name))
      $this->html .= '&lt;<span style="color:orange;font-weight:bold;">' . $name . '</span>';
    else
      $this->html .= '&lt;<span style="color:blue">' . $name . '</span>';

    $this->writeAttributes($attrs);

    $this->html .= '&gt;';
  }

  function closeHandler($parser, $name)
  {
    if($this->tag_dictionary->getTagInfo($name))
      $this->html .= '&lt;/<span style="color:orange;font-weight:bold;">' . $name . '</span>&gt;';
    else
      $this->html .= '&lt;/<span style="color:blue">' . $name . '</span>&gt;';
  }

  function dataHandler($parser, $data)
  {
    $data = str_replace("\t", '  ', $data);
    $this->html .= $data;
  }

  function escapeHandler($parser, $data)
  {
    $this->html .= '<span style="color:green;font-style:italic;">&lt;!' . $data . '&gt;</span>';
  }

  function getHtml()
  {
    $this->html = preg_replace('~(\{(\$|\^|#)[^\}]+\})~', "<span style='background-color:lightgreen;font-weight:bold;'>\\1</span>", $this->html);

    $lines = preg_split( "#\r\n|\r|\n#", $this->html);

    $content = '';
    $max = sizeof($lines);
    $digits = strlen("{$max}");

    for($i=0; $i < $max; $i++)
    {
      $j = $i + 1;
      $content .= "<span style='font-family:courier;color:#c0c0c0;'>{$j}" . str_repeat('&nbsp;', $digits - strlen("{$j}")) . "</span> " .  $lines[$i] . "\n";
    }

    return $content;
  }
}
?>
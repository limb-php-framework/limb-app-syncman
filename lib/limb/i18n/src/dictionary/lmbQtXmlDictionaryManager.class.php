<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbQtXmlDictionaryManager.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */

class lmbQtXmlDictionaryManager
{
  protected $dictionary;
  protected $current_context;
  protected $current_source;

  protected $use_cache = false;
  protected $is_name = false;
  protected $is_source = false;
  protected $is_translation = false;

  function useCache($flag = true)
  {
    $this->use_cache = $flag;
  }

  function getDOMDocument($dictionary)
  {
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->formatOutput = true; // pretty printing

    $ts_node = $doc->createElement('TS');
    $doc->appendChild($ts_node);

    $translations = $dictionary->getTranslations();

    foreach($translations as $section => $messages)
    {
      $context_node = $doc->createElement('context');
      $name_node = $doc->createElement('name');
      $name_node->appendChild($doc->createTextNode($section));

      $context_node->appendChild($name_node);

      foreach($messages as $source => $translation)
      {
        $message_node = $doc->createElement('message');
        $source_node = $doc->createElement('source');
        $translation_node = $doc->createElement('translation');

        $source_node->appendChild($doc->createTextNode($source));

        if(empty($translation))
          $translation_node->setAttribute('type', 'unfinished');
        else
          $translation_node->appendChild($doc->createTextNode($translation));

        $message_node->appendChild($source_node);
        $message_node->appendChild($translation_node);

        $context_node->appendChild($message_node);
      }
      $ts_node->appendChild($context_node);
    }
    return $doc;
  }

  function loadXML($dictionary, $xml)
  {
    $this->_parseXML($dictionary, $xml);
  }

  function loadFromFile($dictionary, $file)
  {
    if(!file_exists($file))
      throw new lmbFileNotFoundException($file, "translations file $file not found");

    if(!$this->_loadFromCache($dictionary, $file))
    {
      $this->_parseXML($dictionary, file_get_contents($file));
      $this->_saveToCache($dictionary, $file);
    }
  }

  function saveToFile($dictionary, $file)
  {
    $this->getDOMDocument($dictionary)->save($file);
  }

  protected function _loadFromCache($dictionary, $file)
  {
    if(!$this->use_cache)
      return false;

    if(!file_exists($cache = $this->_getCacheFile($file)))
      return false;

    $dictionary->setTranslations(unserialize(file_get_contents($cache)));
    return true;
  }

  protected function _saveToCache($dictionary, $file)
  {
    if(!$this->use_cache)
      return;

    $cache = $this->_getCacheFile($file);
    if(!is_dir($dir = dirname($cache)))
      mkdir($dir);
    file_put_contents($this->_getCacheFile($file), serialize($dictionary->getTranslations()), LOCK_EX);
  }

  protected function _getCacheFile($file)
  {
    return LIMB_VAR_DIR . '/i18n/' . md5(realpath($file));
  }

  protected function _parseXML($dictionary, $xml)
  {
    $this->dictionary = $dictionary;

    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_object($parser, $this);
    xml_set_element_handler($parser, 'tagOpen', 'tagClosed');

    xml_set_character_data_handler($parser, 'tagData');

    $res = xml_parse($parser, $xml);
    if(!$res)
    {
      throw new lmbException("XML error",
                              array('error' => xml_error_string(xml_get_error_code($parser)),
                                    'line' => xml_get_current_line_number($parser)));
    }

    xml_parser_free($parser);
  }

  function tagOpen($parser, $name, $attrs)
  {
    if($name == 'name')
    {
      $this->is_name = true;
    }
    elseif($name == 'source')
    {
      $this->is_source = true;
    }
    elseif($name == 'translation')
    {
      if(isset($attrs['type']) && $attrs['type'] == 'unfinished')
        $this->dictionary->addEntry($this->current_context, $this->current_source);
      else
        $this->is_translation = true;
    }
  }

  function tagData($parser, $data)
  {
    $trimmed_data = trim($data);

    if($this->is_name)
    {
      $this->current_context = $trimmed_data;
    }
    elseif($this->is_source)
    {
      $this->current_source = $trimmed_data;
    }
    elseif($this->is_translation)
    {
      $this->dictionary->addEntry($this->current_context, $this->current_source, $trimmed_data);
    }
  }

  function tagClosed($parser, $name)
  {
    if($name == 'name')
    {
      $this->is_name = false;
    }
    elseif($name == 'message')
    {
      $this->is_source = false;
      $this->is_translation = false;
    }
    elseif($name == 'source')
    {
      $this->is_source = false;
    }
    elseif($name == 'translation')
    {
      $this->is_translation = false;
    }
  }

  //there was some sort of seg fault
  /*protected function _parseXML($dictionary, $xml)
  {
    if(!$xml_doc = simplexml_load_string($xml))
      return false;

    foreach($xml_doc->context as $context)
    {
      foreach($context->message as $message)
      {
        if($translation = trim((string)$message->translation))
          $dictionary->addEntry((string)$context->name, (string)$message->source, $translation);
        else
          $dictionary->addEntry((string)$context->name, (string)$message->source);
      }
    }
    return true;
  }*/
}

?>

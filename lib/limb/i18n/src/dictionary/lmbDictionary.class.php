<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDictionary.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');

class lmbDictionary
{
  protected $translations;

  function __construct($translations = array())
  {
    $this->translations = $translations;
  }

  function isEmpty()
  {
    return sizeof($this->translations) == 0;
  }

  function translate($context, $sentence)
  {
    if(isset($this->translations[$context][$sentence]) &&
       !empty($this->translations[$context][$sentence]))
      return $this->translations[$context][$sentence];
    else
      return $sentence;
  }

  function addEntry($context, $sentence, $translation = '')
  {
    $this->translations[$context][$sentence] = $translation;
  }

  function hasEntry($context, $sentence)
  {
    return isset($this->translations[$context][$sentence]);
  }

  function hasContext($context)
  {
    return isset($this->translations[$context]);
  }

  function isTranslated($context, $sentence)
  {
    return isset($this->translations[$context][$sentence]) &&
           !empty($this->translations[$context][$sentence]);
  }

  function setTranslations($translations)
  {
    $this->translations = $translations;
  }

  function getTranslations()
  {
    return $this->translations;
  }

  function merge($t)
  {
    $dictionary = new lmbDictionary();
    $dictionary->setTranslations($this->getTranslations());

    $translations = $t->getTranslations();
    foreach($translations as $context => $sentences)
    {
      foreach($sentences as $sentence => $translation)
        $dictionary->addEntry($context, $sentence, $translation);
    }
    return $dictionary;
  }

  function mergeForExistingSections($t)
  {
    $dictionary = new lmbDictionary();
    $dictionary->setTranslations($this->getTranslations());

    $translations = $t->getTranslations();
    foreach($translations as $context => $sentences)
    {
      if(!$this->hasContext($context))
        continue;

      foreach($sentences as $sentence => $translation)
        $dictionary->addEntry($context, $sentence, $translation);
    }

    return $dictionary;
  }

  function getSectionTranslations($section_name)
  {
    $result = array();
    foreach($this->translations as $context => $sentences)
    {
      if(strpos($context, $section_name) !== false)
        $result[$context] = $sentences;
    }
    return $result;
  }

  function isInSync($d)
  {
    foreach($this->translations as $context => $sentences)
    {
      foreach(array_keys($sentences) as $sentence)
      {
        if(!$d->hasEntry($context, $sentence))
          return false;
      }
    }
    return true;
  }
}

?>

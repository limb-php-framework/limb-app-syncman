<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NTools.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/i18n/src/lmbLocale.class.php');

class lmbI18NTools extends lmbAbstractTools
{
  protected $locale;
  protected $dictionaries = array();

  function createLocale($locale_code)
  {
    return lmbLocale :: create($locale_code);
  }

  function getLocale()
  {
    if(!is_object($this->locale))
    {
      $this->locale = lmbLocale :: create('en');
    }

    return $this->locale;
  }

  function setLocale($locale)
  {
    $this->locale = $locale;
  }

  function setDictionary($lang, $dict)
  {
    $this->dictionaries[$lang] = $dict;
  }

  function getDictionary($lang)
  {
    if(!isset($this->dictionaries[$lang]))
    {
      lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
      $this->setDictionary($lang, new lmbDictionary());
    }

    return $this->dictionaries[$lang];
  }

  function tr($context, $text, $lang = null, $attributes = null)
  {
    if($lang)
    {
      $dict = $this->getDictionary($lang);
    }
    else
    {
      $lang = $this->getLocale()->getLanguage();
      $dict = $this->getDictionary($lang);
    }

    $translation = $dict->translate($context, $text);

    if($attributes)
      $translation = str_replace(array_keys($attributes), array_values($attributes), $translation);

    return $translation;
  }
}
?>

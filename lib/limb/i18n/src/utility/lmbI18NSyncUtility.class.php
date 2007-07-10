<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NSyncUtility.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/util/src/system/lmbFsRecursiveIterator.class.php');
lmb_require('limb/i18n/src/dictionary/lmbPHPCodeDictionaryParser.class.php');
lmb_require('limb/i18n/src/dictionary/lmbFileSystemDictionaryLoader.class.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/dictionary/lmbQtXmlDictionaryManager.class.php');
lmb_require('limb/i18n/src/dictionary/lmbTemplateDictionaryParser.class.php');

class lmbI18NSyncUtility
{
  protected $qt_manager;
  protected $response;

  function __construct($prefix, $response)
  {
    $this->qt_manager = new lmbQtXmlDictionaryManager();
    $this->response = $response;
    $this->prefix = $prefix;
  }

  function getNotSynchedTranslationsFiles($translations_dir)
  {
    $original_dict_file = $this->getDictionaryFileName($translations_dir);
    $original_dict = $this->loadDictionary($original_dict_file);

    $iterator = new lmbFsRecursiveIterator($translations_dir);

    $not_synched_files = array();
    for($iterator->rewind();$iterator->valid();$iterator->next())
    {
      $dict_file = $iterator->getPathName();
      if(!$this->_isIteratorItemIsTranslationsFile($iterator)||
         $this->_isSameFiles($original_dict_file, $dict_file))
        continue;

      $dictionary = $this->loadDictionary($dict_file);

      if(!$this->isDictionariesSynched($original_dict, $dictionary))
        $not_synched_files[] = $dict_file;
    }

    return $not_synched_files;
  }

  function synchTranslationsFiles($translations_dir)
  {
    $original_dict_file = $this->getDictionaryFileName($translations_dir);
    $original_dict = $this->loadDictionary($original_dict_file);

    $iterator = new lmbFsRecursiveIterator($translations_dir);

    for($iterator->rewind();$iterator->valid();$iterator->next())
    {
      $dict_file = $iterator->getPathName();
      if(!$this->_isIteratorItemIsTranslationsFile($iterator) ||
         $this->_isSameFiles($original_dict_file, $dict_file))
        continue;

      $translation_dict = $this->loadDictionary($dict_file);
      $this->saveDictionary($original_dict->merge($translation_dict), $dict_file);

      $this->response->write($dict_file . ' merged with ' . $original_dict_file);
    }
  }

  function updateDictionaryFromSourceFiles($translations_dir, $parse_dir)
  {
    $original_dict_file = $this->getDictionaryFileName($translations_dir);
    $original = $this->loadDictionary($original_dict_file);

    $loader = new lmbFileSystemDictionaryLoader();
    $loader->registerFileParser('.html', new lmbTemplateDictionaryParser());
    $loader->registerFileParser('.php', new lmbPHPCodeDictionaryParser());

    $new_dict = new lmbDictionary();
    $iterator = new lmbFsRecursiveIterator($parse_dir);

    $loader->traverse($iterator, $new_dict, $this->response);
    $this->saveDictionary($new_dict->merge($original), $original_dict_file);

    $this->response->write($original_dict_file . ' updated');
  }

  function updateDictionaryFromPackagesSourceFiles($translations_dir, $packages_paths)
  {
    foreach($packages_paths as $package_path)
    {
      $this->response->write('Search in ' . $package_path . "\n");
      $this->updateDictionaryFromSourceFiles($translations_dir, $package_path);
    }
  }

  function updateApplicationDictionary($application_i18n_dir, $packages_paths)
  {
    $application_dictionary = $this->loadDictionaryFromDir($application_i18n_dir);

    foreach($packages_paths as $package_dir)
    {
      if($this->tranlationFileIsSame($package_dir, $application_i18n_dir))
        continue;

      if(!file_exists($package_dir))
        continue;

      $package_dictionary = $this->loadDictionaryFromDir($package_dir);
      if($package_dictionary->isEmpty())
        continue;

      $application_dictionary = $application_dictionary->merge($package_dictionary);
      $this->response->write($this->getDictionaryFileName($application_i18n_dir) . ' file was merged with ' . $this->getDictionaryFileName($package_dir) . "\n");
    }

    $this->saveDictionaryToDir($application_dictionary, $application_i18n_dir);
    $this->response->write($this->getDictionaryFileName($application_i18n_dir) . ' saved'. "\n");
  }

  function tranlationFileIsSame($package_dir, $application_i18n_dir)
  {
    return (strtolower($this->getDictionaryFileName($package_dir)) ==
            strtolower($this->getDictionaryFileName($application_i18n_dir)));
  }

  function updatePackageDictionaries($application_i18n_dir, $packages_paths)
  {
    $application_dictionary = $this->loadDictionaryFromDir($application_i18n_dir);

    foreach($packages_paths as $package_dir)
    {
      $package_dict_file = $this->getDictionaryFileName($package_dir);

      if(!file_exists($package_dict_file))
        continue;

      $package_dictionary = $this->loadDictionary($package_dict_file);
      $package_dictionary = $package_dictionary->mergeForExistingSections($application_dictionary);

      $this->saveDictionaryToDir($package_dictionary, $package_dir);

      $this->response->write('translation.ts file from ' . $package_dir . ' was merged with translation.ts file from' . $application_i18n_dir);
    }
  }

  function isDictionariesSynched($first_dictionary, $second_dictionary)
  {
    if(!$first_dictionary->isInSync($second_dictionary))
      return false;

    elseif(!$second_dictionary->isInSync($first_dictionary))
      return false;

    return true;
  }

  function getDictionaryFileName($dir)
  {
    return lmbFs :: normalizePath($dir . $this->prefix . '/translation.ts');
  }

  function loadDictionaryFromDir($dir)
  {
    return $this->loadDictionary($this->getDictionaryFileName($dir));
  }

  function loadDictionary($file)
  {
    $translation_dict = new lmbDictionary();
    try
    {
      $this->qt_manager->loadFromFile($translation_dict, $file);
    }
    catch(lmbFileNotFoundException $e)
    {
    }

    return $translation_dict;
  }

  function saveDictionary($dictionary, $file)
  {
    $this->qt_manager->saveToFile($dictionary, $file);
  }

  function saveDictionaryToDir($dictionary, $dir)
  {
    $this->_unsureDictionaryExists($dir . $this->prefix);
    $this->saveDictionary($dictionary, $this->getDictionaryFileName($dir));
  }

  protected function _isSameFiles($first_file, $second_file)
  {
    return lmbFs :: normalizePath($first_file) == lmbFs::normalizePath($second_file);
  }

  protected function _isIteratorItemIsTranslationsFile($iterator)
  {
    return $iterator->isFile() && ($iterator->getCurrentFileName() == 'translation.ts');
  }

  protected function _unsureDictionaryExists($dir)
  {
    if(is_dir($dir))
      return;

    lmbFs :: mkdir($dir);
  }
}
?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTranslationDictionaryLoadingFilter.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');
lmb_require('limb/i18n/src/dictionary/lmbQtXmlDictionaryManager.class.php');

class lmbTranslationDictionaryLoadingFilter implements lmbInterceptingFilter
{
  protected $scan_dir;

  function __construct($scan_dir)
  {
    $this->scan_dir = $scan_dir;
  }

  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();

    $iterator = new DirectoryIterator($this->scan_dir);
    $mgr = new lmbQtXmlDictionaryManager();
    $mgr->useCache();

    foreach ($iterator as $self)
    {
      if(!$self->isDot() && $self->isDir())
      {
        $dir = $self->current()->getPathName();

        if(!file_exists($file = $dir . '/translation.ts'))
          continue;

        $dict = new lmbDictionary();
        $mgr->loadFromFile($dict, $file);

        $lang = basename($dir);
        $toolkit->setDictionary($lang, $dict);
      }
    }

    $filter_chain->next();
  }
}

?>

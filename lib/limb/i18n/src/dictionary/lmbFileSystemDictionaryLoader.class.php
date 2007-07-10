<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileSystemDictionaryLoader.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */

class lmbFileSystemDictionaryLoader
{
  protected $parsers = array();

  function registerFileParser($ext, $parser)
  {
    $this->parsers[$ext] = $parser;
  }

  function traverse($iterator, $dictionary, $response)
  {
    for($iterator->rewind(); $iterator->valid(); $iterator->next())
    {
      $item = $iterator->current();
      if($item->isFile())
      {
        $file = $item->getPathName();
        $ext = strrchr(basename($file), '.');
        if(isset($this->parsers[$ext]))
          $this->parsers[$ext]->parseFile($file, $dictionary, $response);
      }
    }
  }
}

?>

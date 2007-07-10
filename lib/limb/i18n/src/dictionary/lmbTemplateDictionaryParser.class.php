<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTemplateDictionaryParser.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/dictionary/lmbBaseDictionaryParser.class.php');


class lmbTemplateDictionaryParser extends lmbBaseDictionaryParser
{
  function parse($code, $dictionary, $response)
  {
    if(preg_match_all('~\{\$[\'"]([^\'"]+)[\'"]\|i18n:[\'"]([^\'"]+)[\'"]~', $code, $matches))
    {
      foreach($matches[1] as $index => $source)
      {
        $response->write('adding ' . $matches[2][$index] . ' : ' . $source);
        $dictionary->addEntry($matches[2][$index], $source);
      }
    }
  }
}

?>

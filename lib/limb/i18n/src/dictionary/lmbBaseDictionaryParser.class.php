<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbBaseDictionaryParser.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */

abstract class lmbBaseDictionaryParser
{
  abstract function parse($code, $dictionary, $response);
  function parseFile($file, $dictionary, $response)
  {
    $this->parse(file_get_contents($file), $dictionary, $response);
  }
}

?>

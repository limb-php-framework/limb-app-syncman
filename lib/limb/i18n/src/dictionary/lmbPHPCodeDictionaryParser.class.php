<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPHPCodeDictionaryParser.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/util/src/util/lmbPHPTokenizer.class.php');
lmb_require('limb/i18n/src/dictionary/lmbBaseDictionaryParser.class.php');

class lmbPHPCodeDictionaryParser extends lmbBaseDictionaryParser
{
  protected $tokenizer;

  function __construct()
  {
    $this->tokenizer = new lmbPHPTokenizer();
  }

  function parse($code, $dictionary, $response)
  {
    $this->tokenizer->input($code);

    while($token = $this->tokenizer->next())
    {
      if(is_array($token) && $token[0] == T_STRING && $token[1] == 'tr')
      {
        if($this->tokenizer->next() == "(")
        {
          $t1 = $this->tokenizer->next();
          $this->tokenizer->next();
          $t2 = $this->tokenizer->next();
          $t3 = $this->tokenizer->next();

          if(is_array($t1) && $t1[0] == T_CONSTANT_ENCAPSED_STRING &&
             is_array($t2) && $t2[0] == T_CONSTANT_ENCAPSED_STRING &&
             ($t3 == ")" || $t3 == ","))
          {
            $context = trim($t1[1], '"\'');
            $source = trim($t2[1], '"\'');
            $response->write('adding ' . $context . ' : ' . $source);
            $dictionary->addEntry($context, $source);
          }
        }
      }
    }
  }
}

?>

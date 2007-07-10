<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: fetch_parameter.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag fetch:PARAM
* @forbid_end_tag
* @parent_tag_class lmbFetchTag
*/
class lmbFetchParameterTag extends WactCompilerTag
{
  function generateContents($code)
  {
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      $this->attributeNodes[$key]->generatePreStatement($code);

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->setAdditionalParam("' . $name . '",');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');');

      $this->attributeNodes[$key]->generatePostStatement($code);
    }
  }
}

?>
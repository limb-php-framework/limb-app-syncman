<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: perform_parameters.tag.php 5018 2007-02-09 15:13:19Z tony $
 * @package    web_app
 */
/**
* @tag perform:params
* @forbid_end_tag
* @parent_tag_class lmbPerformTag
*/
class lmbPerformParametersTag extends WactCompilerTag
{
  function generateContents($code)
  {
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      $this->attributeNodes[$key]->generatePreStatement($code);

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->addParam(');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');' . "\n");

      $this->attributeNodes[$key]->generatePostStatement($code);
    }
  }
}

?>
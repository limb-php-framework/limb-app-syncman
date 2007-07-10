<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: fetch_decorate.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag fetch:DECORATE
* @forbid_end_tag
* @parent_tag_class lmbFetchTag
* @req_const_attributes using
*/
class lmbFetchDecorateTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $decorator = $this->getAttribute('using');
    $code->writePhp($this->parent->getComponentRefCode() .
                      '->addDataSetDecorator("' . $decorator . '");');

    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      if($name == 'using')
        continue;

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->addDataSetDecoratorParameter("' . $decorator . '","' . $name . '",');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');');
    }
  }
}

?>
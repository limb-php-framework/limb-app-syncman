<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequestTransferComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/wact/src/WactTemplate.class.php');

class lmbRequestTransferComponent extends WactRuntimeComponent
{
  public $attributes = array();
  protected $attributes_string = '';//used in replace callback

  function setAttributesToTransfer($attributes)
  {
    $this->attributes = $attributes;
  }

  function appendRequestAttributes(&$content)
  {
    $attributes_to_append = array();

    $toolkit = lmbToolkit :: instance();
    $request = $toolkit->getRequest();

    foreach($this->attributes as $attribute)
    {
      if($value = $request->get($attribute))
        $attributes_to_append[] = $attribute . '=' . addslashes($value);
    }
    if($this->attributes_string = implode('&', $attributes_to_append))
    {
      $callback = array(&$this,'_replaceCallback');
      $content = preg_replace_callback("/(<(?:a|area|form|frame|input)[^>\\w]+(?:href|action|src)=)(?>(\"|'))?((?(2)[^\\2>]+?|[^\\s>]+))((?(2)\\2)[^>]*>)/",
                                       $callback,
                                       $content);
    }
  }

  protected function _replaceCallback($matches)
  {
    $matches[3] = rtrim($matches[3], '?') . '?&' . $this->attributes_string;

    return $matches[1] . $matches[2] . $matches[3] . $matches[4];
  }
}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: site_branch_selector.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag limb:SITE_BRANCH_SELECTOR
*/
class lmbSiteBranchSelectorTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $is_first = true;
    $default = null;
    foreach(array_keys($this->children) as $key)
    {
      if(!is_a($this->children[$key], 'lmbSiteBranchTag'))
        continue;

      $branch = $this->children[$key];

      if($branch->isDefault())
        $default = $branch;

      if(!$path = $branch->getPath())
        continue;

      $if = $is_first ? 'if' : 'elseif';

      $code->writePhp($if . '(preg_match("' . $this->_makePathRegex($path) . '",' .
                                          '$_SERVER["REQUEST_URI"])) {');
      $branch->generateNow($code);
      $code->writePhp('}');

      $is_first = false;
    }

    if($default)
    {
      $code->writePhp('else {');
      $default->generateNow($code);
      $code->writePhp('}');
    }
  }

  function _makePathRegex($path)
  {
    $trans = array("\*" => '.*?',
                   "\?" => '.');
    return '~^' . strtr(preg_quote($path, '~'), $trans) . '~';
  }
}
?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: site_branch.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag limb:SITE_BRANCH
* @parent_tag_class lmbSiteBranchSelectorTag
*/
class lmbSiteBranchTag extends WactSilentCompilerTag
{
  protected $path;
  protected $is_default = false;

  function preParse()
  {
    parent :: preParse();

    if($default = $this->getAttribute('default'))
      $this->is_default = true;

    $this->path = $this->getAttribute('path');
    if(!$this->is_default && !$this->path)
    {
      $this->raiseRequiredAttributeError('path or default');
    }
  }

  function isDefault()
  {
    return $this->is_default;
  }

  function getPath()
  {
    return $this->path;
  }
}

?>
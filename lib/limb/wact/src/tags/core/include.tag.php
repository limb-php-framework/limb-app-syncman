<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: include.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * Include another template into the current template
 * @tag core:INCLUDE
 * @req_const_attributes file
 * @forbid_end_tag
 */
class WactCoreIncludeTag extends WactCompilerTag
{
  /**
  * @param WactCompiler
  **/
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $locator = $compiler->getTemplateLocator();

    if(!$file = $this->getAttribute('file'))
      $this->raiseRequiredAttributeError($file);

    $source_file = $locator->locateSourceTemplate($file);
    if (empty($source_file))
      $this->raiseCompilerError('Template source file not found', array('file_name' => $file));

    if ($this->getBoolAttribute('literal'))
      $this->addChild(new WactTextNode(null, $locator->readTemplateFile($source_file)));
    elseif ($this->getBoolAttribute('source'))
    {
      $this->addChild(new WactTextNode(null, highlight_string($locator->readTemplateFile($source_file), true)));
    }
    else
      $compiler->parseTemplate($file, $this);
  }
}
?>

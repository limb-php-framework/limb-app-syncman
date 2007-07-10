<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_errors.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @tag form:ERRORS
 * @forbid_end_tag
 * @parent_tag_class WactFormTag
 * @req_const_attributes target
 */
class WactFormErrorsTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $form = $this->findParentByClass('WactFormTag');

    $target = $form->getChild($this->getAttribute('target'));

    $code->writePHP($target->getComponentRefCode() . '->registerDataSet(' .
                    $form->getComponentRefCode() . '->getErrorDataSet());');
  }
}
?>

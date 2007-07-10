<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: item.tag.php 5048 2007-02-13 08:40:26Z tony $
 * @package    wact
 */

/**
 * Compile time component for items (rows) in the list
 * @tag list:ITEM
 * @parent_tag_class WactListListTag
 */
class WactListItemTag extends WactRuntimeDatasourceComponentTag
{
  protected $runtimeComponentName = 'WactDatasourceRuntimeComponent';

  function generateContents($code_writer)
  {
    $list = $this->findParentByClass('WactListListTag');

    $separator = $this->findImmediateChildByClass('WactListSeparatorTag');
    if ($separator)
    {
      $ShowSeparator = $code_writer->getTempVariable();
      $code_writer->writePHP('$' . $ShowSeparator . ' = FALSE;' . "\n");
    }

    $code_writer->writePHP('do { ' . "\n");
    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataSource(' .
                    $list->getComponentRefCode() . '->current());' . "\n");

    if ($separator)
    {
      $code_writer->writePHP('if ($' . $ShowSeparator . ') {' . "\n");
      $separator->generateNow($code_writer);
      $code_writer->writePHP('}' . "\n");
      $code_writer->writePHP('$' . $ShowSeparator . ' = TRUE;' . "\n");
    }

    parent::generateContents($code_writer);

    $code_writer->writePHP($list->getComponentRefCode() . '->next();' . "\n");
    $code_writer->writePHP('} while (' . $list->getComponentRefCode() . '->valid());' . "\n");
  }
}
?>
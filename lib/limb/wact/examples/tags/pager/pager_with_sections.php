<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: pager_with_sections.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

$template = new WactTemplate('2/page.html');
include('data.inc.php');
$dataset = new WactArrayIterator($data);

$template->setChildDataSet('php_modules', $dataset);
$pager = $template->getChild('pager', $dataset);

$pager->setPagedDataSet($dataset);
$dataset->paginate($pager->getStartingItem(), $pager->getItemsPerPage());

$template->display();

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: block.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

$page = new WactTemplate('block.html');
$block = $page->getChild('Block3');
$block->show();
$page->display();

?>

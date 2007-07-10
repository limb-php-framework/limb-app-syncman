<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

$page = new WactTemplate('1/page.html');
$page->setChildDatasource('my_form1', $_GET);

$select = $page->getChild('my_select2');
$select->setChoices(array('no_value' => '------',
                          'first' => 'First option',
                          'second' => 'Second option'));
$page->setChildDatasource('my_form2', $_GET);
$page->display();

?>

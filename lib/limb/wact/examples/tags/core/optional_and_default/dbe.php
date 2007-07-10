<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: dbe.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

$page = new WactTemplate('2/page.html');
$root_datasource = array('title' => 'Page title',
                         'data' => array('item' => 'Some item'));
$some_datasource = array('name' => 'John');

$page->registerDatasource($root_datasource);
$page->setChildDatasource('container1', $some_datasource);

$page->display();

?>

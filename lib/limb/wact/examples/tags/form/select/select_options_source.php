<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select_options_source.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

$page = new WactTemplate('2/page.html');

$data = array(array('id' => 10, 'title' => 'First option'),
              array('id' => 20, 'title' => 'Second option'));
$page->setChildDataset('my_options_source', $data);
$page->setChildDatasource('my_form1', $_GET);
$page->display();

?>

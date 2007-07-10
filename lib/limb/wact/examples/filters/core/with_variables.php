<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: with_variables.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

define('MY_TESTING_TEMPLAE_EXAMPLE_CONSTANT', 'Constant value');

$page = new WactTemplate('2/page.html');
$page->set('my_url', 'http://example.com?param=My value');
$page->set('my_hex', 'abc');
$page->set('my_text', '<b>text</b>');
$page->display();

?>

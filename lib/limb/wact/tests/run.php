<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: run.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/tests_runner/common.inc.php');
require_once('limb/tests_runner/src/lmbTestShellUI.class.php');
require_once('limb/tests_runner/src/lmbTestTreeDirNode.class.php');
require_once('limb/tests_runner/src/lmbTestShellUI.class.php');

require_once(dirname(__FILE__) . '/../common.inc.php');

if(PHP_SAPI == 'cli')
{
  $node = new lmbTestTreeDirNode(dirname(__FILE__) . '/cases');
  $group = $node->createTestGroup();
  $group->run(new TextReporter());
}
else
  echo "Please run in Cli mode only!";

?>
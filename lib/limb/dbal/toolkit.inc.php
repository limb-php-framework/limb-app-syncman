<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5032 2007-02-12 13:50:19Z pachanga $
 * @package    dbal
 */


lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/dbal/src/toolkit/lmbDbTools.class.php');
lmbToolkit :: merge(new lmbDbTools());

?>
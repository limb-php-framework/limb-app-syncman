<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
set_magic_quotes_runtime(0);
require_once('limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/wact.inc.php');
require_once(dirname(__FILE__) . '/src/util/popup.inc.php');
require_once(dirname(__FILE__) . '/toolkit.inc.php');
require_once('limb/dbal/common.inc.php');
require_once('limb/active_record/common.inc.php');
require_once('limb/i18n/common.inc.php');

@define('LIMB_HTTP_BASE_PATH', '/');
@define('LIMB_HTTP_SHARED_PATH', '/shared/');

?>
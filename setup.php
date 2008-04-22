<?php
ini_set('output_buffering', 'Off');
ini_set('implicit_flush', 'On');

set_time_limit(0);

set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR .
                 dirname(__FILE__) . '/lib/' . PATH_SEPARATOR);

if(file_exists(dirname(__FILE__) . '/setup.override.php'))
  require_once(dirname(__FILE__) . '/setup.override.php');

@define('LIMB_USE_NATIVE_SESSION_DRIVER', true);
@define('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');
@define('WACT_CONFIG_DIRECTORY', dirname(__FILE__) . '/settings/wact/');

define('SYNCMAN_VERSION', trim(file_get_contents(dirname(__FILE__) . '/VERSION')));

@define('SYNCMAN_PROJECTS_SETTINGS_DIR', dirname(__FILE__) . '/projects/');
@define('SYNCMAN_KEY', '/home/syncman/.ssh/id_dsa');
@define('SYNCMAN_SVN_BIN', 'svn');
@define('SYNCMAN_RSYNC_BIN', 'rsync');
@define('SYNCMAN_SSH_BIN', 'ssh');

@define('LIMB_CONF_INCLUDE_PATH', dirname(__FILE__) . '/settings/');

require_once(dirname(__FILE__) . '/common.inc.php');
?>

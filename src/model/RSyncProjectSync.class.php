<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('src/model/ProjectSync.interface.php');

class RSyncProjectSync extends lmbObject implements ProjectSync
{
  protected $_server_conf;

  function __construct($conf)
  {
    $this->_server_conf = $conf;
  }

  protected function _getRemoteUserWithHost()
  {
    return $this->_server_conf['user'] . '@' . $this->_server_conf['host'];
  }

  function sync($local_dir, $remote_dir, $sync_opts = null)
  {
    $cmd =
      SYNCMAN_RSYNC_BIN . ' -CvzOrlt --include=tags --include=core' .
      ' -e "' . SYNCMAN_SSH_BIN . ' -i ' . $this->_server_conf['key'] . '" ' .
      $sync_opts . ' ' . $local_dir . '/ ' . $this->_getRemoteUserWithHost() . ':' . $remote_dir
    ;

    return $cmd;
  }
}

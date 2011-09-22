<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('src/model/ProjectSync.interface.php');

class FtpProjectSync extends lmbObject implements ProjectSync
{
  protected $_server_conf;

  function __construct($conf)
  {
    $this->_server_conf = $conf;
  }

  function sync($local_dir, $remote_dir, $sync_opts = null)
  {
    $cmd =
      SYNCMAN_FTP_BIN . ' -c "set ftp:list-options -a;
        open ftp://' . $this->_server_conf['user'] . ':' . $this->_server_conf['password'] . '@' . $this->_server_conf['host'] . ';
        mirror --reverse --delete --dereference --only-newer --verbose --exclude=var/' . $sync_opts . ' ' . $local_dir . ' ' . $remote_dir . '"'
    ;

    return $cmd;
  }
}

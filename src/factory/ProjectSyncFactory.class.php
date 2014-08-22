<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('src/model/RSyncProjectSync.class.php');
lmb_require('src/model/FtpProjectSync.class.php');

class ProjectSyncFactory extends lmbObject
{
  static function create($server_conf, $type_sync = null)
  {
    if($type_sync)
    {
      if($type_sync == 'ftp')
        return new FtpProjectSync($server_conf);
    }

    return new RSyncProjectSync($server_conf);
  }
}


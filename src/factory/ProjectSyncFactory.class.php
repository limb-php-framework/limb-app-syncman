<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('src/model/RSyncProjectSync.class.php');
lmb_require('src/model/FtpProjectSync.class.php');

class ProjectSyncFactory extends lmbObject
{
  static function create($conf)
  {
    if(isset($conf['type_sync']))
    {
      if($conf['type_sync'] == 'ftp')
        return new FtpProjectSync($conf['server']);
    }

    return new RSyncProjectSync($conf['server']);
  }
}


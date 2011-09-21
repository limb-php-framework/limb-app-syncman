<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('src/model/RSyncProjectSync.class.php');

class ProjectSyncFactory extends lmbObject
{
  static function create($conf)
  {
    return new RSyncProjectSync($conf['server']);
  }
}

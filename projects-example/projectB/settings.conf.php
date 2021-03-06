<?php

$conf = array(

  'servers' => array(
    array(
      'host' => 'myhost.com',
      'user' => 'syncman',
      'port' => 22,
      'key' => '/home/syncman/.ssh/id_dsa',
      'remote_dir' => '/var/www/projectB',
    ),
    array(
      'host' => 'mysecondhost.com',
      'user' => 'syncman',
      'port' => 22,
      'key' => '/home/syncman/.ssh/id_dsa',
      'remote_dir' => '/var/www/projectB',
    ),
  ),

  'repository' => array(
    //-- allowed types: git, svn
    'type' => 'svn',
    'path' => 'myrepos/projectB/trunk',
  ),

  //-- allowed types: rsync, ftp
  'type_sync' => 'rsync',

  'presync_cmd' => 'php %local_dir%/cli/pre_sync.php',
  'postsync_cmd' => 'ssh -i %key% %user%@%host% \'php %remote_dir%/cli/post_sync.php\'',

  'history' => true,
  'ssh_get_date' => "date +%F_%R",
  'ssh_mkdir' => "mkdir -p \$dir",
  'ssh_ln_edit' => "rm -f \$ln_path; ln -s \$new_dir \$ln_path;",
  'ssh_cp' => "cp -pRT \$dir_of/ \$dir_in/", // для первого раза можно добавить в конце &> /dev/null
  'ssh_ls' => "ls -F --classify -1 \$dir",
  'ssh_preg_dir' => "/(.)+\//",
  'ssh_readlink' => "readlink -v \$link",
);


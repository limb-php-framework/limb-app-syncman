<?php

$conf = array(
  'host' => 'myhost.com',
  'user' => 'syncman',
  'key' => '/home/syncman/.ssh/id_dsa',
  'repository' => 'svn://myrepos/projectB/trunk',
  'remote_dir' => '/var/www/projectB',
  'presync_cmd' => 'php %local_dir%/cli/pre_sync.php',
  'postsync_cmd' => 'ssh -i %key% %user%@%host% \'php %remote_dir%/cli/post_sync.php\'',
  'port' => 22,
  'password' => 'qwerty',
  'history' => true,
  'ssh_get_date' => "date +%F_%R",
  'ssh_mkdir' => "mkdir -p \$dir",
  'ssh_ln_edit' => "rm -f \$ln_path; ln -s \$new_dir \$ln_path;",
  'ssh_cp' => "cp -pRT \$dir_of/ \$dir_in/", // для первого раза можно добавить в конце &> /dev/null
  'ssh_ls' => "ls -F --classify -1 \$dir",
  'ssh_preg_dir' => "/(.)+\//",
  'ssh_readlink' => "readlink -v \$link",
);

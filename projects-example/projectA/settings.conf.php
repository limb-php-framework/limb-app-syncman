<?php

$conf = array(
  'server' => array(
    'host' => 'myhost.com',
    'user' => 'syncman',
    'password' => 'qwerty',
    'key' => '/home/syncman/.ssh/id_dsa',
    'remote_dir' => '/var/www/projectA',
  ),

  'repository' => array(
    'type' => 'git',               //svn | git
    'path' => 'myrepos/projectA/',
  ),

  'type_sync' => 'ftp',            //ftp | rsync; if('type_sync' == 'ftp') 'history' = false;
  'presync_cmd' => 'php %local_dir%/cli/pre_sync.php',
  'postsync_cmd' => 'ssh -i %key% %user%@%host% \'php %remote_dir%/cli/post_sync.php\'',

  'history' => false,
);

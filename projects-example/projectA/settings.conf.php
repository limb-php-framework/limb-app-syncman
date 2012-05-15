<?php

$conf = array(
  'server' => array(
    'host' => 'myhost.com',
    'user' => 'syncman',
    'password' => 'qwerty',
    'remote_dir' => '/var/www/projectA',
  ),

  'repository' => array(
    //-- allowed types: git, svn
    'type' => 'git',
    'path' => 'myrepos/projectA/',
    'branch' => 'project_branch',
  ),

  //-- allowed types: rsync, ftp
  'type_sync' => 'ftp',

  'presync_cmd' => 'php %local_dir%/cli/pre_sync.php',
  //'postsync_cmd' => 'ssh -i %key% %user%@%host% \'php %remote_dir%/cli/post_sync.php\'',
  
  'history' => false,
);

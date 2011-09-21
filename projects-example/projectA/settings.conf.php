<?php

$conf = array(
  'server' => array(
    'host' => 'myhost.com',
    'user' => 'syncman',
    'key' => '/home/syncman/.ssh/id_dsa',
    'remote_dir' => '/var/www/projectA',
  ),

  'repository' => array(
    'type' => 'git',
    'path' => 'myrepos/projectA/',
  ),

  'presync_cmd' => 'php %local_dir%/cli/pre_sync.php',
  'postsync_cmd' => 'ssh -i %key% %user%@%host% \'php %remote_dir%/cli/post_sync.php\'',

  'history' => false,
);

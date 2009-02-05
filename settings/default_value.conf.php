<?php
$conf = array(
  'host' => 'localhost',
  'category' => '---',
  'history' => false,
  // removed directories will be named using the command below
  'ssh_get_date' => "date +%F_%R",
  'ssh_mkdir' => "mkdir -p \$dir",
  // expression for symbolic link creation
  'ssh_ln_edit' => "rm -f \$ln_path; ln -s \$new_dir \$ln_path;",
  // directory copy command
  // NOTE: the copy command should preserve date of file creation, otherwise rsync will overwrite the whole directory on the next sync
  'ssh_cp' => "cp -pRT \$dir_of/ \$dir_in/",
  // directories list command. A directory from this list is used for symbolic link creation with 'ssh_ln_edit' command
  'ssh_ls' => "ls -F --classify -1 \$dir",
  // expression which filters directories
  'ssh_preg_dir' => "/(.)+\//",
  // read the value of a symlink
  'ssh_readlink' => "readlink -v \$link",
);


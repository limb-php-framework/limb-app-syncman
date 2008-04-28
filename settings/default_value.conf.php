<?php
$conf = array(
  'host' => 'localhost',
  'category' => 'no_category',
  'history' => false,
  // удаленные каталоги будут называться по результату выполнения этой команды
  'ssh_get_date' => "date +%F_%R",
  'ssh_mkdir' => "mkdir -p \$dir",
  // создание символьной ссылки; exp: rm /home/user/ssilka; ln -s /var/www/exp /home/user/ssilka;
  'ssh_ln_edit' => "rm -f \$ln_path; ln -s \$new_dir \$ln_path;",
  // копирование каталогов;
  // Важно! команда копирования должна сохранять дату создания файла, иначе rsync будет перезаписывать весь каталог при обновлении 
  'ssh_cp' => "cp -pRT \$dir_of/ \$dir_in/",
  // список каталогов. Полученный каталог из этого списка впоследствии применяется для установки ссылки командой 'ssh_ln_edit'
  'ssh_ls' => "ls -F --classify -1 \$dir",
  // выражение для отбора каталогов из списка файлов
  'ssh_preg_dir' => "/(.)+\//",
  // прочитать значение симольной ссылки
  'ssh_readlink' => "readlink -v \$link",
  );


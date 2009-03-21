#!/usr/bin/env php
<?php
require_once(dirname(__FILE__) . '/../setup.php');
require_once(dirname(__FILE__) . '/../src/syncmanctl.inc.php');

taskman_run($argv, 'usage');

function usage()
{
  echo "Usage:\n syncman.php [-D ARG1 [-D ARG2=value [..]]] cmd\n";
}


#!/usr/bin/env php
<?php

require_once(dirname(__FILE__) . '/../setup.php');
require_once(dirname(__FILE__) . '/../src/taskman.inc.php');
require_once('src/model/Project.class.php');

taskman_run($argv, 'usage');

class CliResponse
{
  function notify($project, $cmd, $log)
  {
    static $cmds = array();
    if(!isset($cmds[$cmd]))
    {
      echo("$cmd\n");
      $cmds[$cmd] = 1;
    }

    echo($log);
  }
  function error($project, $log)
  {
    echo($log);
  }
}

function task_sync($args = array())
{
  if(!$args)
    throw new Exception("No projects passed as args");

  foreach($args as $name)
  {
    $project = Project :: findProject($name);
    $project->sync(new CliResponse());
  }
}

function usage()
{
  echo "Usage:\n syncman.php [-D ARG1 [-D ARG2=value [..]]] cmd\n";
}


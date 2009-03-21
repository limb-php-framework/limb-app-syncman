<?php
require_once(dirname(__FILE__) . '/taskman.inc.php');
require_once(dirname(__FILE__) . '/model/Project.class.php');

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

function task_rexec($args = array())
{
  if(!$args)
    throw new Exception("Args missing");

  $name = array_shift($args);
  $project = Project :: findProject($name);
  $project->rexec(implode(" ", $args), new CliResponse());
}



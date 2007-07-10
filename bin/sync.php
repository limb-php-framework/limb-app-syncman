<?php
if(!isset($argv[1]))
{
  echo("Usage: sync.php <project>\n");
  exit(1);
}
require_once(dirname(__FILE__) . '/../setup.php');
require_once('src/model/Project.class.php');

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
$project = Project :: findProject($argv[1]);
$project->sync(new CliResponse());

?>

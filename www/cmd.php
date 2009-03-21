<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT"); 

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

if(!isset($_GET['cmd']))
  exit();

$names = explode(",", $_GET['cmd']);
foreach($names as $name)
{
  $project = Project :: findProject($name);
  $project->sync(new CliResponse());
}


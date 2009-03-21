<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT"); 

require_once(dirname(__FILE__) . '/../setup.php');
require_once(dirname(__FILE__) . '/../src/syncmanctl.inc.php');

if(!isset($_GET['cmd']))
  exit();

$args = array();
if(isset($_GET['args']))
  $args = explode(',', $_GET['args']);

taskman_collecttasks();
taskman_runtask($_GET['cmd'], $args);

<?php

require_once(dirname(__FILE__) . '/../setup.php');

function findAllProjects()
{
  $projects = array();
  foreach(scandir(SYNCMAN_PROJECTS_SETTINGS_DIR) as $item)
  {
    if($item{0} == '.')
     continue;

    if(!file_exists(SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item . '/settings.ini'))
    {
      echo("project '$item' skip - no file exists 'settings.ini' \n");
      continue;
    }
    elseif(file_exists(SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item . '/settings.conf.php'))
    {
      echo("project '$item' skip - file exists 'settings.conf.php' \n");
      continue;
    }

    $project = new lmbIni(SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item . '/settings.ini');
    $projects[$item] = $project;
  }
  return $projects;
}

function sr($str)
{
  return str_replace("'", "\\'", $str);
}

foreach(findAllProjects() as $name => $project)
{
  $text = "<?php\n\n\$conf = array(\n";
  foreach($project as $key => $value)
    $text .= "  " . "'" . sr($key) . "' => '" . sr($value) . "'" . ",\n";
  $text .= ");\n";
  $dir = SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $name . '/settings.conf.php';
  file_put_contents($dir, $text);
  echo("file create: '{$dir}' \n");
}



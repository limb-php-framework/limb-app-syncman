<?php

lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('src/model/Project.class.php');

class Category extends lmbObject
{
  protected $projects = array();
  protected $name;

  static function findAllCategories()
  {
    $default_category = new Category('default');

    $categories = array();

    foreach(Project :: findAllProjects() as $project)
    {
      if($name = $project->getCategory())
      {
        if(!isset($categories[$name]))
        {
          $category = new Category($name);
          $categories[$name] = $category;
        }
        else
          $category = $categories[$name];
      }
      else
      {
        $categories[$default_category->getName()] = $default_category;
        $category = $default_category;
      }

      $category->addProject($project);
    }
    ksort($categories);
    return array_values($categories);
  }

  static function findCategory($name)
  {
    foreach(self :: findAllCategories() as $cat)
    {
      if($cat->getName() == $name)
        return $cat;
    }
  }

  function __construct($name)
  {
    $this->name = $name;
  }

  function addProject($project)
  {
    $this->projects[] = $project;
  }
}


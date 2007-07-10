<?php
lmb_require('src/model/Category.class.php');
lmb_require('src/model/Project.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class ProjectsController extends lmbController
{
  function doDisplay()
  {
    $this->view->findChild('categories')->registerDataset(Category :: findAllCategories());
  }

  function doSimple()
  {
    $this->view->findChild('projects')->registerDataset(Project :: findAllProjects());
  }

  function doSync()
  {
    $project = Project :: findProject($this->request->get('id'));
  }

  function doSimpleSync()
  {
    $project = new Project();
  }

  function doPerformDiff()
  {
    $project = Project :: findProject($this->request->get('id'));
    $project->diff($project->getLastSyncRev(), 'HEAD', $this);
  }

  function doStartSync()
  {
    if($ids = $this->request->getArray('ids'))
    {
      foreach($ids as $id)
      {
        $this->_syncProject($id);
      }
    }
    elseif($id = $this->request->get('id'))
      $this->_syncProject($id);

    $this->_out("<hr><b>Done!(check logs for errors)</b><br><br>");
    $this->_out("<script>window.top.opener.location.reload()</script>");
  }

  protected function _syncProject($id)
  {
    if($project = Project :: findProject($id))
    {
      $this->_out("<hr><b>================ Syncing " . $project->getName(). " ================</b>");
      $project->sync($this);
    }
  }

  function doUnlock()
  {
    $project = Project :: findProject($this->request->get('id'));
    $project->unlock();
  }

  function notify($project, $cmd, $log)
  {
    static $cmds = array();
    if(!isset($cmds[$cmd]))
    {
      $this->_out("<hr><b>$cmd</b><br>");
      $cmds[$cmd] = 1;
    }

    $this->_out('<small>' . nl2br($log) . '</small>');
  }

  function error($project, $log)
  {
    $this->_out("<hr><b style='color:red'>$log</b><br>");
  }

  protected function _out($msg)
  {
    echo $msg;
    echo "<script>window.scrollTo(0,document.height);</script>";
    flush();
  }
}

?>

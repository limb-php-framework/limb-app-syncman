<?php
lmb_require('src/model/Category.class.php');
lmb_require('src/model/Project.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class ProjectsController extends lmbController
{
  protected $last_cmd;

  function doDisplay()
  {
    $this->view->set('category', Category :: findAllCategories());

    if(isset($_COOKIE['category_detail']))
      $this->view->set('category_detail', $_COOKIE['category_detail']);
    else
      $this->view->set('category_detail', array());

    if(isset($_COOKIE['project_detail']))
      $this->view->set('project_detail', $_COOKIE['project_detail']);
    else
      $this->view->set('project_detail', array());
  }

  function doSimple()
  {
    $this->view->set('projects', Project :: findAllProjects());
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
    
    $repository_rev = $project->getRepositoryRev();
    $last_sync_rev = $project->getLastSyncRev();
    
    $this->_out(
      "<hr><b>
      <table style='font-weight: bold'>
      <tr><td>Production Rev :</td><td>" . $last_sync_rev . "</td></tr>
      <tr><td>Origin Rev :</td><td>" . $repository_rev . "</td></tr>
      </table>
      </b>"
    );    
    
    $project->diff($last_sync_rev, $repository_rev, $this);
    
    if($last_sync_rev != $repository_rev)
      $this->_out("<hr><b> Need To be Updated </b>");
    
    $id_href = $this->request->get('id');
    $this->_reload($id_href);
  }

  function doPerformLog()
  {
    $project = Project :: findProject($this->request->get('id'));
    
    $repository_rev = $project->getRepositoryRev();
    $last_sync_rev = $project->getLastSyncRev();
    
    $this->_out(
      "<hr><b>
      <table style='font-weight: bold'>
      <tr><td>Production Rev :</td><td>" . $last_sync_rev . "</td></tr>
      <tr><td>Origin Rev :</td><td>" . $repository_rev . "</td></tr>
      </table>
      </b>"
    );
    
    $project->log($last_sync_rev, $repository_rev, $this);
    
    if($last_sync_rev != $repository_rev)
      $this->_out("<hr><b> Need To be Updated </b>");
    
    $id_href = $this->request->get('id');
    $this->_reload($id_href);
  }

  function doCheckoutBranch()
  {
    $project = Project :: findProject($this->request->get('id'));

    if($project->getRepository()->getType() != 'git')
      return;

    if(!$this->request->hasPost())
      return;

    $branch = $this->request->getPost('branch', 'master');
    $new_project = $project->checkoutBranch($branch, $this);

    $this->redirect(LIMB_HTTP_BASE_PATH . 'projects/sync?id=' . $new_project->getName());
  }

  function doStartSync()
  {
    $id_href = '';
    if($ids = $this->request->getArray('ids')) {}
    elseif($id = $this->request->get('id'))
    {
      $ids = array(0 => $id);
      $id_href = $id;
    }

    if(is_array($ids))
    {
      foreach($ids as $id)
      {
        $this->_syncProject($id);
      }
    }

    $this->_out("<hr><b>Done!(check logs for errors)</b><br><br>");
    $this->_reload($id_href);
  }
  
  protected function _reload($id_href)
  {
    $this->_out("<script>
      window.top.opener.location.reload();
      window.top.opener.location.hash = '#project_{$id_href}';
    </script>");
  }

  protected function _syncProject($id)
  {
    if($project = Project :: findProject($id))
    {
      $this->_out("<hr><b>================ Syncing " . $project->getName(). " ================</b>");

      $ignore_externals = (bool)$this->request->getGet('ignore-externals', false);
      $project->sync($this, $ignore_externals);
    }
  }

  function doUnlock()
  {
    $project = Project :: findProject($this->request->get('id'));
    $project->unlock();
  }

  function doDetail()
  {
    if($category = $this->request->get('category'))
    {
      $value = isset($_COOKIE['category_detail'][$category]) ? $_COOKIE['category_detail'][$category] : 0;

      $value = (int) (! $value);
      $this->response->setcookie("category_detail[{$category}]", $value, $value ? time()+3600*24*30 : time(), "/");

      if($this->request->getInteger('js') !== 1)
        $this->redirect(array('controller' => 'projects', 'action' => 'display'));
      else
      {
        if($value == 1)
          $this->view->set('item', Category :: findCategory($category));
        else
        {
          $this->response->commit();
          exit();
        }
      }
    }
  }

  function doRollback()
  {
    $this->useForm('form');
    $form_date = array();
    $project = Project :: findProject($this->request->get('id'));
    $this->view->set('project', $project);

    if(!$project->getHistory())
    {
      $this->flashError("For project '{$project->getName()}' history is off!");
      $this->redirect(array('controller' => 'projects', 'action' => 'display'));
      return;
    }

    if($this->request->hasPost())
    {
      $form_date['new_current_ln'] = $this->request->get('new_current_ln');
      if(!$project->setCurrentLn($form_date['new_current_ln']))
      {
        $this->flashError('Change not save!');
      }
      else
        $this->flashMessage('Change save!');
    }

    $dir_list = array();
    foreach ($project->getListHistory() as $value)
      $dir_list[$value] = $value;
    $this->view->set('dir_list', $dir_list);

    $this->setFormDatasource($form_date);

    foreach ($project->errors as $error)
      $this->flashError($error);
  }

  function notify($project, $cmd, $log)
  {
    if($this->last_cmd !== $cmd)
    {
      $this->_out("<hr><b>$cmd</b><br>");
      $this->last_cmd = $cmd;
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

<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('src/model/Repository.interface.php');

class GitRepository extends lmbObject implements Repository
{
  protected $_path;
  protected $_branch;

  function __construct($path, $branch = 'master')
  {
    $this->_path = $path;
    $this->_branch = $branch;
  }

  function getPath()
  {
    return $this->_path;
  }  
   
  function getType()
  {
    return 'git';
  }
  
  function getBranch()
  {
    return $this->_branch; 
  }

  function getFetchProjectCmd($wc_path)
  {
    $path = $this->getPath();
    $branch = $this->getBranch();
    
    $cmd =
      "mkdir {$wc_path} && " .
      "cd {$wc_path} && " .
      SYNCMAN_GIT_BIN . " init . && " .
      SYNCMAN_GIT_BIN . " remote add origin {$path} && " .
      SYNCMAN_GIT_BIN . " fetch origin && " .
      SYNCMAN_GIT_BIN . " branch --track {$branch} origin/{$branch} && " .
      SYNCMAN_GIT_BIN . " checkout {$branch} && " .
      SYNCMAN_GIT_BIN . " pull origin {$branch}"
    ;

    return $cmd;
  }

  function getUpdateCmd($wc_path, $ignore_externals = false)
  {
    $cmd =
      'cd ' . $wc_path . ' && ' .
      SYNCMAN_GIT_BIN . ' fetch origin && ' .
      SYNCMAN_GIT_BIN . ' pull origin ' . $this->getBranch()
    ;

    return $cmd;
  }

  function getDiffCmd($wc_path, $revision_wc, $resivion_remote)
  {
    $cmd =
      'cd ' . $wc_path . ' && ' .
      SYNCMAN_GIT_BIN . ' fetch origin && ' .
      SYNCMAN_GIT_BIN . ' diff --name-only ' . $resivion_remote
    ;
      
    return $cmd;
  }
  
  function getLogCmd($wc_path, $revision_wc, $resivion_remote)
  {
    $cmd =
      'cd ' . $wc_path . ' && ' .
      SYNCMAN_GIT_BIN . ' fetch origin && ' .
      SYNCMAN_GIT_BIN . ' log ' . $revision_wc . '..' . $resivion_remote
    ;
      
    return $cmd;
  }

  function getLastCommitCmd($wc_path, $is_remote = false)
  {
    preg_match('~commit\s*(\w+)\s+~i', $this->_gitInfo($wc_path, $is_remote), $m); 
    return isset($m[1]) ? substr($m[1],0,10) : null;
  }

  protected function _gitInfo($wc_path, $is_remote = false)
  {
    if ($is_remote)
    {
      $cmd =
        'cd ' . $wc_path . ' && ' .
        SYNCMAN_GIT_BIN . ' fetch origin && ' .
        SYNCMAN_GIT_BIN . ' log --max-count=1 origin/' . $this->getBranch()
      ;
    }
    else
    {
      $cmd =
       'cd ' . $wc_path . ' && ' .
        SYNCMAN_GIT_BIN . ' log --max-count=1'
      ;  
    }

    return `$cmd`;
  }
}

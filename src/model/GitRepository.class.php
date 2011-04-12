<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('src/model/Repository.interface.php');

class GitRepository extends lmbObject implements Repository
{
  protected $_path;

  function __construct($path)
  {
    $this->_path = $path;
  }

  function getPath()
  {
    return $this->_path;
  }  
   
  function getType()
  {
    return 'git';
  }

  function getFetchProjectCmd($wc_path)
  {
    return SYNCMAN_GIT_BIN . ' clone ' . $this->getPath() . ' ' . $wc_path;
  }

  function getUpdateCmd($wc_path, $ignore_externals)
  {
    $cmd =
      'cd ' . $wc_path . ' && ' .
      SYNCMAN_GIT_BIN . ' pull origin master'
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
        SYNCMAN_GIT_BIN . ' log --max-count=1 origin master'
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

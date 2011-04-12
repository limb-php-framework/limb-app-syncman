<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('src/model/Repository.interface.php');

class SvnRepository extends lmbObject implements Repository
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
    return 'svn';
  }

  function getFetchProjectCmd($wc_path)
  {
    return SYNCMAN_SVN_BIN . ' co --non-interactive ' . $this->getPath() . ' ' . $wc_path;
  }

  function getUpdateCmd($wc_path, $ignore_externals = false)
  {
    $cmd = SYNCMAN_SVN_BIN . ' up --non-interactive ';

    if ($ignore_externals)
      $cmd .= ' --ignore-externals ';

     $cmd .= $wc_path;

    return $cmd;
  }

  function getDiffCmd($wc_path, $revision_wc, $resivion_remote)
  {
    return SYNCMAN_SVN_BIN . ' diff --summarize -r' . $revision_wc . ':HEAD ' . $this->getPath();
  }
  
  function getLogCmd($wc_path, $revision_wc, $resivion_remote)
  {
    return SYNCMAN_SVN_BIN . ' log -r' . $revision_wc . ':HEAD ' . $this->getPath();
  }

  function getLastCommitCmd($wc_path, $is_remote = false)
  {
    $repo_path = $is_remote ? $this->getPath() : $wc_path;
    preg_match('~Revision:\s*(\d+)\s+~i', $this->_svnInfo($repo_path), $m);
    
    return isset($m[1]) ? $m[1] : null;
  }

  protected function _svnInfo($repo_path, $is_remote = false)
  {
    $svn = SYNCMAN_SVN_BIN;
    return `$svn info $repo_path`;
  }
}

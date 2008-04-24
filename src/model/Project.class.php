<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

class Project extends lmbObject
{
  protected $listener;
  protected $sync_date;
  protected $sync_rev;
  protected static $default_value;

  function __construct($name)
  {
    $this->setName($name);

    if(!isset(self::$default_value))
      self::$default_value = lmbToolkit :: instance()->getConf('default_value.ini');

    foreach(self::$default_value as $key => $value)
      if(!isset($this[$key]))
        $this->set($key, $value);
  }

  static function createFromIni($name, $ini)
  {
    $project = new Project($name);

    $ini = parse_ini_file($ini);
    foreach($ini as $key => $value)
      $project->set($key, $value);

    return $project;
  }

  function sync($listener = null)
  {
    $this->lock();

    $this->listener = $listener;

    try
    {
      $this->_removeOldLog();

      if(!$this->sync_date)
        $this->_resetSyncDate();

      if(!$this->existsWc())
        $this->_execCmd($this->getCheckoutWcCmd());
      else
        $this->_execCmd($this->getUpdateWcCmd());

      $this->_syncLocalDirWithWc();

      $this->_execCmd($this->getPresyncCmd());

      $this->_resetSyncRev();

      $this->_execCmd($this->getSyncCmd());

      $this->_execCmd($this->getPostsyncCmd());

      $this->_updateLastSyncDate();
      $this->_updateLastRev();
    }
    catch(Exception $e)
    {
      $this->listener->error($this, $e->getMessage());
    }

    $this->unlock();
  }

  function diff($revision1, $revision2 = 'HEAD', $listener = null)
  {
    $this->listener = $listener;

    $this->_removeOldDiffLog();
    $this->_execCmd($this->getDiffCmd($revision1, $revision2), $this->getDiffFile());
  }

  static function findAllProjects()
  {
    $projects = array();
    foreach(scandir(SYNCMAN_PROJECTS_SETTINGS_DIR) as $item)
    {
      if($item{0} == '.')
        continue;

      $project = self :: createFromIni($item, SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item . '/settings.ini');
      $projects[] = $project;
    }
    return $projects;
  }

  static function findProject($name)
  {
    foreach(self :: findAllProjects() as $project)
    {
      if($project->getName() == $name)
        return $project;
    }
  }

  function lock()
  {
    touch($this->getLockFile());
  }

  function unlock()
  {
    if($this->getIsLocked())
      unlink($this->getLockFile());
  }

  function getLocalDir()
  {
    lmbFs :: mkdir(LIMB_VAR_DIR . '/projects/');
    return LIMB_VAR_DIR . '/projects/' .$this->getName();
  }

  function getSettingsDir()
  {
    return SYNCMAN_PROJECTS_SETTINGS_DIR . $this->getName();
  }

  function getSyncCmd()
  {
    if($cmd = $this->_getFilled('sync_cmd'))
      return $cmd;

    $rsync_opts = $this->_getRaw('rsync_opts');

    return  SYNCMAN_RSYNC_BIN . ' -CvzOrlt --include=tags --include=core -e "' . SYNCMAN_SSH_BIN . ' -i ' . $this->getKey() . '" ' . $rsync_opts . ' ' .
      $this->getLocalDir(). '/ ' . $this->getRemoteUserWithHost() . ':' . $this->getRemoteDir();
  }

  function getPresyncCmd()
  {
    return $this->_getFilled('presync_cmd');
  }

  function getPostsyncCmd()
  {
    return $this->_getFilled('postsync_cmd');
  }

  function getRemoteUserWithHost()
  {
    return $this->getUser() . '@' . $this->getHost();
  }

  function existsWc()
  {
    return is_dir($this->getWc());
  }

  function getCheckoutWcCmd()
  {
    return SYNCMAN_SVN_BIN . ' co --non-interactive ' . $this->getRepository() . ' ' . $this->getWc();
  }

  function getUpdateWcCmd()
  {
    return SYNCMAN_SVN_BIN . ' up --non-interactive ' . $this->getWc();
  }

  function getWcRev()
  {
    return $this->_getRev($this->getWc());
  }

  function getRepositoryRev()
  {
    return $this->_getRev($this->getRepository());
  }


  protected function _getRev($path)
  {
    preg_match('~Revision:\s*(\d+)\s+~i', $this->_svnInfo($path), $m);
    return isset($m[1]) ? $m[1] : null;
  }

  protected function _svnInfo($path)
  {
    $svn = SYNCMAN_SVN_BIN;
    return `$svn info $path`;
  }

  function getWc()
  {
    lmbFs :: mkdir(LIMB_VAR_DIR . '/wc/');
    return LIMB_VAR_DIR . '/wc/' . $this->getName();
  }

  function getIsChanged()
  {
    return $this->getLastSyncRev() != $this->getRepositoryRev();
  }

  function getIsStale()
  {
    return $this->getWcRev() != $this->getRepositoryRev();
  }

  function getLastSyncDateFile()
  {
    return LIMB_VAR_DIR . '/.'. $this->getName() . '.date';
  }

  function getLastSyncRevFile()
  {
    return LIMB_VAR_DIR . '/.'. $this->getName() . '.rev';
  }

  function getLastSyncDate()
  {
    return $this->_getFileContents($this->getLastSyncDateFile());
  }

  function getLastSyncRev()
  {
    return $this->_getFileContents($this->getLastSyncRevFile());
  }

  protected function _updateLastRev()
  {
    file_put_contents($this->getLastSyncRevFile(), $this->sync_rev);
  }

  protected function _updateLastSyncDate()
  {
    file_put_contents($this->getLastSyncDateFile(), $this->sync_date);
  }

  function getLogFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.' . $this->sync_date . '.log';
  }

  function getLockFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.lock';
  }

  function getIsLocked()
  {
    return file_exists($this->getLockFile());
  }

  protected function _syncLocalDirWithWc()
  {
    $this->_execCmd(SYNCMAN_RSYNC_BIN . ' -CaO --include=tags --include=core --delete ' . $this->getWc() . '/ ' . $this->getLocalDir());
  }

  protected function _resetSyncDate()
  {
    $this->sync_date = date('Y-m-d_H-i-s');
  }

  function setSyncDate($sync_date)
  {
    $this->sync_date = $sync_date;
  }

  function getSyncDate()
  {
    if(!$this->sync_date)
      $this->_resetSyncDate();

    return $this->sync_date;
  }

  protected function _resetSyncRev()
  {
    $this->sync_rev = $this->getWcRev();
  }

  function getDiffFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.diff';
  }

  function getDiffCmd($revision1, $revision2 = 'HEAD')
  {
    return SYNCMAN_SVN_BIN . ' diff --summarize ' . '-r' . $revision1 . ':' . $revision2 . ' ' . $this->getRepository();
  }

  protected function _execCmd($cmd)
  {
    if(!$cmd)
      return;

    $proc = popen("$cmd 2>&1", 'r');

    $fh = fopen($this->getLogFile(), 'a');
    $log = '';
    while(!feof($proc))
    {
     $t_log = fread($proc, 8192);
     $log .= $t_log;
     fwrite($fh, $t_log);
     if($this->listener)
       $this->listener->notify($this, $cmd, $t_log);
    }

    $res = pclose($proc);
    fclose($fh);

    if($res != 0)
      throw new Exception("Command '$cmd' execution failed, return status is '$res'");

    return $log;
  }

  protected function _getFileContents($file)
  {
    if(!file_exists($file))
      return '';

    return trim(file_get_contents($file));
  }

  protected function _fillTemplate($str)
  {
    return str_replace(array('%wc%',
                             '%host%',
                             '%user%',
                             '%local_dir%',
                             '%remote_dir%',
                             '%key%',
                             '%settings_dir%',
                             ),
                       array($this->getWc(),
                             $this->getHost(),
                             $this->getUser(),
                             $this->getLocalDir(),
                             $this->getRemoteDir(),
                             $this->getKey(),
                             $this->getSettingsDir(),
                             ),
                             $str);
  }

  protected function _removeOldLog()
  {
    if(file_exists($this->getLogFile()))
      unlink($this->getLogFile());
  }

  protected function _removeOldDiffLog()
  {
    if(file_exists($this->getDiffFile()))
     unlink($this->getDiffFile());
  }

  protected function _getFilled($name)
  {
    $value = parent :: _getRaw($name);
    return $this->_fillTemplate($value);
  }
}

?>

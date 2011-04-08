<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('src/factory/RepositoryFactory.class.php');

class Project extends lmbObject
{
  protected static $default_conf;

  protected $listener;
  protected $sync_date;
  protected $sync_rev;
  protected $connection;
  protected $orig_conf;
  protected $repository;

  public $errors = array();

  function __construct($name, lmbConf $conf)
  {
    $this->setName($name);

    if(!isset(self::$default_conf))
      self::$default_conf = lmbToolkit :: instance()->getConf('default_value.conf.php');

    foreach(self::$default_conf as $key => $value)
      $this->set($key, $value);

    foreach($conf as $key => $value)
      $this->set($key, $value);

    $repository_factory = new RepositoryFactory();
    $this->repository = $repository_factory->create($conf);

    $this->orig_conf = $conf;
  }

  static function createFromConf($name, $conf)
  {
    return new Project($name, new lmbConf($conf));
  }

  function sync($listener = null, $ignore_externals = false)
  {
    $this->lock();

    $this->listener = $listener;

    try
    {
      $this->_removeOldLog();

      if(!$this->sync_date)
        $this->_resetSyncDate();

      if(!$this->existsWc())
        $this->_execCmd($this->getFetchProjectCmd());
      else
        $this->_execCmd($this->getUpdateCmd($ignore_externals));

      if($this->needPresync())
      {
        $this->_syncLocalDirWithWc();
        $this->_execCmd($this->getPresyncCmd());
      }

      $this->_resetSyncRev();

      if($this->getHistory())
        $this->_syncHistory();

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

  function rexec($cmd, $listener = null)
  {
    $this->lock();

    $this->listener = $listener;

    try
    {
      $this->_execCmd(SYNCMAN_SSH_BIN . ' -i ' . $this->getKey() . " " . $this->getRemoteUserWithHost() . " '" . $cmd . "'");
    }
    catch(Exception $e)
    {
      $this->listener->error($this, $e->getMessage());
    }

    $this->unlock();
  }

  //assumes we have taskman based project
  function rtask($cmd, $listener = null)
  {
    $taskman_script = $this->_getRaw('taskman_script');
    if(!$taskman_script)
      throw new Exception("'taskman_script' property is missing");

    $this->rexec("$taskman_script $cmd", $listener);
  }

  function diff($revision_wc, $resivion_remote, $listener = null)
  {
    $this->listener = $listener;

    $this->_removeOldDiffLog();
    $this->_execCmd($this->repository->getDiffCmd($this->getWc(), $revision_wc, $resivion_remote), $this->getDiffFile());
  }

  static function findAllProjects()
  {
    $projects = array();
    foreach(scandir(SYNCMAN_PROJECTS_SETTINGS_DIR) as $item)
    {
      if($item{0} == '.' || !is_dir(SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item))
        continue;

      $project = self :: createFromConf($item, SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item . '/settings.conf.php');
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

  function getExportedConfig()
  {
    return var_export($this->orig_conf->export(), true);
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

  function needPresync()
  {
    if(!$this->has('presync') || $this->_getRaw('presync') == true)
      return true;
    return false;
  }

  function getSharedWc()
  {
    return $this->_getRaw('shared_wc');
  }

  function getPresyncCmd()
  {
    return $this->_getFilled('presync_cmd');
  }

  function getPostsyncCmd()
  {
    return $this->_getFilled('postsync_cmd');
  }

  function getRemoteDir()
  {
    return $this->_getRaw('remote_dir') . ($this->getHistory() ? '/current' : '');
  }

  function getRemoteUserWithHost()
  {
    return $this->getUser() . '@' . $this->getHost();
  }

  function existsWc()
  {
    return is_dir($this->getWc());
  }

  function getFetchProjectCmd()
  {
    if($cmd = $this->_getFilled('checkout_wc_cmd'))
      return $cmd;

    return $this->repository->getFetchProjectCmd($this->getWc());
  }

  function getUpdateCmd($ignore_externals = false)
  {
    if($cmd = $this->_getFilled('update_wc_cmd'))
      return $cmd;

    return $this->repository->getUpdateCmd($this->getWc(), $ignore_externals);
  }

  function getWcRev()
  {
    return $this->repository->getLastCommitCmd($this->getWc(), $is_remote = false);
  }

  function getRepositoryRev()
  {
    return $this->repository->getLastCommitCmd($this->getWc(), $is_remote = true);
  }

  function getWc()
  {
    lmbFs :: mkdir(LIMB_VAR_DIR . '/wc/');
    $shared_wc = $this->getSharedWc();
    if($shared_wc)
      return LIMB_VAR_DIR . '/wc/' . $shared_wc;
    else
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

  protected function _ssh2Connection()
  {
    if(function_exists('ssh2_connect'))
    {
      $this->connection = ssh2_connect($this->getHost(), $this->getPort());
      if(!$this->connection)
        throw new Exception("No connection to the ssh server!");
      if(!ssh2_auth_pubkey_file($this->connection, $this->getUser(),
                            $this->getKey().'.pub',
                            $this->getKey(), $this->getPassword()))
        throw new Exception("Public Key Authentication Failed!");
    }
    else
      $this->connection = true;
  }

  protected function _syncHistory()
  {
    if(!$this->connection)
      $this->_ssh2Connection();


    $new_dir = trim($this->_execCmdSsh($this->getSshGetDate()));

    $new_dir = $this->_getRaw('remote_dir').'/'.$new_dir;
    $cmd = str_replace('$dir', $new_dir, $this->getSshMkdir());
    $this->_execCmdSsh($cmd);

    $cmd = str_replace(array('$dir_of', '$dir_in'),
                       array($this->getRemoteDir(), $new_dir),
                       $this->getSshCp());
    try
    {
      $this->_execCmdSsh($cmd);
    }
    catch (Exception $e)
    {
      $this->_writeOutputInLog("'cp' execution failed => initialization file structure", "no cmd");
    }

    $cmd = str_replace(array('$ln_path', '$new_dir'),
                       array($this->getRemoteDir(), $new_dir),
                       $this->getSshLnEdit());
    $this->_execCmdSsh($cmd);
  }

  protected function _writeOutputInLog($proc, $cmd)
  {
    if($this->listener)
      $this->listener->notify($this, $cmd, '');

    $fh = fopen($this->getLogFile(), 'a');
    $log = '';
    if(is_string($proc))
    {
      $log = $proc;
      fwrite($fh, $log);
        if($this->listener)
          $this->listener->notify($this, $cmd, $log);
    }
    else
    {
      while($t_log = fgets($proc))
      {
        $log .= $t_log;
        fwrite($fh, $t_log);
        if($this->listener)
          $this->listener->notify($this, $cmd, $t_log);
      }
    }
    fclose($fh);
    return $log;
  }

  protected function _execCmdSshNoException($cmd, &$out='')
  {
    try
    {
      if(!$this->connection)
        $this->_ssh2Connection();
      $out = $this->_execCmdSsh($cmd);
      return true;
    }
    catch(Exception $e)
    {
      $this->errors[] = $e->getMessage();
      return false;
    }
  }

  protected function _execCmdSsh($cmd)
  {
    $log = '';
    if(!$cmd)
      return;
    if(function_exists('ssh2_exec'))
    {
      if(!$this->connection)
        throw new Exception("No connection to the ssh server!");
      $proc = ssh2_exec($this->connection, $cmd);
      if($proc == false)
        throw new Exception("Ssh command '$cmd' execution failed!");
      else
      {
        $err_stream = ssh2_fetch_stream($proc, 1);

        stream_set_blocking($err_stream, true);
        stream_set_blocking($proc, true);

        $err_log = $this->_writeOutputInLog($err_stream, "ssh-err@:~$ ".$cmd);
        $log = $this->_writeOutputInLog($proc, "ssh-out@:~$ ".$cmd);

        if($err_log !== '')
          throw new Exception("Ssh command '$cmd' execution failed! Out: '$err_log'");
      }
    }
    else
      $this->_execCmd(SYNCMAN_SSH_BIN . ' -i ' . $this->getKey() . " " . $this->getRemoteUserWithHost() . " " . $cmd);
    return $log;
  }

  protected function _execCmd($cmd)
  {
    if(!$cmd)
      return;

    $proc = popen("$cmd 2>&1", 'r');

    $log = $this->_writeOutputInLog($proc, $cmd);

    $res = pclose($proc);

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

  function getListHistory()
  {
    $cmd = str_replace('$dir', $this->_getRaw('remote_dir'), $this->getSshLs());
    if($this->_execCmdSshNoException($cmd, $ls))
    {
      $ls = explode("\n", $ls);
      foreach($ls as $key => $value)
        if(!preg_match($this->getSshPregDir(), $value))
          unset($ls[$key]);
      return $ls;
    }
    return null;
  }

  function getCurrentLn()
  {
    $cmd = str_replace('$link', $this->getRemoteDir(), $this->getSshReadlink());
    if($this->_execCmdSshNoException($cmd, $current_ln))
      return trim($current_ln);
    return null;
  }

  function setCurrentLn($new_dir)
  {
    $cmd = str_replace(array('$ln_path', '$new_dir'),
                       array($this->getRemoteDir(), $new_dir),
                       $this->getSshLnEdit());
    return $this->_execCmdSshNoException($cmd);
  }
}

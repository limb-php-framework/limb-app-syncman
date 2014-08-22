<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('src/factory/RepositoryFactory.class.php');
lmb_require('src/factory/ProjectSyncFactory.class.php');

class Project extends lmbObject
{
  protected static $default_conf;

  protected $listener;
  protected $sync_date;
  protected $sync_rev;
  protected $connection;
  protected $orig_conf;
  protected $repository;
  protected $servers;
  protected $current_server;

  public $errors = array();

  function __construct($name, lmbConf $conf)
  {
    $this->setName($name);

    if(!isset(self::$default_conf))
      self::$default_conf = lmbToolkit :: instance()->getConf('default_value.conf.php');

    $new_default_conf = $this->_prepareConf(self::$default_conf);
    $this->_setProjectParams($new_default_conf);

    $prepared_conf = $this->_prepareConf($conf);
    $this->_setProjectParams($prepared_conf);

    $this->repository = RepositoryFactory :: create($prepared_conf);

    $this->orig_conf = $conf;
  }

  function getServer()
  {
    if ($this->current_server)
      return $this->current_server;

    $first_server = array_shift(array_slice($this->servers, 0, 1));
    $this->setServer($first_server);

    return $first_server;
  }

  function setServer($server)
  {
    $this->current_server = $server;
  }

  function getServerParam($param)
  {
    $server = $this->getServer();
    return $server[$param];
  }

  protected function _prepareConf($conf)
  {
    foreach($conf as $key => $value)
      $new_conf[$key] = $value;

    if(!isset($new_conf['server']) && !isset($new_conf['servers']))
    {
      $new_conf['server'] = array();

      $new_conf = $this->_prepareConfServerOption($new_conf, 'user');
      $new_conf = $this->_prepareConfServerOption($new_conf, 'host');
      $new_conf = $this->_prepareConfServerOption($new_conf, 'port');
      $new_conf = $this->_prepareConfServerOption($new_conf, 'key');
      $new_conf = $this->_prepareConfServerOption($new_conf, 'password');
      $new_conf = $this->_prepareConfServerOption($new_conf, 'remote_dir');
    }

    if(!isset($new_conf['servers']))
      $new_conf['servers'] = array();

    if(isset($new_conf['server']))
    {
      if($new_conf['server'])
        $new_conf['servers'][] = $new_conf['server'];

      unset($new_conf['server']);
    }

    return $new_conf;
  }

  protected function _prepareConfServerOption($new_conf, $option)
  {
    if(isset($new_conf[$option]))
    {
      $new_conf['server'][$option] = $new_conf[$option];
      unset($new_conf[$option]);
    }

    return $new_conf;
  }

  protected function _setProjectParams($conf)
  {
    foreach($conf as $key => $value)
      $this->set($key, $value);
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

      foreach($this->servers as $server)
      {
        $this->setServer($server);

        $this->listener->notify($this, '================ ' . $this->getServerParam('host') . ' ================', null);

        if($this->needPresync())
        {
          $this->_syncLocalDirWithWc();
          $this->_execCmd($this->getPresyncCmd());
        }

        $this->_resetSyncRev();

        if($this->needHistory())
          $this->_syncHistory();

        $this->_execCmd($this->getSyncCmd());

        $this->_execCmd($this->getPostsyncCmd());

        $this->_updateLastSyncDate();
        $this->_updateLastRev();
        $this->_updateOriginRev($this->getRepositoryRev());
      }
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
      $this->_execCmd(SYNCMAN_SSH_BIN . ' -i ' . $this->getServerParam('key') . " " . $this->getRemoteUserWithHost() . " '" . $cmd . "'");
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

    $this->_removeOldDiff();

    if($revision_wc == null)
      echo "<hr><b> Operation impossible. Working copy doesn't exist </b>";
    else
      $this->_execCmd($this->repository->getDiffCmd($this->getWc(), $revision_wc, $resivion_remote), $this->getLastDiffFile());

    $this->_updateOriginRev($resivion_remote);
  }

  function log($revision_wc, $resivion_remote, $listener = null)
  {
    $this->listener = $listener;

    $this->_removeOldDiffLog();

    if($revision_wc == null)
      echo "<hr><b> Operation impossible. Working copy doesn't exist </b>";
    else
      $this->_execCmd($this->repository->getLogCmd($this->getWc(), $revision_wc, $resivion_remote), $this->getLastDiffLogFile());

    $this->_updateOriginRev($resivion_remote);
  }

  function checkoutBranch($branch, $listener = null)
  {
    $this->listener = $listener;
    $settings_dir = $this->getSettingsDir();
    $name = $this->getName();

    $content = '<?php' . PHP_EOL . '$conf[\'repository\'][\'branch\'] = \'' . $branch . '\';' . PHP_EOL;
    file_put_contents($settings_dir . '/settings.conf.override.php', $content);

    $this->_removeLocalDir();
    $this->_removeWC();

    return self :: createFromConf($name, $settings_dir . '/settings.conf.php');
  }

  static function findAllProjects()
  {
    $projects = array();
    foreach(scandir(SYNCMAN_PROJECTS_SETTINGS_DIR) as $item)
    {
      if($item{0} == '.' || !is_dir(SYNCMAN_PROJECTS_SETTINGS_DIR . '/' . $item))
        continue;

      $project = self :: createFromConf($item, SYNCMAN_PROJECTS_SETTINGS_DIR . $item . '/settings.conf.php');
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

  protected function _removeLocalDir()
  {
    return lmbFs :: rm($this->getLocalDir());
  }

  function getSettingsDir()
  {
    return SYNCMAN_PROJECTS_SETTINGS_DIR . $this->getName();
  }

  function getSyncCmd()
  {
    if($cmd = $this->_getFilled('sync_cmd'))
      return $cmd;

    if(isset($this->rsync_opts) && (!isset($this->type_sync) || $this->type_sync == 'rsync'))
      $sync_opts = $this->_getRaw('rsync_opts');
    else
      $sync_opts = $this->_getRaw('sync_opts');

    $type_sync = isset($this->type_sync) ? $this->type_sync : null;
    $project_sync = ProjectSyncFactory :: create($this->getServer(), $type_sync);

    return $project_sync->sync($this->getLocalDir(), $this->getRemoteDir(), $sync_opts);
  }

  function needPresync()
  {
    if(!$this->has('presync') || $this->_getRaw('presync') == true)
      return true;
    return false;
  }

  function needHistory()
  {
    return ($this->getHistory() && (!isset($this->type_sync) || $this->type_sync != 'ftp'));
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
    return $this->getServerParam('remote_dir') . ($this->getHistory() ? '/current' : '');
  }

  function getRemoteUserWithHost()
  {
    return $this->getServerParam('user') . '@' . $this->getServerParam('host');
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

  protected function _removeWC()
  {
    if($this->existsWc())
      return lmbFs :: rm($this->getWc());

    return true;
  }

  function getIsChanged()
  {
    $last_sync_rev = $this->getLastSyncRev();
    $origin_rev = $this->getOriginRev();

    if($last_sync_rev == '' && $origin_rev == '')
      return true;
    else
      return $last_sync_rev != $origin_rev;
  }

  function getIsStale()
  {
    return $this->getWcRev() != $this->getOriginRev();
  }

  function getLastSyncDateFile()
  {
    return LIMB_VAR_DIR . '/.'. $this->getName() . '.date';
  }

  function getLastSyncRevFile()
  {
    return LIMB_VAR_DIR . '/.'. $this->getName() . '.rev';
  }

  function getOriginRevFile()
  {
    return LIMB_VAR_DIR . '/.'. $this->getName() . '.origin.rev';
  }

  function getLastSyncDate()
  {
    return $this->_getFileContents($this->getLastSyncDateFile());
  }

  function getLastSyncRev()
  {
    return $this->_getFileContents($this->getLastSyncRevFile());
  }

  function getOriginRev()
  {
    return $this->_getFileContents($this->getOriginRevFile());
  }

  function _updateOriginRev($resivion_remote)
  {
    file_put_contents($this->getOriginRevFile(), $resivion_remote);
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

  function getLastDiffFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.diff';
  }

  function getLastDiffLogFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.diff.log';
  }

  protected function _ssh2Connection()
  {
    if(function_exists('ssh2_connect'))
    {
      $this->connection = ssh2_connect($this->getServerParam('host'), $this->getServerParam('port'));
      if(!$this->connection)
        throw new Exception("No connection to the ssh server!");
      if(!ssh2_auth_pubkey_file(
          $this->connection, $this->getServerParam('user'),
          $this->getServerParam('key') . '.pub',
          $this->getServerParam('key'), $this->getServerParam('password')))
        throw new Exception("Public Key Authentication Failed!");
    }
    else
    {
      $this->connection = true;
    }
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

  protected function _writeOutputInLog($proc, $cmd, $log_file = null)
  {
    if($this->listener)
      $this->listener->notify($this, $cmd, '');

    $log_file = ($log_file === null) ? $this->getLogFile() : $log_file;
    $fh = fopen($log_file, 'a');
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
    {
      $this->_execCmd(SYNCMAN_SSH_BIN . ' -i ' . $this->getServerParam('key') . " " . $this->getRemoteUserWithHost() . " " . $cmd);
    }

    return $log;
  }

  protected function _execCmd($cmd, $log_file = null)
  {
    if(!$cmd)
      return;

    $proc = popen("$cmd 2>&1", 'r');

    $log = $this->_writeOutputInLog($proc, $cmd, $log_file);

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
    if(!$str)
      return null;

    return str_replace(array('%wc%',
                             '%host%',
                             '%user%',
                             '%local_dir%',
                             '%remote_dir%',
                             '%key%',
                             '%settings_dir%',
                             ),
                       array($this->getWc(),
                             $this->getServerParam('host'),
                             $this->getServerParam('user'),
                             $this->getLocalDir(),
                             $this->getRemoteDir(),
                             $this->getServerParam('key'),
                             $this->getSettingsDir(),
                             ),
                       $str);
  }

  protected function _removeOldLog()
  {
    if(file_exists($this->getLogFile()))
      unlink($this->getLogFile());
  }

  protected function _removeOldDiff()
  {
    if(file_exists($this->getLastDiffFile()))
     unlink($this->getLastDiffFile());
  }

  protected function _removeOldDiffLog()
  {
    if(file_exists($this->getLastDiffLogFile()))
      unlink($this->getLastDiffLogFile());
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

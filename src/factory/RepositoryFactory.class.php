<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('src/model/SvnRepository.class.php');
lmb_require('src/model/GitRepository.class.php');

class RepositoryFactory extends lmbObject
{
  function create(lmbConf $conf)
  {
    //-- BC for usage svn repository path as string
    if (!is_array($conf['repository']))
      return new SvnRepository($conf['repository']);

    if ($conf['repository']['type'] == 'svn')
      return new SvnRepository($conf['repository']['path']);

    return new GitRepository($conf['repository']['path']);
  }
}


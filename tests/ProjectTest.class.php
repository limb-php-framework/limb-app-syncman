<?php
require_once(dirname(__FILE__) . '/../setup.php');
require_once(dirname(__FILE__) . '/../src/model/Project.class.php');

class ProjectTest extends UnitTestCase
{
  function testCreateFromIni()
  {
    $ini = $this->_createIni("
    repository=svn://svn.bit/
    key=/id_dsa/key
    host=srv2.0x00.ru
    user=syncman
    remote_dir=/var/www/mydir
    presync_cmd=php %local_dir%/cli/pre_sync.php
    postsync_cmd=ssh -i %key% %user%@%host% 'php %remote_dir%/cli/post_sync.php'
    sync_cmd=rsync -Cavz -e 'ssh -i %key%' %user%@%host%
    ");

    $project = Project :: createFromIni('foo', $ini);

    $this->assertEqual($project->getName(), 'foo');
    $this->assertEqual($project->getHost(), 'srv2.0x00.ru');
    $this->assertEqual($project->getUser(), 'syncman');
    $this->assertEqual($project->getRemoteDir(), '/var/www/mydir');
    $this->assertEqual($project->getKey(), '/id_dsa/key');
    $this->assertEqual($project->getPresyncCmd(), 'php ' . $project->getLocalDir() . '/cli/pre_sync.php');
    $this->assertEqual($project->getPostsyncCmd(), "ssh -i /id_dsa/key syncman@srv2.0x00.ru 'php /var/www/mydir/cli/post_sync.php'");
    $this->assertEqual($project->getSyncCmd(), "rsync -Cavz -e 'ssh -i /id_dsa/key' syncman@srv2.0x00.ru");
  }

  function _createIni($content)
  {
    $file = '/tmp/project.ini';
    file_put_contents($file, $content);
    return $file;
  }
}

?>

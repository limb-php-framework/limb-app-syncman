<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionMessageBoxMessagesFetcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbSessionMessageBoxMessagesFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $result = array();

    $message_box = lmbToolkit :: instance()->getSessionMessageBox();
    foreach($message_box->getMessages() as $message)
      $result[] = array('message' => $message);

    $message_box->resetMessages();

    return new lmbArrayDataset($result);
  }
}
?>

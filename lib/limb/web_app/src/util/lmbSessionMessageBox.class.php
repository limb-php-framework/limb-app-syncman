<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionMessageBox.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/util/lmbMessageBox.class.php');

class lmbSessionMessageBox extends lmbMessageBox
{
  static function create($session)
  {
    if(!is_object($obj = $session->get(__CLASS__)))
    {
      $obj = new lmbSessionMessageBox();
      $session->set(__CLASS__, $obj);
    }
    return $obj;
  }
}

?>
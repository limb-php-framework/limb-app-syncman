<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_errors.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require 'limb/wact/common.inc.php';
require 'limb/wact/src/WactTemplate.class.php';

$page = new WactTemplate('2/page.html');

if($_SERVER['REQUEST_METHOD'] == 'POST')
  _processPost($page);

$page->display();

function _processPost($page)
{
  $form = $page->getChild('my_form');

  if(isset($_POST['my_input']) && $_POST['my_input'])
  {
    $page->set('text', $_POST['my_input']);
    $form->registerDatasource($_POST);
  }
  else
  {
    $error_list = new WactFormErrorList();
    $error_list->addError('"{field1}" must have a value', $fields = array('field1' => 'my_input'));
    $form->setErrors($error_list);
  }
}
?>

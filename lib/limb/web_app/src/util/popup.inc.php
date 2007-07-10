<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: popup.inc.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/command/lmbClosePopupDialogCommand.class.php');

function registerClosePopupCallback($form)
{
  $form->registerOnValidCallback(new lmbClosePopupDialogCommand(), 'perform');
}

function performPopupDialogCommand(lmbFormCommand $form_command)
{
  $form_command->registerOnValidCallback(new lmbClosePopupDialogCommand(), 'perform');
  return $form_command->perform();
}

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeToolkitTools.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */

class lmbCompositeToolkitTools implements lmbToolkitTools
{
  protected $toolkit;
  protected $tools = array();

  function __construct($tools)
  {
    if(is_array($tools))
      $this->tools = $tools;
    else
      $this->tools = func_get_args();
  }

  function __clone()
  {
    foreach($this->tools as $key => $tools)
      $this->tools[$key] = clone($tools);
  }

  function getToolsSignatures()
  {
    $result = array();
    foreach($this->tools as $tools)
    {
      $signatures = $tools->getToolsSignatures();
      $result = array_merge($result, $signatures);
    }
    return $result;
  }
}
?>

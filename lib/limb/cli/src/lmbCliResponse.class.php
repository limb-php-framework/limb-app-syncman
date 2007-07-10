<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCliResponse.class.php 4988 2007-02-08 15:35:19Z pachanga $
 * @package    cli
 */
class lmbCliResponse
{
  protected $verbose = false;

  function __construct($verbose = false)
  {
    $this->verbose = $verbose;
  }

  function setVerbose($verbose)
  {
    $this->verbose = $verbose;
  }

  function write($msg)
  {
    if($this->verbose)
      echo $msg . "\n";
  }
}

?>

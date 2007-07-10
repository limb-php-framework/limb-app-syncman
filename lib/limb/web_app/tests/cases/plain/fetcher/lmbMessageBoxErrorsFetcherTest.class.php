<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMessageBoxErrorsFetcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbMessageBoxErrorsFetcher.class.php');

class lmbMessageBoxErrorsFetcherTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testGetDataset()
  {
    $this->toolkit->getMessageBox()->addError('Error1');
    $this->toolkit->getMessageBox()->addError('Error2');

    $fetcher = new lmbMessageBoxErrorsFetcher();
    $rs = $fetcher->getDataset();

    $rs->rewind();
    $this->assertEqual($rs->current()->get('error'), 'Error1');
    $rs->next();
    $this->assertEqual($rs->current()->get('error'), 'Error2');
  }

}
?>

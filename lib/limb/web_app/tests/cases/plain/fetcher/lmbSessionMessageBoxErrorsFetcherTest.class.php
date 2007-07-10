<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionMessageBoxErrorsFetcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbSessionMessageBoxErrorsFetcher.class.php');
lmb_require('limb/web_app/tests/cases/lmbWebAppTestCase.class.php');

class lmbSessionMessageBoxErrorsFetcherTest extends lmbWebAppTestCase
{
  function setUp()
  {
    parent :: setUp();
    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
    parent :: tearDown();
  }

  function _cleanUp()
  {
     $this->toolkit->getSessionMessageBox()->reset();
  }

  function testGetDataset()
  {
    $this->toolkit->getSessionMessageBox()->addError('Error1');
    $this->toolkit->getSessionMessageBox()->addError('Error2');

    $fetcher = new lmbSessionMessageBoxErrorsFetcher();
    $rs = $fetcher->getDataset();

    $rs->rewind();
    $this->assertEqual($rs->current()->get('error'), 'Error1');
    $rs->next();
    $this->assertEqual($rs->current()->get('error'), 'Error2');
  }

  function testFetcherResetsErrorsList()
  {
    $this->toolkit->getSessionMessageBox()->addError('Error1');
    $this->toolkit->getSessionMessageBox()->addError('Error2');

    $fetcher = new lmbSessionMessageBoxErrorsFetcher();
    $rs = $fetcher->getDataset();

    $rs = $fetcher->getDataset();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }
}
?>

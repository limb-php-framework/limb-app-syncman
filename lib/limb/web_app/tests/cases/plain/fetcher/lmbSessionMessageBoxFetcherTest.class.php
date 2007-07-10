<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionMessageBoxFetcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbSessionMessageBoxFetcher.class.php');
lmb_require('limb/web_app/tests/cases/lmbWebAppTestCase.class.php');

class lmbSessionMessageBoxFetcherTest extends lmbWebAppTestCase
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

  function testGetDatasetErrorsComeFirst()
  {
    $this->toolkit->getSessionMessageBox()->addMessage('Message1');
    $this->toolkit->getSessionMessageBox()->addError('Error2');

    $fetcher = new lmbSessionMessageBoxFetcher();
    $rs = $fetcher->getDataset();

    $rs->rewind();
    $this->assertFalse($rs->current()->get('is_message'));
    $this->assertTrue($rs->current()->get('is_error'));
    $this->assertEqual($rs->current()->get('text'), 'Error2');

    $rs->next();
    $this->assertTrue($rs->current()->get('is_message'));
    $this->assertFalse($rs->current()->get('is_error'));
    $this->assertEqual($rs->current()->get('text'), 'Message1');

  }

  function testFetcherResetsMessagesList()
  {
    $this->toolkit->getSessionMessageBox()->addMessage('Message1');
    $this->toolkit->getSessionMessageBox()->addError('Error2');

    $fetcher = new lmbSessionMessageBoxFetcher();
    $rs = $fetcher->getDataset();

    $rs = $fetcher->getDataset();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }
}
?>

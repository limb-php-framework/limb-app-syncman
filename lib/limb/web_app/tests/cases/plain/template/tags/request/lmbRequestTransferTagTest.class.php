<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequestTransferTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

class lmbRequestTransferTagTest extends lmbWactTestCase
{
  function testTransferNoSuchAttributesInRequest()
  {
    $request = $this->toolkit->getRequest();

    $request->set('p1', 'test1');

    $template = '<limb:REQUEST_TRANSFER attributes="p2,p3">' .
                '<form action="/some/path">' .
                '</form>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_no_attrs.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_no_attrs.html');
    $this->assertEqual($page->capture(), '<form action="/some/path"></form>');
  }

  function testTransferCheckQuotes()
  {
    $request = $this->toolkit->getRequest();
    $request->set('p1', 'test1');

    $template = '<limb:REQUEST_TRANSFER attributes="p1">' .
                '<form action="/some/path">' .
                '<area src=\'http://test/root\'>' .
                '<a href="/test"></a>' .
                '</area><frame src=whatever /></form>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_quotes.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_quotes.html');

    //somehow WACT sets quotes automatically...
    $expected = '<form action="/some/path?&p1=test1">' .
                '<area src="http://test/root?&p1=test1">' .
                '<a href="/test?&p1=test1"></a>' .
                '</area><frame src="whatever?&p1=test1" /></form>';

    $this->assertEqual($page->capture(), $expected);
  }

  function testTransferAddSlashes()
  {
    $request = $this->toolkit->getRequest();
    $request->set('p1', 'test"test');

    $template = '<limb:REQUEST_TRANSFER attributes="p1">' .
                '<form action="/some/path"></form>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_slash.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_slash.html');
    $this->assertEqual($page->capture(), '<form action="/some/path?&p1=test\"test"></form>');
  }

  function testTransferForm()
  {
    $request = $this->toolkit->getRequest();
    $request->set('p1', 'test1');
    $request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<form action="/some/path"></form>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_form.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_form.html');
    $this->assertEqual($page->capture(), '<form action="/some/path?&p1=test1&p2=test2"></form>');
  }

  function testTransferHref()
  {
    $request = $this->toolkit->getRequest();
    $request->set('p1', 'test1');
    $request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<a href="/some/path">content</a>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_a.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_a.html');
    $this->assertEqual($page->capture(), '<a href="/some/path?&p1=test1&p2=test2">content</a>');
  }

  function testTransferArea()
  {
    $request = $this->toolkit->getRequest();
    $request->set('p1', 'test1');
    $request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<area src="/some/path">content\ncontent2</area>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_area.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_area.html');
    $this->assertEqual($page->capture(), '<area src="/some/path?&p1=test1&p2=test2">content\ncontent2</area>');
  }

  function testTransferFrame()
  {
    $request = $this->toolkit->getRequest();
    $request->set('p1', 'test1');
    $request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<frame src="/some/path"/>' .
                '</limb:REQUEST_TRANSFER>';

    $this->registerTestingTemplate('/limb/request_transfer_frame.html', $template);

    $page = $this->initTemplate('/limb/request_transfer_frame.html');
    $this->assertEqual($page->capture(), '<frame src="/some/path?&p1=test1&p2=test2" />');
  }
}
?>

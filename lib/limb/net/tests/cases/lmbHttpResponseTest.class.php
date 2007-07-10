<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbHttpResponseTest.class.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */
lmb_require('limb/net/src/lmbHttpRedirectStrategy.class.php');

Mock :: generatePartial(
  'lmbHttpResponse',
  'SpecialMockResponse',
  array('_sendHeader', '_sendString', '_sendFile')
);

Mock::generate('lmbHttpRedirectStrategy', 'MockHttpRedirectStrategy');

class lmbHttpResponseTest extends UnitTestCase
{
  var $response;

  function setUp()
  {
    $this->response = new SpecialMockResponse();
  }

  function testHeader()
  {
    $this->response->start();

    $this->response->expectArgumentsAt(0, '_sendHeader', array("Location:to-some-place"));
    $this->response->expectArgumentsAt(1, '_sendHeader', array("Location:to-some-place2"));

    $this->response->header("Location:to-some-place");
    $this->response->header("Location:to-some-place2");

    $this->response->commit();
  }

  function testIsEmpty()
  {
    $this->assertTrue($this->response->isEmpty());
  }

  function testIsEmptyHeadersSent()
  {
    $this->response->start();
    $this->response->header('test');
    $this->assertTrue($this->response->isEmpty());
    $this->response->commit();
  }

  function testNotEmptyRedirect()
  {
    $this->response->start();
    $this->response->redirect("/to/some/place?t=1&amp;t=2");
    $this->assertFalse($this->response->isEmpty());
    $this->response->commit();
  }

  function testNotEmptyResponseString()
  {
    $this->response->start();
    $this->response->write("<b>wow</b>");
    $this->assertFalse($this->response->isEmpty());
    $this->response->commit();
  }

  function testNotEmptyReadfile()
  {
    $this->response->start();
    $this->response->readfile("/path/to/file");
    $this->assertFalse($this->response->isEmpty());
    $this->response->commit();
  }

  function testNotEmpty304Status()
  {
    $this->response->start();
    $this->response->header('HTTP/1.0 304 Not Modified');
    $this->assertFalse($this->response->isEmpty());
    $this->response->commit();
  }

  function testNotEmpty412Status()
  {
    $this->response->start();
    $this->response->header('HTTP/1.1 412 Precondition Failed');
    $this->assertFalse($this->response->isEmpty());
    $this->response->commit();
  }

  function testHeadersNotSent()
  {
    $this->assertFalse($this->response->headersSent());
  }

  function testFileNotSent()
  {
    $this->assertFalse($this->response->fileSent());
  }

  function testFileSent()
  {
    $this->response->start();
    $this->response->readfile('somefile');
    $this->assertTrue($this->response->fileSent());
    $this->response->commit();
  }

  function testHeadersSent()
  {
    $this->response->start();
    $this->response->header("Location:to-some-place");
    $this->assertTrue($this->response->headersSent());
    $this->response->commit();
  }

  function testRedirect()
  {
    $this->response->start();
    $this->assertFalse($this->response->isRedirected());

    $this->response->redirect($path = 'some path');

    $this->assertTrue($this->response->isRedirected());
    $this->assertEqual($this->response->getRedirectedPath(), $path);
    $this->response->commit();
  }

  function testRedirectOnlyOnce()
  {
    $this->response->start();

    $strategy = new MockHttpRedirectStrategy();

    $this->response->setRedirectStrategy($strategy);

    $this->assertFalse($this->response->isRedirected());

    $strategy->expectOnce('redirect');
    $this->response->redirect($path = 'some path');
    $this->response->redirect('some other path');

    $this->assertTrue($this->response->isRedirected());
    $this->assertEqual($this->response->getRedirectedPath(), $path);

    $this->response->commit();
  }

  function testWrite()
  {
    $this->response->start();
    $this->response->expectOnce('_sendString', array("<b>wow</b>"));

    $this->response->write("<b>wow</b>");
    $this->response->commit();
  }

  function testReadfile()
  {
    $this->response->start();
    $this->response->expectOnce('_sendFile', array("/path/to/file"));

    $this->response->readfile("/path/to/file");
    $this->response->commit();
  }

  function testGetResponseDefaultStatus()
  {
    $this->assertEqual($this->response->getStatus(), 200);
  }

  function testGetResponseStatusHttp()
  {
    $this->response->start();
    $this->response->header('HTTP/1.0  304 ');
    $this->assertEqual($this->response->getStatus(), 304);

    $this->response->header('HTTP/1.1  412');
    $this->assertEqual($this->response->getStatus(), 412);
    $this->response->commit();
  }

  function testGetUnknownDirective()
  {
    $this->assertFalse($this->response->getDirective('cache-control'));
  }

  function testGetDirective()
  {
    $this->response->start();
    $this->response->header('Cache-Control: protected, max-age=0, must-revalidate');
    $this->assertEqual($this->response->getDirective('cache-control'), 'protected, max-age=0, must-revalidate');

    $this->response->header('Cache-Control :    protected, max-age=10  ');
    $this->assertEqual($this->response->getDirective('cache-control'), 'protected, max-age=10');
    $this->response->commit();
  }

  function testGetContentDefaultType()
  {
    $this->assertEqual($this->response->getContentType(), 'text/html');
  }

  function testGetContentType()
  {
    $this->response->start();
    $this->response->header('Content-Type: image/png');
    $this->assertEqual($this->response->getContentType(), 'image/png');

    $this->response->header('Content-Type: application/rss+xml');
    $this->assertEqual($this->response->getContentType(), 'application/rss+xml');
    $this->response->commit();
  }
}

?>
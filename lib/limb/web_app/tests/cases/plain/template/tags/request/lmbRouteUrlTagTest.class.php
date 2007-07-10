<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRouteUrlTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');

class lmbRouteUrlTagTest extends lmbWactTestCase
{
  function testAllParamsAreStaticAndUseNamedRoute()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:news,action:archive" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_static_attributes.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_static_attributes.html');

    $expected = '<a onclick="something" href="/news/archive"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function _testWithDynamicParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:{$controller},action:{$action}" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_dynamic.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_dynamic.html');
    $page->set('controller', $controller = 'news');
    $page->set('action', $action = 'archive');

    $expected = '<a onclick="something" href="/news/archive"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testWithComplexDBEParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:{$#request.controller},action:{$#request.action}" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_dynamic_complex_dbe.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_dynamic_complex_dbe.html');

    $dataspace = new lmbDataspace();
    $dataspace->set('controller', $controller = 'news');
    $dataspace->set('action', $action = 'archive');
    $page->set('request', $dataspace);

    $expected = '<a onclick="something" href="/news/archive"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testTryToGuessRoute()
  {
    $config = array('blog' => array('path' => '/blog/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url params="controller:news,action:archive"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_no_route_name.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_no_route_name.html');

    $expected = '<a href="/news/archive"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testExtraHrefChunk()
  {
    $config = array('blog' => array('path' => '/blog/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url params="controller:news,action:archive" extra="?id=50"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_extra_href_chunk.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_extra_href_chunk.html');

    $expected = '<a href="/news/archive?id=50"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function _createRoutes($config)
  {
    $routes = new lmbRoutes($config);
    $this->toolkit->setRoutes($routes);
    return $routes;
  }
}
?>

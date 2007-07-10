<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIpFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

class lmbIpFilterTest extends lmbWactTestCase
{
  function testFilter()
  {
    $template = '{$"-1"|ip}';

    $this->registerTestingTemplate('/limb/ip.html', $template);

    $page = $this->initTemplate('/limb/ip.html');

    $this->assertEqual($page->capture(), '255.255.255.255');
  }

  function testFilterDBE()
  {
    $template = '<core:SET var="-1">{$var|ip}';

    $this->registerTestingTemplate('/limb/ip2.html', $template);

    $page = $this->initTemplate('/limb/ip2.html');

    $this->assertEqual($page->capture(), '255.255.255.255');
  }

}
?>

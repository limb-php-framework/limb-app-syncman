<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTemplateExpressionsTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */


class WactTemplateExpressionsTest extends WactTemplateTestCase
{
  function testIntegerConstant()
  {
    $template = '{$23}';

    $this->registerTestingTemplate('/template/expression/integer.html', $template);
    $page = $this->initTemplate('/template/expression/integer.html');
    $output = $page->capture();
    $this->assertEqual($output, '23');
  }

  function testFloatConstant()
  {
    $template = '{$1.5}';

    $this->registerTestingTemplate('/template/expression/float.html', $template);
    $page = $this->initTemplate('/template/expression/float.html');
    $output = $page->capture();
    $this->assertEqual($output, '1.5');
  }

  function testStringConstant()
  {
    $template = '{$"hello"}';

    $this->registerTestingTemplate('/template/expression/string.html', $template);
    $page = $this->initTemplate('/template/expression/string.html');
    $output = $page->capture();
    $this->assertEqual($output, 'hello');
  }

  function testDelimiterStringConstant()
  {
    $template = '{$"}"}';

    $this->registerTestingTemplate('/template/expression/string-del.html', $template);
    $page = $this->initTemplate('/template/expression/string-del.html');
    $output = $page->capture();
    $this->assertEqual($output, '}');
  }

  function testSingleQuoteStringConstant()
  {
    $template = '{$"\'"|raw}';

    $this->registerTestingTemplate('/template/expression/quote-single.html', $template);
    $page = $this->initTemplate('/template/expression/quote-single.html');
    $output = $page->capture();
    $this->assertEqual($output, '\'');
  }

  function testDoubleQuoteStringConstant()
  {
    $template = '{$\'"\'|raw}';

    $this->registerTestingTemplate('/template/expression/quote-double.html', $template);
    $page = $this->initTemplate('/template/expression/quote-double.html');
    $output = $page->capture();
    $this->assertEqual($output, '"');
  }

  function testPath()
  {
    $template = '{$Data.Var}';

    $this->registerTestingTemplate('/template/expression/path.html', $template);
    $page = $this->initTemplate('/template/expression/path.html');
    $page->set('Data', array('Var' => 'test'));
    $output = $page->capture();
    $this->assertEqual($output, 'test');
  }

  function testLongPath()
  {
    $template = '{$Data.More.Var}';

    $this->registerTestingTemplate('/template/expression/longpath.html', $template);
    $page = $this->initTemplate('/template/expression/longpath.html');
    $page->set('Data', array('More' => array('Var' => 'test')));
    $output = $page->capture();
    $this->assertEqual($output, 'test');
  }
}
?>
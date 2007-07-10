<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorDecorateTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbPagedDatasetDecorator.class.php');

class TestingIteratorDecoratorTagDecorator extends lmbPagedDatasetDecorator
{
  var $prefix1 = '!!!';

  function setPrefix1($prefix)
  {
    $this->prefix1 = $prefix;
  }

  function current()
  {
    $record = parent :: current();
    $record['full'] = $this->prefix1 . $record['child'];
    return $record;
  }
}

class lmbIteratorDecorateTagTest extends lmbWactTestCase
{
  function testApplyDecorator()
  {
    $data = array (
      array ('name'=> 'joe', 'children' => array(array('child' => 'enny'),
                                                 array('child' => 'harry'))),
      array ('name'=> 'ivan', 'children' => array(array('child' => 'ann'),
                                                  array('child' => 'boris'))));

    $dataset = new lmbArrayDataset($data);

    $template = '<list:LIST id="fathers"><list:ITEM>{$name}:'.
                '<iterator:TRANSFER from="children" target="children">' .
                  '<iterator:decorate using="TestingIteratorDecoratorTagDecorator">'.
                '</iterator:TRANSFER>' .
                '<list:LIST id="children"><list:ITEM>{$full},</list:ITEM></list:LIST>'.
                '|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/iterator_decorator_simple.html', $template);

    $page = $this->initTemplate('/limb/iterator_decorator_simple.html');

    $list = $page->getChild('fathers');
    $list->registerDataset($dataset);

    $this->assertEqual(trim($page->capture()), 'joe:!!!enny,!!!harry,|ivan:!!!ann,!!!boris,|');
  }
}
?>

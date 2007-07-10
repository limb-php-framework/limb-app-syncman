<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorTransferTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');

class lmbIteratorTransferTagTest extends lmbWactTestCase
{
  function testTransfer()
  {
    $data = array (array ('name'=> 'joe', 'children' => array(array('child' => 'enny'),
                                                              array('child' => 'harry'))),
                      array ('name'=> 'ivan', 'children' => array(array('child' => 'ann'),
                                                                  array('child' => 'boris'))));

    $dataset = new lmbArrayDataset($data);

    $template = '<list:LIST id="fathers"><list:ITEM>{$name}:'.
                '<iterator:TRANSFER from="children" target="children" />' .
                '<list:LIST id="children"><list:ITEM>{$child},</list:ITEM></list:LIST>|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/iterator_transfer.html', $template);

    $page = $this->initTemplate('/limb/iterator_transfer.html');

    $page->setChildDataSet('fathers', $dataset);

    $this->assertEqual($page->capture(), 'joe:enny,harry,|ivan:ann,boris,|');
  }

  function testOffsetTagAttribute()
  {
    $template =  '<iterator:transfer from="fathers" target="fathers" offset="1"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/iterator_transfer_with_offset.html', $template);

    $page = $this->initTemplate('/limb/iterator_transfer_with_offset.html');

    $data = array(array('name'=> 'joe'),
                  array('name'=> 'ivan'));
    $dataset = new lmbPagedArrayDataset($data);
    $page->set('fathers', $dataset);

    $this->assertEqual($page->capture(), 'ivan:');
  }

  function testLimitTagAttribute()
  {
    $template =  '<iterator:transfer from="fathers" target="fathers" limit="1"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/iterator_transfer_with_limit.html', $template);

    $page = $this->initTemplate('/limb/iterator_transfer_with_limit.html');

    $data = array(array('name'=> 'joe'),
                  array('name'=> 'ivan'));
    $dataset = new lmbPagedArrayDataset($data);
    $page->set('fathers', $dataset);

    $this->assertEqual($page->capture(), 'joe:');
  }

  function testUseComplexDBEWithFromAttribute()
  {
    $template =  '<iterator:transfer from="object.fathers" target="fathers"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/iterator_transfer_with_from_dbe.html', $template);

    $page = $this->initTemplate('/limb/iterator_transfer_with_from_dbe.html');

    $data = array(array('name'=> 'joe'),
                  array('name'=> 'ivan'));

    $dataset = new lmbPagedArrayDataset($data);
    $page->set('object', new WactArrayObject(array('fathers' => $dataset)));

    $this->assertEqual($page->capture(), 'joe:ivan:');
  }

  function testCreatesEmptyArrayIteratorIfScalarlIsReceived()
  {
    $template =  '<iterator:transfer from="fathers" target="fathers"/>'.
                 '<list:LIST id="fathers"><list:ITEM>{$name}:</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/iterator_transfer_with_scalar_received.html', $template);

    $page = $this->initTemplate('/limb/iterator_transfer_with_scalar_received.html');

    $page->set('fathers', 'whatever_scalar');

    $this->assertEqual($page->capture(), '');
  }
}
?>

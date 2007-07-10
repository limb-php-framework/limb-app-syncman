<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactListTagsTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');
require_once('decorators.inc.php');

class WactListTagsTest extends WactTemplateTestCase
{
  protected $founding_fathers;
  protected $numbers;

  function setUp()
  {
    parent :: setUp();

    $this->founding_fathers =  array(array('First' => 'George', 'Last' => 'Washington'),
                                    array('First' => 'Alexander', 'Last' => 'Hamilton'),
                                    array('First' => 'Benjamin', 'Last' => 'Franklin'));

    $this->numbers = array(array('BaseNumber' => 2),
                          array('BaseNumber' => 4),
                          array('BaseNumber' => 6));
  }

  function testList()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/list.html', $template);
    $page = $this->initTemplate('/tags/core/list/list.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testListSeparator()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}'.
                '<list:SEPARATOR>-</list:SEPARATOR></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/separator.html', $template);
    $page = $this->initTemplate('/tags/core/list/separator.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin");
  }

  function testSeparatorWithDefinedStep()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$First}<list:SEPARATOR step="2">|</list:SEPARATOR></list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/separator_defined_step.html', $template);

    $page = $this->initTemplate('/tags/core/list/separator_defined_step.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);

    $this->assertEqual($page->capture(), 'GeorgeAlexander|Benjamin');
  }

  function testListSeparatorWithLiteralAttribute()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}'.
                '<list:SEPARATOR literal="true"></tr><tr></list:SEPARATOR></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/separator_with_literal.html', $template);
    $page = $this->initTemplate('/tags/core/list/separator_with_literal.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George</tr><tr>Alexander</tr><tr>Benjamin");
  }

  function testListDefaultWithDataNotOutput()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}-</list:ITEM>'.
                '<list:default>default</list:default>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/default.html', $template);
    $page = $this->initTemplate('/tags/core/list/default.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testListDefaultWithNoDataOutputs()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}-</list:ITEM>'.
                '<list:default>default</list:default>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/default-empty.html', $template);
    $page = $this->initTemplate('/tags/core/list/default-empty.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator(array()));
    $output = $page->capture();
    $this->assertEqual($output, "default");
  }

  function testListRowNumberProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$ListRowNumber}:{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/list-rownumber.html', $template);
    $page = $this->initTemplate('/tags/core/list/list-rownumber.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "1:George-2:Alexander-3:Benjamin-");
  }

  function testRowNumberWithOffset()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$ListRowNumber}:{$First}</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/limb/list_row_number_with_offset.html', $template);

    $page = $this->initTemplate('/limb/list_row_number_with_offset.html');

    $list = $page->getChild('test');

    $dataset = new WactArrayIterator($this->founding_fathers);
    $dataset->paginate(1, 2);

    $list->registerDataSet($dataset);
    $this->assertEqual($page->capture(), '2:Alexander3:Benjamin');
  }

  function testListRowOddProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>'.
                '<core:optional for="ListRowOdd">odd</core:optional>'.
                '<core:default for="ListRowOdd">even</core:default>'.
                ':{$First}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/list-rowodd.html', $template);
    $page = $this->initTemplate('/tags/core/list/list-rowodd.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "odd:George-even:Alexander-odd:Benjamin-");
  }

  function testListRowEvenProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>'.
                '<core:optional for="ListRowEven">even</core:optional>'.
                '<core:default for="ListRowEven">odd</core:default>'.
                ':{$First}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/list-roweven.html', $template);
    $page = $this->initTemplate('/tags/core/list/list-roweven.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "odd:George-even:Alexander-odd:Benjamin-");
  }

  function testListParityProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$Parity}:{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/list-parity.html', $template);
    $page = $this->initTemplate('/tags/core/list/list-parity.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "odd:George-even:Alexander-odd:Benjamin-");
  }

  function testListFrom()
  {
    $template = '<list:LIST from="test"><list:ITEM>{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/list_from.html', $template);
    $page = $this->initTemplate('/tags/core/list/list_from.html');

    $page->set('test', new WactArrayIterator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testNestedListOuterIdInnerFrom()
  {
     $template = '<list:LIST id="test"><list:ITEM>'.
                  '{$First}:'.
                  '<list:LIST from="sub"><list:ITEM>{$subvar1} </list:ITEM>-</list:LIST>'.
                  '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/nested-id-from.html', $template);
    $page = $this->initTemplate('/tags/core/list/nested-id-from.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new NestedDataSetDecorator(new WactArrayIterator($this->founding_fathers)));
    $output = $page->capture();
    $this->assertEqual($output, "George:value1 value3 value5 -Alexander:value1 value3 value5 -Benjamin:value1 value3 value5 -");
  }

  function testNestedListOuterFromInnerFrom()
  {
    $template = '<list:LIST from="test"><list:ITEM>'.
                '{$First}:'.
                 '<list:LIST from="sub"><list:ITEM>{$subvar1} </list:ITEM>-</list:LIST>'.
                 '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/nested-from-from.html', $template);
    $page = $this->initTemplate('/tags/core/list/nested-from-from.html');

    $page->set('test',new NestedDataSetDecorator(new WactArrayIterator($this->founding_fathers)));
    $output = $page->capture();
    $this->assertEqual($output, "George:value1 value3 value5 -Alexander:value1 value3 value5 -Benjamin:value1 value3 value5 -");
  }

  function testNestedListOuterIdInnerId()
  {
    $template = '<list:LIST id="test"><list:ITEM>'.
                '{$BaseNumber}:'.
                '<list:LIST id="sub"><list:ITEM>{$Num} </list:ITEM>-</list:LIST>'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/nested-id-id.html', $template);
    $page = $this->initTemplate('/tags/core/list/nested-id-id.html');

    $numbers_list = new WactArrayIterator($this->numbers);
    $page->setChildDataSet('test', $numbers_list);
    $page->setChildDataSet('sub', new InnerDataSource($numbers_list));
    $output = $page->capture();
    $this->assertEqual($output, "2:2 4 8 -4:4 16 64 -6:6 36 216 -");
  }

  function testNestedListOuterFromInnerId()
  {
    $template = '<list:LIST from="test"><list:ITEM>'.
                '{$BaseNumber}:'.
                '<list:LIST id="sub"><list:ITEM>{$Num} </list:ITEM>-</list:LIST>'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/nested-from-id.html', $template);
    $page = $this->initTemplate('/tags/core/list/nested-from-id.html');

    $numbers_list = new WactArrayIterator($this->numbers);
    $page->set('test', $numbers_list);
    $page->setChildDataSet('sub', new InnerDataSource($numbers_list));

    $output = $page->capture();
    $this->assertEqual($output, "2:2 4 8 -4:4 16 64 -6:6 36 216 -");
  }

  function testFromComplexDBE()
  {
    $template = '<list:LIST from="object.test"><list:ITEM>'.
                '{$BaseNumber}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/nested_from_complext_dbe.html', $template);
    $page = $this->initTemplate('/tags/core/list/nested_from_complext_dbe.html');

    $object = new ArrayObject(array('test' => new WactArrayIterator($this->numbers)));
    $page->set('object', $object);

    $output = $page->capture();
    $this->assertEqual($output, "2-4-6-");
  }

  function testWorksOkWithScalarValuesAsEmptyIterators()
  {
    $template = '<list:LIST from="object"><list:ITEM>'.
                '{$BaseNumber}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/core/list/works_ok_with_scalara_as_empty_iterators.html', $template);
    $page = $this->initTemplate('/tags/core/list/works_ok_with_scalara_as_empty_iterators.html');

    $page->set('object', 'any_scalar');

    $output = $page->capture();
    $this->assertEqual($output, "");
  }

  function testScriptInList()
  {
    $template = '<list:list id="script_test" from="data"><script type="text/javascript">'.
                '<list:item>alert("{$msg}");</list:item>'.
                '</script></list:list>';

    $this->registerTestingTemplate('/tags/core/list/script-in-list.html', $template);
    $page = $this->initTemplate('/tags/core/list/script-in-list.html');

    $test_ds = new WactArrayIterator(array(array('msg' => 1),
                                           array('msg' => 2),
                                           array('msg' => 3)));

    $page->set('data', $test_ds);

    $output = $page->capture();
    $this->assertWantedPattern('/alert.*1.*alert.*2.*alert.*3/iU', $output, 'Bug 1000806-Failed to iterated over the list [%s]');
    $this->assertNoUnwantedPattern('/list/i', $output, 'Bug 1000806-Output contains the word list [%s]');
    $this->assertNoUnwantedPattern('/item/i', $output, 'Bug 1000806-Output contains the word item [%s]');
  }

  function testScriptInListWorkAround()
  {
    $template = '{$startscript|raw}<list:list id="script_test" from="data">'
                .'<list:item>alert("{$msg}");</list:item>'
                .'</list:list>{$endscript|raw}';

    $this->registerTestingTemplate('/tags/core/list/script-in-list2.html', $template);
    $page = $this->initTemplate('/tags/core/list/script-in-list2.html');

    $test_ds = new WactArrayIterator(array(array('msg' => 1),
                                              array('msg' => 2),
                                              array('msg' => 3)));

    $page->set('data', $test_ds);
    $page->set('startscript',  '<script type="text/javascript">');
    $page->set('endscript',  '</script>');

    $output = $page->capture();
    $this->assertWantedPattern('/alert.*1.*alert.*2.*alert.*3/iU', $output, 'Bug 1000806-Failed to iterated over the list [%s]');
    $this->assertNoUnwantedPattern('/list/i', $output, 'Bug 1000806-Output contains the word list [%s]');
    $this->assertNoUnwantedPattern('/item/i', $output, 'Bug 1000806-Output contains the word item [%s]');
  }
}
?>
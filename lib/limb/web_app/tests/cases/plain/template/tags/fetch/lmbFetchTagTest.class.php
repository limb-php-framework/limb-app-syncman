<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetchTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/tests/cases/lmbTestDbTable.class.php');
lmb_require('limb/web_app/src/fetcher/lmbTableRecordsFetcher.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');
lmb_require('limb/datasource/src/lmbPagedDatasetDecorator.class.php');
lmb_require('limb/datasource/src/lmbDatasetHelper.class.php');

class TestingTemplateDatasetDecorator extends lmbPagedDatasetDecorator
{
  var $prefix1;
  var $prefix2;
  var $sort_params;

  function setPrefix1($prefix)
  {
    $this->prefix1 = $prefix;
  }

  function setPrefix2($prefix)
  {
    $this->prefix2 = $prefix;
  }

  function sort($sort_params)
  {
    $this->sort_params = $sort_params;
  }

  function current()
  {
    $record = parent :: current();
    $data = $record->export();
    $data['full'] = $this->prefix1 . $data['title'] . '-' . $data['description'] . $this->prefix2;
    $processed_record = new lmbDataspace();
    $processed_record->import($data);
    return $processed_record;
  }
}

class TestingFetchTagsDatasetFetcher extends lmbFetcher
{
  static $stub_dataset;
  protected $extra_param;

  protected function _createDataset()
  {
    if(!$this->extra_param)
      return self :: $stub_dataset;

    $arr = lmbDatasetHelper :: iteratorToArray(self :: $stub_dataset);
    foreach($arr as $key => $value)
      $arr[$key]['param'] = $this->extra_param;

    return new lmbPagedArrayDataset($arr);
  }

  function setExtraParam($value)
  {
    $this->extra_param = $value;
  }

  static function setStubDataset($dataset)
  {
    self :: $stub_dataset = $dataset;
  }
}

class lmbFetchTagTest extends lmbWactTestCase
{
  function setUp()
  {
    parent :: setUp();
    $dataset = new lmbPagedArrayDataset(array(array('title' => 'joe', 'description' => 'fisher'),
                                         array('title' => 'ivan', 'description' => 'gamer')));

    TestingFetchTagsDatasetFetcher :: setStubDataset($dataset);
  }

  function testSingleTarget()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_single_target.html', $template);

    $page = $this->initTemplate('/limb/dataset_single_target.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }

  function testMultipleTargets()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget1,testTarget2" />' .
                '<list:LIST id="testTarget1"><list:ITEM>{$title}-</list:ITEM></list:LIST>' .
                '<list:LIST id="testTarget2"><list:ITEM>{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_multiple_targets.html', $template);

    $page = $this->initTemplate('/limb/dataset_multiple_targets.html');

    $this->assertEqual($page->capture(), 'joe-ivan-fisher|gamer|');
  }

  function testWithNavigator()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget"  navigator="pagenav" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>'.
                '<pager:NAVIGATOR id="pagenav" items="10"></pager:NAVIGATOR>';

    $this->registerTestingTemplate('/limb/dataset_with_navigator.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_navigator.html');

    $this->assertEqual($page->capture(), 'joe|ivan|');

    $pager = $page->findChild('pagenav');
    $this->assertEqual($pager->getTotalItems(), 2);
  }

  function testOnlyRecord()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" first="true" />' .
                '<core:datasource id="testTarget">{$title}</core:datasource>';

    $this->registerTestingTemplate('/limb/dataset_only_record.html', $template);

    $page = $this->initTemplate('/limb/dataset_only_record.html');

    $this->assertEqual($page->capture(), 'joe');
  }

  function testApplyDecorators()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" >' .
                '<fetch:decorate using="TestingTemplateDatasetDecorator">' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$full}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_with_decorators.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_decorators.html');

    $this->assertEqual(trim($page->capture()), 'joe-fisher|ivan-gamer|');
  }

  function testSingleTargetWithDBEParam()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget">' .
                '<fetch:param extra_param="{$#genger}">' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$param}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_single_target_with_dbe.html', $template);

    $page = $this->initTemplate('/limb/dataset_single_target_with_dbe.html');
    $page->set('genger', 'Man');

    $this->assertEqual(trim($page->capture()), 'joe-Man|ivan-Man|');
  }

  function testSingleTargetWithComplexDBEParam()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget">' .
                '<fetch:param extra_param="{$#request.gender}">' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$param}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_single_target_with_complex_dbe.html', $template);

    $page = $this->initTemplate('/limb/dataset_single_target_with_complex_dbe.html');

    $dataspace = new lmbDataspace();
    $dataspace->set('gender', 'Man');

    $page->set('request', $dataspace);

    $this->assertEqual(trim($page->capture()), 'joe-Man|ivan-Man|');
  }

  function testApplyDecoratorsWithExtraParams()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget">' .
                '<fetch:decorate using="TestingTemplateDatasetDecorator" prefix1="Hi-" prefix2="-!!">' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$full}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_with_decorators_with_params.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_decorators_with_params.html');

    $this->assertEqual(trim($page->capture()), 'Hi-joe-fisher-!!|Hi-ivan-gamer-!!|');
  }

  function testOrderParamIsPassedFromParamTag()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" >' .
                '<fetch:param order="title=ASC">' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_with_sort_params_by_param_tag.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_sort_params_by_param_tag.html');

    $this->assertEqual(trim($page->capture()), 'ivan-gamer|joe-fisher|');
  }

  function testOrderParamIsPassedFromFetchTagAttribute()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" order="title"/>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/dataset_with_sort_params_by_tag_attribute.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_sort_params_by_tag_attribute.html');

    $this->assertEqual(trim($page->capture()), 'ivan-gamer|joe-fisher|');
  }

  function testOffsetAndLimitFromParamTag()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget">' .
                '<fetch:param offset="1" limit="1"></fetch>'.
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>'.
                '<pager:NAVIGATOR id="pagenav" items="10"></pager:NAVIGATOR>';

    $this->registerTestingTemplate('/limb/dataset_with_offset_limit_by_param_tag.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_offset_limit_by_param_tag.html');

    $this->assertEqual($page->capture(), 'ivan|');
  }

  function testOffsetAndLimitFromFetchTagAttributes()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" offset="1" limit="1" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>'.
                '<pager:NAVIGATOR id="pagenav" items="10"></pager:NAVIGATOR>';

    $this->registerTestingTemplate('/limb/dataset_with_offset_limit_by_attributes.html', $template);

    $page = $this->initTemplate('/limb/dataset_with_offset_limit_by_attributes.html');

    $this->assertEqual($page->capture(), 'ivan|');
  }

  function testDatasetIsCachedByDetault()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/fetched_dataset_is_cached.html', $template);

    $page = $this->initTemplate('/limb/fetched_dataset_is_cached.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');

    // let's change dataset and see what is does not have any affect
    $dataset = new lmbPagedArrayDataset(array(array('title' => 'vika', 'description' => 'dancer'),
                                         array('title' => 'loly', 'description' => 'stripper')));

    TestingFetchTagsDatasetFetcher :: setStubDataset($dataset);

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }

  function testDatasetCachingCanBeDisabled()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" cache_dataset="false"/>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/feched_dataset_caching_disabled.html', $template);

    $page = $this->initTemplate('/limb/feched_dataset_caching_disabled.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');

    // let's change dataset and see what is does not have any affect
    $dataset = new lmbPagedArrayDataset(array(array('title' => 'vika', 'description' => 'dancer'),
                                         array('title' => 'loly', 'description' => 'stripper')));

    TestingFetchTagsDatasetFetcher :: setStubDataset($dataset);

    $this->assertEqual(trim($page->capture()), 'vika|loly|');
  }
}
?>

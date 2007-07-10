<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');
lmb_require('limb/datasource/src/lmbPagedDatasetDecorator.class.php');
lmb_require('limb/classkit/src/lmbClassPath.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class TestingDatasetDecorator extends lmbPagedDatasetDecorator
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
    $data['full'] = $this->prefix1 . $data['name'] . '-' . $data['job'] . $this->prefix2;
    $processed_record = new lmbDataspace();
    $processed_record->import($data);
    return $processed_record;
  }
}

class TestingFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    return new lmbPagedArrayDataset(array(array('name' => 'John', 'job' => 'Carpenter'),
                                          array('name' => 'Mike', 'job' => 'Fisher')));
  }
}

class lmbFetcherTest extends UnitTestCase
{
  function testGetDataSet()
  {
    $fetcher = new TestingFetcher();
    $dataset = $fetcher->getDataSet();

    $dataset->rewind();
    $this->assertTrue($dataset->valid());
    $record = $dataset->current();
    $this->assertEqual($record->get('name'), 'John');
  }

  function testAddDecoratorWithParams()
  {
    $fetcher = new TestingFetcher();
    $fetcher->addDecorator('TestingDatasetDecorator', array('prefix1' => 'PrefixA_',
                                                            'prefix2' => '_PrefixB'));
    $dataset = $fetcher->getDataSet();

    $dataset->rewind();
    $this->assertTrue($dataset->valid());
    $record = $dataset->current();
    $this->assertEqual($record->get('full'), 'PrefixA_John-Carpenter_PrefixB');
  }

  function testSetOrder()
  {
    $fetcher = new TestingFetcher();
    $fetcher->addDecorator('TestingDatasetDecorator');
    $fetcher->setOrder('title=ASC,name,last_name=DESC');

    $dataset = $fetcher->getDataSet();

    $this->assertEqual($dataset->sort_params, array('title' => 'ASC',
                                                    'name' => 'ASC',
                                                    'last_name' => 'DESC'));
  }
}
?>

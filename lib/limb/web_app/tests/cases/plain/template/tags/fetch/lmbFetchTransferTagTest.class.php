<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetchTransferTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/tests/cases/lmbTestDbTable.class.php');

class lmbFetchTransferTestingFetcher
{
  function getDataset()
  {
    return new lmbArrayDataset(array(array('title' => 'joe'),
                                     array('title' => 'ivan')));
  }
}

class lmbFetchTransferTagTest extends lmbWactTestCase
{
  function testTransferPassAllTo()
  {
    $template = '<fetch id="my_fetcher" using="lmbFetchTransferTestingFetcher" target="primary_target" />' .
                '<fetch:transfer from="my_fetcher" target="secondary_target">' .
                '<list:LIST id="primary_target"><list:ITEM>{$title}|</list:ITEM></list:LIST>' .
                '<list:LIST id="secondary_target"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/fetch_transfer_all_to.html', $template);

    $page = $this->initTemplate('/limb/fetch_transfer_all_to.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|joe|ivan|');
  }

  function testTransferPassRecordTo()
  {
    $template = '<fetch id="my_fetcher" using="lmbFetchTransferTestingFetcher" target="primary_target" />' .
                '<fetch:transfer from="my_fetcher" target="secondary_target" first="true">' .
                '<list:LIST id="primary_target"><list:ITEM>{$title}|</list:ITEM></list:LIST>' .
                '<core:datasource id="secondary_target">{$title}</core:datasource>';

    $this->registerTestingTemplate('/limb/fetch_transfer_record_to.html', $template);

    $page = $this->initTemplate('/limb/fetch_transfer_record_to.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|joe');
  }

  function testTransferPassAllToMultipleTargets()
  {
    $template = '<fetch id="my_fetcher" using="lmbFetchTransferTestingFetcher" target="primary_target" />' .
                '<fetch:transfer from="my_fetcher" target="secondary_target1, secondary_target2">' .
                '<list:LIST id="primary_target"><list:ITEM>{$title}|</list:ITEM></list:LIST>' .
                '<list:LIST id="secondary_target1"><list:ITEM>{$title}|</list:ITEM></list:LIST>' .
                '<list:LIST id="secondary_target2"><list:ITEM>{$title}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/fetch_transfer_all_to_multiple_targets.html', $template);

    $page = $this->initTemplate('/limb/fetch_transfer_all_to_multiple_targets.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|joe|ivan|joe-ivan-');
  }

  function testTransferPassRecordToMultipleTargets()
  {
    $template = '<fetch id="my_fetcher" using="lmbFetchTransferTestingFetcher" target="primary_target" />' .
                '<fetch:transfer from="my_fetcher" target="secondary_target1,secondary_target2" first="true">' .
                '<list:LIST id="primary_target"><list:ITEM>{$title}|</list:ITEM></list:LIST>' .
                '<core:datasource id="secondary_target1">-{$title}-</core:datasource>' .
                '<core:datasource id="secondary_target2">~{$title}~</core:datasource>';

    $this->registerTestingTemplate('/limb/fetch_transfer_record_to_multiple_targets.html', $template);

    $page = $this->initTemplate('/limb/fetch_transfer_record_to_multiple_targets.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|-joe-~joe~');
  }
}
?>

<?php
require_once('../setup.php');
require_once('limb/web_app/src/template/lmbWactTemplate.class.php');
require_once('limb/datasource/src/lmbArrayDataset.class.php');
require_once('src/model/News.class.php');

$template = new lmbWactTemplate('rss/last_news.rss');
$template->setChildDataSet('last_news', getNewsDataSetWithFullPaths());

header("Content-Type: application/xml");

$template->display();

/*-------------------------------------------------------*/

function getNewsDataSetWithFullPaths()
{
  $news_rs = lmbActiveRecord :: find('News', array('sort' => array('date' => 'DESC', 'title' => 'ASC'),
                                                   'limit' => 5));

  $result = array();
  foreach($news_rs as $news)
  {
    $news_id = $news->getId();
    $result[$news_id] = $news->export();
    $result[$news_id]['path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/news/detail/' . $news_id;
  }

  return new lmbArrayDataset($result);
}

?>
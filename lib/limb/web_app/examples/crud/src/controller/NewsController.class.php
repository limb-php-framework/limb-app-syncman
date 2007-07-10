<?php
lmb_require('src/model/News.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class NewsController extends lmbController
{
  function doCreate()
  {
    if(!$this->request->hasPost())
      return;

    $news = new News();
    $news->import($this->request);

    $this->useForm('news_form');
    $this->setViewFormDatasource($news);

    if($news->trySave($this->error_list))
      $this->redirect();
  }

  function doEdit()
  {
    $news = new News((int)$this->request->get('id'));

    $this->useForm('news_form');
    $this->setViewFormDatasource($news);

    if(!$this->request->hasPost())
      return;

    $news->import($this->request);

    if($news->trySave($this->error_list))
      $this->redirect();
  }

  function doDelete()
  {
    $news = new News((int)$this->request->get('id'));
    $news->destroy();
    $this->redirect();
  }
}

?>

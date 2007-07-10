<?php

lmb_require('limb/web_app/src/controller/lmbController.class.php');

class MainPageController extends lmbController
{
  function doDisplay()
  {
    $this->redirect(array('controller' => 'projects'));
  }
}

?>

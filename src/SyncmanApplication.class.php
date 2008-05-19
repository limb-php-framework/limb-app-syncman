<?php

lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/core/src/lmbHandle.class.php');

class SyncmanApplication extends lmbFilterChain
{
  function process()
  {
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbUncaughtExceptionHandlingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
                                        array(new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher'))));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));

    parent :: process();
  }
}

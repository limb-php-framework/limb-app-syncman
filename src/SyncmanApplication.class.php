<?php
lmb_require('limb/web_app/src/lmbWebApplication.class.php');

class SyncmanApplication extends lmbWebApplication

{

  function __construct()
  {
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbUncaughtExceptionHandlingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
                                        array(new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher'))));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));
  }

}

?>

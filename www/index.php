<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT"); 

require_once(dirname(__FILE__) . '/../setup.php');
lmb_require('src/SyncmanApplication.class.php');
lmb_require('limb/web_app/src/controller/*.class.php');

$application = new SyncmanApplication();
$application->process();

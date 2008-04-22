<?php

$conf = array(
'HomePage' =>
  array('path' => '/',
    'defaults' => array('controller' => 'projects', 'action' => 'display')),

'ServiceActionId' =>
  array('path' => '/:controller/:action/:id',
        'defaults' => array('action' => 'display')),

'ServiceAction' =>
  array('path' => '/:controller/:action'),

'Service' =>
  array('path' => '/:controller'),
);

?>

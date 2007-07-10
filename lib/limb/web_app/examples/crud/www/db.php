<?php

require_once(dirname(__FILE__) . '/../setup.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

$db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
$rs = $db->select('phpmodules');
$data = array();
foreach($rs as $record)
{
  $data[] = array('name' => $record->get('name'),
                  'description' => $record->get('description'),
                  'url' => $record->get('url'),
                  'flag' => $record->get('flag'));
}

ob_start();
var_export($data);
$content = ob_get_contents();
ob_end_clean();

file_put_contents(dirname(__FILE__) . '/data.inc.php', $content);

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lpsync.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
$application_dir = $_SERVER['LIMB_PROJECT_DIR'];
require_once($application_dir . '/setup.php');

lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/cli/src/lmbCliInput.class.php');
lmb_require('limb/cli/src/lmbCliOption.class.php');
lmb_require('limb/i18n/src/utility/lmbI18NSyncUtility.class.php');

function usage($code = 0, $commands)
{
  $script_name = basename(__FILE__, '.php');
  $usage = <<<EOD
Usage: limb $script_name [OPTION]
-c, --command             command index
-v, --verbose             verbose output
-h, --help                display this help and exit
-l, --language            language prefix

Possible commands:

EOD;

foreach($commands as $index => $command)
  $usage .= "{$index} - ". $command . "\n";

  echo $usage;
  exit($code);
}

$cli = new lmbCliInput(new lmbCliOption('c', 'command', lmbCliOption :: VALUE_REQ),
                    new lmbCliOption('l', 'language', lmbCliOption :: VALUE_OPT),
                    new lmbCliOption('v', 'verbose'),
                    new lmbCliOption('h', 'help'));

$commands = array(
  1 => 'Sync application .ts files (original with languages)',
  2 => 'Parse .html and .php files (packages includes) and update application original .ts file',
  3 => 'Synch application .ts file with packages .ts files (Move changes to packages)',
  4 => 'Create/Update application .ts file from packages .ts files');

if(!$cli->read())
  usage(1, $commands);

if($cli->isOptionPresent('h'))
  usage(0, $commands);

$verbose = $cli->isOptionPresent('v') ? true : false;
$command = $cli->getOptionValue('c', 0);
$language = $cli->getOptionValue('l',  '');
$prefix = '/i18n/translations/';

if(!isset($commands[$command]))
  usage(1, $commands);

echo 'Got command: ' . $commands[$command] ."\n";
echo 'Application dir: '. $application_dir . "\n";
echo 'Language: '. $language . "\n";

$response = new lmbCliResponse($verbose);
$utility = new lmbI18NSyncUtility($prefix . $language . '/', $response);

switch($command)
{
  case 1:
    $utility->synchTranslationsFiles($application_dir);
  break;
  case 2:
    $utility->updateDictionaryFromPackagesSourceFiles($application_dir, lmb_get_used_packages());
  break;
  case 3:
    $utility->updatePackageDictionaries($application_dir, lmb_get_used_packages());
  break;
  case 4:
    $utility->updateApplicationDictionary($application_dir, lmb_get_used_packages());
  break;
}

echo "Done! \n";

?>

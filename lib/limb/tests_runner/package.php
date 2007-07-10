<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: package.php 5055 2007-02-13 12:58:06Z pachanga $
 * @package    tests_runner
 */

require_once 'PEAR/PackageFileManager2.php';
require_once 'PEAR/PackageFileManager/Svn.php';

list($name, $baseVersion, $state) = explode('-', trim(file_get_contents(dirname(__FILE__) . '/VERSION')));
$changelog = file_get_contents(dirname(__FILE__) . '/CHANGELOG');
$summary = file_get_contents(dirname(__FILE__) . '/SUMMARY');
$description = file_get_contents(dirname(__FILE__) . '/DESCRIPTION');
$maintainers = explode("\n", trim(file_get_contents(dirname(__FILE__) . '/MAINTAINERS')));

$version = $baseVersion . (isset($argv[3]) ? $argv[3] : '');
$dir = dirname(__FILE__);

$apiVersion = $baseVersion;
$apiStability = $state;

$package = new PEAR_PackageFileManager2();

$result = $package->setOptions(array(
    'license'           => 'LGPL',
    'filelistgenerator' => 'file',
    'ignore'            => array('package.php',
                                 'package.xml',
                                 '*.tgz',
                                 'var',
                                 'setup.override.php',
                                 'common.ini.override'),
    //'simpleoutput'      => true,
    'baseinstalldir'    => 'limb/' . $name,
    'packagedirectory'  => './',
    'packagefile' => 'package.xml',
    'dir_roles' => array('docs' => 'doc',
                         'examples' => 'doc',
                         'tests' => 'test'),
    'roles' => array('*' => 'php'),
    'exceptions' => array('pear_limb_unit' => 'script',
                          'pear_limb_unit.bat' => 'script'),
    'installexceptions' => array('pear_limb_unit' => '/',
                                 'pear_limb_unit.bat' => '/')
    ));
if(PEAR::isError($result))
{
  echo $result->getMessage();
  exit(1);
}

$package->setPackage($name);
$package->setSummary($summary);
$package->setDescription($description);

$package->setChannel('pear.limb-project.com');
$package->setAPIVersion($apiVersion);
$package->setReleaseVersion($version);
$package->setReleaseStability($state);
$package->setAPIStability($apiStability);
$package->setNotes($changelog);
$package->setPackageType('php');
$package->setLicense('LGPL', 'http://www.gnu.org/copyleft/lesser.txt');

foreach($maintainers as $line)
{
  list($role, $nick, $name, $email, $active) = explode(',', $line);
  $package->addMaintainer($role, $nick, $name, $email, $active);
}

$package->addReplacement('pear_limb_unit', 'pear-config', '@PHP-BIN@', 'php_bin');
$package->addReplacement('pear_limb_unit', 'pear-config', '@PHP-DIR@', 'php_dir');
$package->addReplacement('pear_limb_unit.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
$package->addReplacement('pear_limb_unit.bat', 'pear-config', '@PHP-DIR@', 'php_dir');

$package->addUnixEol('pear_limb_unit');
$package->addWindowsEol('pear_limb_unit.bat');

$package->addRelease();
$package->addIgnoreToRelease('pear_limb_unit.bat');
$package->addInstallAs('pear_limb_unit', 'limb_unit');

$package->addRelease();
$package->setOSInstallCondition('windows');
$package->addIgnoreToRelease('pear_limb_unit');
$package->addInstallAs('pear_limb_unit.bat', 'limb_unit.bat');

$package->setPhpDep('5.1.4');
$package->setPearinstallerDep('1.4.99');

$package->generateContents();

$result = $package->writePackageFile();

if(PEAR::isError($result))
{
  echo $result->getMessage();
  exit(1);
}
?>

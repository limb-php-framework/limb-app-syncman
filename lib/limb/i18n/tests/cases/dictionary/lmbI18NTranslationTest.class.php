<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NTranslationTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/common.inc.php');
lmb_require('limb/i18n/src/dictionary/lmbDictionary.class.php');

class lmbI18NTranslationTest extends UnitTestCase
{
  function test_tr()
  {
    $toolkit = lmbToolkit :: save();

    $translations = array('/foo/bar' => array('Dog' => 'Собака'));
    $toolkit->setDictionary('ru', new lmbDictionary($translations));

    $this->assertEqual(tr('/foo/bar', 'Dog', 'ru'), 'Собака');

    lmbToolkit :: restore();
  }

  function test_tr_useCurrentLocale()
  {
    $toolkit = lmbToolkit :: save();

    $translations = array('/foo/bar' => array('Dog' => 'Собака'));
    $toolkit->setDictionary('ru', new lmbDictionary($translations));
    $toolkit->setLocale($toolkit->createLocale('ru'));

    $this->assertEqual(tr('/foo/bar', 'Dog'), 'Собака');

    lmbToolkit :: restore();
  }

  function test_tr_substituteParameters()
  {
    $toolkit = lmbToolkit :: save();

    $translations = array('/foo/bar' => array('%1 dogs' => '%1 собаки'));
    $toolkit->setDictionary('ru', new lmbDictionary($translations));

    $this->assertEqual(tr('/foo/bar', '%1 dogs', 'ru', array('%1' => 2)), '2 собаки');

    lmbToolkit :: restore();
  }
}

?>

<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIniTest.class.php 4990 2007-02-08 15:35:31Z pachanga $
 * @package    config
 */
lmb_require('limb/config/src/lmbIni.class.php');

define('INI_TEST_UNIQUE_CONSTANT', '*constant*');

class lmbIniTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: instance();
  }

  function tearDown()
  {
    $this->toolkit->clearTestingIni();
  }

  function _createIni($file)
  {
    return new lmbIni($file);
  }

  function testFilePath()
  {
    $ini = new lmbIni(dirname(__FILE__) . '/ini_test.ini', false);
    $this->assertEqual($ini->getOriginalFile(), dirname(__FILE__) . '/ini_test.ini');
  }

  function testTrimmingFileContents()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
        [group1]
         value = test1
      [group2]
              value = test2
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array('value' => 'test1'),
        'group2' => array('value' => 'test2'),
      )
    );
  }

  function testProperComments()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      #[group_is_commented]
      [group1]
       value1 = test1#this a commentary #too#
       #"this is just a commentary"
       value2 = test2
       value3 = "#" # symbols are allowed inside of ""
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array(
          'value1' => 'test1',
          'value2' => 'test2',
          'value3' => '#'),
      )
    );
  }

  function testStringsWithSpaces()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      [group1]
       value1 = this is a string with spaces            indeed
       value2 =       "this is string with spaces too
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array(
          'value1' => 'this is a string with spaces            indeed',
          'value2' => '"this is string with spaces too',
          ),
      )
    );
  }

  function testProperQuotes()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      [group1]
       value1 = "  this is a quoted string  "
       value2 = "  this is a quoted string "too"  "
       value3 = "  this is a quoted string \'too\'  "
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array(
          'value1' => '  this is a quoted string  ',
          'value2' => '  this is a quoted string "too"  ',
          'value3' => '  this is a quoted string \'too\'  ',
          ),
      )
    );
  }

  function testDefaultGroupExistsOnlyIfGlobalValues()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      [group1]
       value = test
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertFalse($ini->hasGroup('default'));
  }

  function testGlobalValuesInDefaultGroup()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      value = global_test
      [group1]
       value = test
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'default' => array('value' => 'global_test'),
        'group1' => array('value' => 'test'),
      )
    );

    $this->assertTrue($ini->hasGroup('default'));
  }

  function testNullElements()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      [group1]
       value =
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array('group1' => array('value' => null))
    );

    $this->assertFalse($ini->hasOption('group1', 'value'));
  }

  function testArrayElements()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      [group1]
       value[] =
       value[] = 1
       value[] =
       value[] = 2
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array('group1' => array('value' => array(null, 1, null, 2)))
    );
  }

  function testHashedArrayElements()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
      [group1]
       value[apple] =
       value[banana] = 1
       value[fruit] =
       value["lime"] = not valid index!
       value[\'lime\'] = not valid index too!
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getAll(),
      array('group1' => array('value' =>
        array('apple' => null, 'banana' => 1, 'fruit' => null)))
    );
  }

  function testCheckers()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1

        [test2]
        test3 =

        [empty_group]
        test = '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertFalse($ini->hasGroup(''));
    $this->assertTrue($ini->hasGroup('default'));
    $this->assertTrue($ini->hasGroup('test'));
    $this->assertTrue($ini->hasGroup('test2'));
    $this->assertTrue($ini->hasGroup('empty_group'));

    $this->assertFalse($ini->hasOption(null, null));
    $this->assertFalse($ini->hasOption('', ''));
    $this->assertFalse($ini->hasOption('', 'no_such_block'));
    $this->assertTrue($ini->hasOption('test', 'test'));
    $this->assertFalse($ini->hasOption('no_such_variable', 'test3'));
    $this->assertTrue($ini->hasOption('unassigned', 'default'));
    $this->assertTrue($ini->hasOption('test', 'default'));
  }

  function testGetOption()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1

        [test2]
        test[] = 1
        test[] = 2

        [test3]
        test[wow] = 1
        test[hey] = 2'
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getOption('unassigned'), '');
    $this->assertEqual($ini->getOption('test'), 1);

    $this->assertEqual($ini->getOption('no_such_option'), '');

    $this->assertEqual($ini->getOption('test', 'no_such_group'), '');

    $this->assertEqual($ini->getOption('test', 'test'), 1);

    $var = $ini->getOption('test', 'test2');
    $this->assertEqual($var, array(1, 2));

    $var = $ini->getOption('test', 'test3');
    $this->assertEqual($var, array('wow' => 1, 'hey' => 2));
  }

  function testReplaceConstants()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
        [{INI_TEST_UNIQUE_CONSTANT}]
        test = {INI_TEST_UNIQUE_CONSTANT}1
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getOption('test', '*constant*'), '*constant*1');
  }

  function testGetGroup()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertEqual($ini->getGroup('default'), array('unassigned' => '', 'test' => 1));
    $this->assertEqual($ini->getGroup('test'), array('test' => 1));

    $this->assertNull($ini->getGroup('no_such_group'));
  }

  function testAssignOption()
  {
    $this->toolkit->setTestingIni(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 2
      '
    );

    $ini = $this->_createIni(LIMB_VAR_DIR .'/testing.ini');

    $this->assertTrue($ini->assignOption($test, 'unassigned'));
    $this->assertEqual($test, '');

    $this->assertTrue($ini->assignOption($test, 'test'));
    $this->assertEqual($test, 1);

    $this->assertTrue($ini->assignOption($test, 'test', 'test'));
    $this->assertEqual($test, 2);
    $this->assertFalse($ini->assignOption($test, 'no_such_option', 'test'));
    $this->assertEqual($test, 2);
  }

  function testMerge()
  {
    $this->toolkit->setTestingIni(
      'a.ini',
      'test = 1
       foo = 1
       val[] = 1

       [group-b]
       a = 2
       foo = 1
       arr[1] = a
      '
    );

    $this->toolkit->setTestingIni(
      'b.ini',
      'test = 2
       bar = 2
       val[] = 2

       [group-b]
       a = 1
       bar = 2
       arr[2] = b
      '
    );


    $a = $this->_createIni(LIMB_VAR_DIR .'/a.ini');
    $b = $this->_createIni(LIMB_VAR_DIR .'/b.ini');

    $c = $a->mergeWith($b);
    $this->assertEqual($c->getAll(), array('default' => array('test' => 2,
                                                              'foo' => 1,
                                                              'bar' => 2,
                                                              'val' => array(2)
                                                              ),
                                            'group-b' => array('a' => 1,
                                                               'foo' => 1,
                                                               'bar' => 2,
                                                               'arr' => array(1 => 'a',
                                                                              2 => 'b')
                                                               )
                                            )
                          );
  }

  function testParseRealFile()
  {
    $ini = new lmbIni(dirname(__FILE__) . '/ini_test.ini', false);
    $this->assertEqual($ini->getAll(), array('test' => array('test' => 1)));
  }
}

?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCliInputTest.class.php 4988 2007-02-08 15:35:19Z pachanga $
 * @package    cli
 */
lmb_require('limb/cli/src/lmbCliInput.class.php');
lmb_require('limb/cli/src/lmbCliOption.class.php');


class lmbCliInputTest extends UnitTestCase
{
  function testReadEmpty()
  {
    $cli = new lmbCliInput();
    $this->assertEqual($cli->getOptions(), array());
    $this->assertEqual($cli->getArguments(), array());

    $this->assertNull($cli->getOption('f'));
    $this->assertNull($cli->getOptionValue('f'));
    $this->assertFalse($cli->isOptionPresent('f'));
    $this->assertEqual($cli->getOptionValue('f', 'wow'), 'wow');
    $this->assertNull($cli->getArgument(0));
    $this->assertEqual($cli->getArgument(0, 'wow'), 'wow');
  }

  function testReadSimpleOptionsWithArguments()
  {
    $argv = array('foo.php', '-f', 'wow', '--bar=1', 'foo', 'bar');

    $cli = new lmbCliInput(new lmbCliOption('f', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('bar', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('f'), 'wow');
    $this->assertEqual($cli->getArguments(), array('foo', 'bar'));
  }

  function testReadOptionsHoldingSpaces()
  {
    $argv = array('foo.php', '--foo', 'wow hey test', '-f', 'spaces spaces', '--bar', 1, 'foo', 'bar');

    $cli = new lmbCliInput(new lmbCliOption('foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('bar', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('f', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('foo'), 'wow hey test');
    $this->assertEqual($cli->getOptionValue('f'), 'spaces spaces');
    $this->assertEqual($cli->getOptionValue('bar'), 1);
    $this->assertEqual($cli->getArguments(), array('foo', 'bar'));
  }

  function testStopReadingOnceEnoughArgumentsStarted()
  {
    $argv = array('foo.php', '--foo=1', '-z', 'stop_here', '-b 2');

    $cli = new lmbCliInput(new lmbCliOption('foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('z'));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getArgument(0), 'stop_here');
    $this->assertEqual($cli->getOptionValue('foo'), 1);
    $this->assertNull($cli->getOptionValue('z'));
    $this->assertTrue($cli->isOptionPresent('z'));
    $this->assertNull($cli->getOptionValue('b'));
    $this->assertFalse($cli->isOptionPresent('b'));
  }

  function testForbiddenOptionValuesBecomeArguments()
  {
    $cli = new lmbCliInput(new lmbCliOption('f'));
    $this->assertTrue($cli->read(array('foo.php', '-f', 'foo', 'bar')));
    $this->assertEqual($cli->getArguments(), array('foo', 'bar'));
  }

  function testReadOptionValueRequiredError()
  {
    $cli = new lmbCliInput(new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_REQ));
    $cli->throwException();

    try
    {
      $cli->read(array('foo.php', '--foo'));
      $this->assertTrue(false);
    }
    catch(lmbCliException $e){}

    $cli->throwException(false);
    $this->assertFalse($cli->read(array('foo.php', '-f')));
  }

  function testReadOptionValueForbiddenError()
  {
    $cli = new lmbCliInput(new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_NO));
    $cli->throwException();

    try
    {
      $cli->read(array('foo.php', '--foo=1'));
      $this->assertTrue(false);
    }
    catch(lmbCliException $e){}

    $cli->throwException(false);
    $this->assertFalse($cli->read(array('foo.php', '--foo', 'foo', 'bar')));
  }

  function testMinimumArgumentsError()
  {
    $cli = new lmbCliInput();
    $cli->setMinimumArguments(2);
    $this->assertFalse($cli->read(array('foo.php', 'wow')));
  }

  function testOfGetOptionValueDualism()
  {
    $argv = array('foo.php', '-f', 1, '--bar=4');

    $cli = new lmbCliInput(new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('b', 'bar', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('f'), 1);
    $this->assertEqual($cli->getOptionValue('foo'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 4);
    $this->assertEqual($cli->getOptionValue('bar'), 4);
  }

  function testReadWithEqualSignPresent()
  {
    $argv = array('foo.php', '--foo=1', '-b', 2);

    $cli = new lmbCliInput(new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('b', 'bar', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('f'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
  }

  function testReadOptionsWithEqualSignMissing()
  {
    $argv = array('foo.php', '--foo', 1, '-b', 2);

    $cli = new lmbCliInput(new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('b', 'bar', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('f'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
  }

  function testReadMixedOptions()
  {
    $argv = array('foo.php', '--foo=1', '-b', 2, '--zoo', 3);

    $cli = new lmbCliInput(new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('b', 'bar', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('z', 'zoo', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('f'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
    $this->assertEqual($cli->getOptionValue('z'), 3);
  }

  function testShortOptionsGluing()
  {
    $argv = array('foo.php', '-ibk');

    $cli = new lmbCliInput(new lmbCliOption('i'),
                        new lmbCliOption('b'),
                        new lmbCliOption('k'));

    $this->assertTrue($cli->read($argv));
    $this->assertTrue($cli->isOptionPresent('i'));
    $this->assertTrue($cli->isOptionPresent('b'));
    $this->assertTrue($cli->isOptionPresent('k'));
    $this->assertFalse($cli->isOptionPresent('z'));
  }

  function testCancelOptionsGluingForNotSpecifiedOptions()
  {
    $cli = new lmbCliInput(new lmbCliOption('i', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read(array('foo.php', '-ias')));
    $this->assertEqual($cli->getOptionValue('i'), 'as');

    $this->assertTrue($cli->read(array('foo.php', '-i100')));
    $this->assertEqual($cli->getOptionValue('i'), 100);
  }

  function testOptionsGluingWithLastValue()
  {
    $argv = array('foo.php', '-ibk', 2);

    $cli = new lmbCliInput(new lmbCliOption('i'),
                        new lmbCliOption('b'),
                        new lmbCliOption('k', lmbCliOption :: VALUE_REQ));

    $this->assertTrue($cli->read($argv));
    $this->assertNull($cli->getOptionValue('i'));
    $this->assertNull($cli->getOptionValue('b'));
    $this->assertEqual($cli->getOptionValue('k'), 2);
  }

  function testGetAllOptions()
  {
    $argv = array('foo.php',
                  '--foo=1',
                  '-b', 2,
                  '--wow', 1,
                  '--hey', 'value with space',
                  '--zoo',
                  '-i50',
                  '-j good',
                  '-e bad',
                  '-ask',
                  '-glu');

    $cli = new lmbCliInput(new lmbCliOption('foo', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('b', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('zoo'),
                        new lmbCliOption('wow', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('hey', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('i', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('j', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('e', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('a', lmbCliOption :: VALUE_REQ),
                        new lmbCliOption('g'),
                        new lmbCliOption('l'),
                        new lmbCliOption('u'));

    $this->assertTrue($cli->read($argv));

    $this->assertEqual($cli->getOptionValue('foo'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
    $this->assertEqual($cli->getOptionValue('wow'), 1);
    $this->assertEqual($cli->getOptionValue('hey'), 'value with space');
    $this->assertEqual($cli->getOptionValue('zoo'), null);
    $this->assertEqual($cli->getOptionValue('i'), 50);
    $this->assertEqual($cli->getOptionValue('j'), 'good');
    $this->assertEqual($cli->getOptionValue('e'), 'bad');
    $this->assertEqual($cli->getOptionValue('a'), 'sk');
    $this->assertEqual($cli->getOptionValue('g'), null);
    $this->assertEqual($cli->getOptionValue('l'), null);
    $this->assertEqual($cli->getOptionValue('u'), null);
  }
}
?>
<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPerformTagTest.class.php 5018 2007-02-09 15:13:19Z tony $
 * @package    web_app
 */
lmb_require('limb/web_app/src/template/components/lmbTemplateCommand.class.php');

class TestingTemplateCommand extends lmbTemplateCommand
{
  function doPerform($value1, $value2)
  {
    $this->template->set('my_var', $value1); // root component is a datasource anyway
    $this->context_component->getDatasourceComponent()->set('my_var', $value2);
  }

  function doSetOtherText($text)
  {
    return $text;
  }
}

class lmbPerformTagTest extends lmbWactTestCase
{
  function testPerform()
  {
    $template = '<core:datasource>'.
                '<perform command="TestingTemplateCommand">'.
                '  <perform:params value1="Value1" value2="Value2" />'.
                '</perform>'.
                '{$my_var} - {$#my_var}</core:datasource>';

    $this->registerTestingTemplate('/limb/perform/simple.html', $template);

    $page = $this->initTemplate('/limb/perform/simple.html');

    $this->assertEqual(trim($page->capture()), 'Value2 - Value1');
  }

  function testPerformWithOutput()
  {
    $template = '<perform command="TestingTemplateCommand" method="set_other_text">'.
                ' <perform:params text="My Text" />'.
                '</perform>';

    $this->registerTestingTemplate('/limb/perform/with_out.html', $template);

    $page = $this->initTemplate('/limb/perform/with_out.html');

    $this->assertEqual(trim($page->capture()), 'My Text');
  }
}
?>

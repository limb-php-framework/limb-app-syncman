<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: inputpassword.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputPasswordComponentTestCase extends WactTemplateTestCase
{
  function testGetValue()
  {
    $template = '<form id="testForm" runat="server">
                        <input type="password" id="test" name="myInput" value="secret" runat="server">
                    </form>';
    $this->registerTestingTemplate('/components/form/inputpassword/value.html', $template);

    $page = $this->initTemplate('/components/form/inputpassword/value.html');

    $form = $page->getChild('testForm');
    $input = $page->getChild('test');

    $this->assertEqual($input->getAttribute('value'), 'secret');
  }

  function testSetValueByName()
  {
    $template = '<form id="testForm" runat="server">
                        <input type="password" id="test" name="myInput" value="oldpassword" runat="server">
                    </form>';
    $this->registerTestingTemplate('/components/form/inputpassword/setvaluebyname.html', $template);

    $page = $this->initTemplate('/components/form/inputpassword/setvaluebyname.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'secret'));
    $form->registerDataSource($data);

    $input = $page->getChild('test');

    $this->assertEqual($input->getValue(),'secret');
    $this->assertEqual($input->getAttribute('value'), 'secret');
  }
}
?>

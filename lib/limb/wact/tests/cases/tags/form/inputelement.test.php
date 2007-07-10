<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: inputelement.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputElementTestCase extends WactTemplateTestCase {

  function testValue()
  {
    $template ='<form id="testForm" runat="server">
                        <input type="text" id="test" name="myInput" runat="server">
                    </form>';
    $this->registerTestingTemplate('/components/form/inputelement/testvalue.html', $template);

    $page = $this->initTemplate('/components/form/inputelement/testvalue.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' => 'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertEqual('foo',$input->getAttribute('value'));
  }

  function testNoValue()
  {
    $template = '<form id="testForm" runat="server">
                        <input type="text" id="test" name="myInput" runat="server">
                    </form>';
    $this->registerTestingTemplate('/components/form/inputelement/testnovalue.html', $template);

    $page = $this->initTemplate('/components/form/inputelement/testnovalue.html');

    $form = $page->getChild('testForm');

    $input = $page->getChild('test');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertEqual('',$input->getAttribute('value'));
  }

}
?>

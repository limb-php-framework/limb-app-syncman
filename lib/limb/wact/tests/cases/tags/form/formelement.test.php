<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: formelement.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactFormElementTestCase extends WactTemplateTestCase
{
  function testGetDisplayName()
  {
    $form_element = new WactFormElement('my_id');
    $this->assertEqual($form_element->getDisplayName(),'');

    $form_element = new WactFormElement('my_id');
    $form_element->displayname = 'a';
    $form_element->setAttribute('title','b');
    $form_element->setAttribute('alt','c');
    $form_element->setAttribute('name','d');
    $this->assertEqual($form_element->getDisplayName(),'a');

    $form_element = new WactFormElement('my_id');
    $form_element->setAttribute('title','b');
    $form_element->setAttribute('alt','c');
    $form_element->setAttribute('name','d');
    $this->assertEqual($form_element->getDisplayName(),'b');

    $form_element = new WactFormElement('my_id');
    $form_element->setAttribute('alt','c');
    $form_element->setAttribute('name','d');
    $this->assertEqual($form_element->getDisplayName(),'c');

    $form_element = new WactFormElement('my_id');
    $form_element->setAttribute('name','foo_Bar');
    $this->assertEqual($form_element->getDisplayName(),'foo Bar');
  }

  function testHasErrorsNone()
  {
    $form_element = new WactFormElement('my_id');
    $this->assertFalse($form_element->hasErrors());
  }

  function testHasErrors()
  {
    $form_element = new WactFormElement('my_id');
    $form_element->errorclass = 'ErrorClass';
    $form_element->errorstyle = 'ErrorStyle';

    $form_element->setError();

    $this->assertTrue($form_element->hasErrors());
    $this->assertEqual($form_element->getAttribute('class'),'ErrorClass');
    $this->assertEqual($form_element->getAttribute('style'),'ErrorStyle');
  }
}
?>

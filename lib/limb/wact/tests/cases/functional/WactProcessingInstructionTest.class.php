<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactProcessingInstructionTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactProcessingInstructionTest extends WactTemplateTestCase
{
  function testXmlProcessingInstruction()
  {
    $template = '<?xml version="1.0"?>';
    $this->registerTestingTemplate('/procinst/xml_processing_instruction.html', $template);

    $page = $this->initTemplate('/procinst/xml_processing_instruction.html');
    $this->assertEqual($page->capture(),$template."\n");
  }

  function testPHPProcessingInstruction()
  {
    $template = '<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactProcessingInstructionTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */
 echo "Foo"; ?>';
    $this->registerTestingTemplate('/procinst/php_processing_instruction.html', $template);

    $page = $this->initTemplate('/procinst/php_processing_instruction.html');
    $this->assertEqual($page->capture(),'Foo');
  }
}
?>
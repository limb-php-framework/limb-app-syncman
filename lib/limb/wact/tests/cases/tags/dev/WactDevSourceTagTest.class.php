<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDevSourceTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactDevSourceTagTest extends WactTemplateTestCase
{
  public function testTag()
  {
    $template = '<dev:source>{$var}</dev:source>';
    $this->registerTestingTemplate('/tags/dev/source.tag', $template);

    $page = $this->initTemplate('/tags/dev/source.tag');
    $this->assertWantedPattern('~\$root-&gt;get\(\'var\'\)~', $page->capture());
  }
}
?>
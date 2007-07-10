<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSiteBranchTagTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

class lmbSiteBranchTagTest extends lmbWactTestCase
{
  protected $old_request_uri = '';

  function setUp()
  {
    parent :: setUp();
    if(isset($_SERVER["REQUEST_URI"]))
      $this->old_request_uri = $_SERVER["REQUEST_URI"];
  }

  function tearDown()
  {
    if($this->old_request_uri)
      $_SERVER["REQUEST_URI"] = $this->old_request_uri;

    parent :: tearDown();
  }

  function testRenderOneSection()
  {
    $template = '<limb:site_branch_selector>'.
                  '<limb:site_branch path="/ru\*">Russian</limb:site_branch>'.
                  '<limb:site_branch path="/en\*">English</limb:site_branch>'.
                  '<limb:site_branch default="true">Default</limb:site_branch>'.
                '</limb:site_branch_selector>';

    $this->registerTestingTemplate('/limb/site_branch_defined_branch.html', $template);

    $page = $this->initTemplate('/limb/site_branch_defined_branch.html');

    $_SERVER["REQUEST_URI"] = '/ru/catalog';

    $this->assertEqual($page->capture(), 'Russian');
  }

  function testRenderDefaultSection()
  {
    $template = '<limb:site_branch_selector>'.
                  '<limb:site_branch path="/ru\*">Russian</limb:site_branch>'.
                  '<limb:site_branch path="/en\*">English</limb:site_branch>'.
                  '<limb:site_branch default="true">Default</limb:site_branch>'.
                '</limb:site_branch_selector>';

    $this->registerTestingTemplate('/limb/site_branch_default_branch.html', $template);

    $page = $this->initTemplate('/limb/site_branch_default_branch.html');

    $_SERVER["REQUEST_URI"] = '/de/catalog';

    $this->assertEqual($page->capture(), 'Default');
  }
}
?>

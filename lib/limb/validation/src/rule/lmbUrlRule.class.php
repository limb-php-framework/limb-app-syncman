<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUrlRule.class.php 5108 2007-02-19 09:07:53Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

/**
* Checks that field value is a valid Url.
*/
class lmbUrlRule extends lmbDomainRule
{
  /**
  * @var array List of allowable schemes e.g. array('http', 'ftp');
  */
  protected $allowable_schemes =  array();

  /**
  * Constructor.
  * @param string Field name
  * @param array List of allowable schemes
  */
  function __construct($field_name, $allowable_schemes = array())
  {
    parent :: __construct($field_name);

    $this->allowable_schemes = $allowable_schemes;
  }

  function check($value)
  {
    $url = @parse_url($value);
    if (isset($url['scheme']) && isset($url['host']) &&
        ($url['scheme'] == 'http' || $url['scheme'] == 'ftp'))
    {
      parent::check($url['host']);
    }

    if (!sizeof($this->allowable_schemes))
      return;

    if (!isset($url['scheme']))
    {
      $this->error(tr('/validation', 'Please specify a scheme for {Field}.'));
      return;
    }

    if (!in_array($url['scheme'], $this->allowable_schemes))
      $this->error(tr('/validation', '{Field} may not use {scheme}.'),
                   array('scheme' => $url['scheme']));
  }
}
?>
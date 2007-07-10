<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleDateRule.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbLocaleDateRule extends lmbSingleFieldRule
{
  protected $locale_id = '';

  function __construct($field_name, $locale_id = '')
  {
    if (!$locale_id)
    {
      $locale = lmbToolkit :: instance()->getLocale();
      $this->locale_id = $locale->getLocaleString();
    }
    else
      $this->locale_id = $locale_id;

    parent :: __construct($field_name);
  }

  function check($value)
  {
    $toolkit = lmbToolkit :: instance();
    $locale = $toolkit->createLocale($this->locale_id);

    try
    {
      $date = lmbLocaleDate :: createByLocaleString($locale, $value, $locale->getShortDateFormat());
    }
    catch(lmbException $e)
    {
      $this->error(tr('/validation', '{Field} must have a valid date format'));
    }
  }
}

?>
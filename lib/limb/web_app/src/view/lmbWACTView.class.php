<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWACTView.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/template/lmbWactTemplate.class.php');
lmb_require('limb/web_app/src/view/lmbView.class.php');

class lmbWACTView extends lmbView
{
  protected $wact_template;
  protected $forms_datasources = array();
  protected $forms_errors = array();

  function render()
  {
    if(!$this->_initWACTTemplate())
      return;

    $this->_fillWACTTemplate();
    return $this->wact_template->capture();
  }

  function reset()
  {
    parent :: reset();
    $this->forms_datasources = array();
    $this->forms_errors = array();
    $this->wact_template = null;
  }

  function getWACTTemplate()
  {
    return $this->wact_template;
  }

  function setFormDatasource($form_name, $datasource)
  {
    $this->forms_datasources[$form_name] = $datasource;
  }

  function getFormDatasource($form_name)
  {
    if(isset($this->forms_datasources[$form_name]))
      return $this->forms_datasources[$form_name];
    else
      return null;
  }

  function setFormErrors($form_name, $error_list)
  {
    $this->forms_errors[$form_name] = $error_list;
  }

  function getForms()
  {
    return $this->forms_datasources;
  }

  function findChild($id)
  {
    if(!$this->_initWACTTemplate())
      return null;

    return $this->wact_template->findChild($id);
  }

  protected function _initWACTTemplate()
  {
    if($this->wact_template)
      return true;

    if(!$path = $this->getTemplate())
      return false;

    $this->wact_template = new lmbWactTemplate($path);
    return true;
  }

  protected function _fillWACTTemplate()
  {
    foreach($this->getVariables() as $variable_name => $value)
      $this->wact_template->set($variable_name, $value);

    foreach($this->forms_datasources as $form_id => $datasource)
    {
      $form_component = $this->wact_template->getChild($form_id);
      $form_component->registerDataSource($datasource);
    }

    foreach($this->forms_errors as $form_id => $error_list)
    {
      $form_component = $this->wact_template->getChild($form_id);
      if(!$error_list->isValid())
        $form_component->setErrors($error_list);
    }
  }
}
?>

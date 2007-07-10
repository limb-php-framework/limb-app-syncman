<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbTools.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/dbal/src/lmbDBAL.class.php');
lmb_require('limb/dbal/src/lmbDbDSN.class.php');

class lmbDbTools extends lmbAbstractTools
{
  protected $default_connection;
  protected $default_db_config;
  protected $db_tables = array();

  function setDefaultDbDSN($conf)
  {
    $this->default_db_config = new lmbDbDSN($conf);
  }

  function getDefaultDbDSN()
  {
    if(is_object($this->default_db_config))
      return $this->default_db_config;

    if(!defined('LIMB_DB_DSN'))
      throw new lmbException('LIMB_DB_DSN constant is not defined!');

    $this->default_db_config = new lmbDbDSN(LIMB_DB_DSN);

    return $this->default_db_config;
  }

  function getDefaultDbConnection()
  {
    if(is_object($this->default_connection))
      return $this->default_connection;

    $dsn = lmbToolkit :: instance()->getDefaultDbDSN();

    $this->default_connection = lmbDBAL :: newConnection($dsn);
    return $this->default_connection;
  }

  function setDefaultDbConnection($conn)
  {
    $this->default_connection = $conn;
  }

  function createTableGateway($table_name)
  {
    if(isset($this->db_tables[$table_name]))
      return $this->db_tables[$table_name];

    $db_table = new lmbTableGateway($table_name);
    $this->db_tables[$table_name] = $db_table;
    return $db_table;
  }
}
?>

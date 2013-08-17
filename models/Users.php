<?php
/**
 * Users Model ORM
 * @package Models
 * @author 	KBedi
 * @version	1.0
 */
class Model_Users
{
    protected $_table;
    private $_db;

    /**
     * Class Contructor
     * @return void
     */    
    public function __construct()
    {
		$registry = Zend_Registry::getInstance();
		$this->_db = $registry->get("dbAdapter");
		Zend_Db_Table_Abstract::setDefaultAdapter($this->_db);
    }

	/**
	* Retrieve table object
	* @return Model_DbTable_Brands
	*/
	public function getTable()
	{
		if (null === $this->_table) {
			// since the dbTable is not a library item but an application item,
			// we must require it to use it
			require_once '/var/www/lamp_root/zend_apps/falcon/models/DbTable/Users.php';
			$this->_table = new Model_DbTable_Users;
		}
		return $this->_table;
	}

	/**
	* Delete all records from the table
	* @return boolean
	*/
	public function removeAll()
	{
		$table  = $this->getTable();
		return $table->delete('1=1');	
	}


	/**
	* Save a new entry
	* @param  array $data 
	* @return int|string
	*/
	public function save(array $data)
	{
		$table  = $this->getTable();
		$fields = $table->info(Zend_Db_Table_Abstract::COLS);
		foreach ($data as $field => $value)
		{
			if (!in_array($field, $fields))
			{
				unset($data[$field]);
			}
		}
		return $table->insert($data);
	}

	/**
	 * Fetch all entries
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function fetchAll()
	{
		return $this->getTable()->fetchAll()->toArray();
	}

	/**
	 * Fetch an individual entry
	 * @param  int|string $id 
	 * @return null|Zend_Db_Table_Row_Abstract
	 */
	public function fetchEntry($id)
	{
		$table = $this->getTable();
		$select = $table->find($id);

		return $table->find($id)->toArray();
	}
}
?>

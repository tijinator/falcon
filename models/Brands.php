<?php
/**
 * Brand Model ORM
 * @package Models
 * @author 	KBedi
 * @version	1.0
 */
class Model_Brands
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
		if (null === $this->_table)
		{
			// since the dbTable is not a library item but an application item,
			// we must require it to use it
			require_once '/var/www/lamp_root/zend_apps/falcon/models/DbTable/Brands.php';
			$this->_table = new Model_DbTable_Brands;
		}
		return $this->_table;
	}

	/**
	 * Delete all records from the table
	 * @return boolean | int
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
		return $this->getTable()->find($id)->current();
	}
	
	/**
	 * Get all events for this brand
	 * @return array
	 */
	public function getEvents($id)
	{
		require_once APPLICATION_PATH . '/models/DbTable/BrandEvents.php';
		require_once APPLICATION_PATH . '/models/DbTable/Events.php';
		
		$table = $this->getTable();
		$brandsRowset = $table->find($id);
		$brandsRowsetCurrent = $brandsRowset->current();
		$eventsRowset = $brandsRowsetCurrent->findManyToManyRowset('Model_DbTable_Events','Model_DbTable_BrandEvents');
		
		return $eventsRowset->toArray();
	}
}
?>

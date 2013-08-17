<?php
/**
 * Templates Model ORM
 * @package Models
 * @author 	KBedi
 * @version	1.0
 */
class Model_Templates
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
	* @return object
	*/
	public function getTable()
	{
		if (null === $this->_table)
		{
			// since the dbTable is not a library item but an application item,
			// we must require it to use it
			require_once APPLICATION_PATH .'/models/DbTable/Templates.php';
			$this->_table = new Model_DbTable_Templates;
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
	* Save a new or an existing entry
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
		
		//Get table metadata
		$info = $table->info();
		
		if($data[$info['sequence']] > 0 )
		{
			$where = $table->getAdapter()->quoteInto('template_id = ?', $data['template_id']);
			return $table->update($data,$where);
		}
		else
		{
			$data['template_id'] = null;
			return $table->insert($data);
		}
	}

	/**
	 * Fetch all entries
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function fetchAll()
	{
		/**
		 * Return all the data
		 */
		return $this->getTable()->fetchAll()->toArray();
	}

	
	/**
	 * Fetch a row from the db using template_id
	 * @param $id template_id 
	 * @return Zend_Db_Table_Row
	 */
	public function fetchEntryByTemplateId($id)
	{	
		$table = $this->getTable();
		$row = $table->fetchRow("template_id = $id");
		return $row;
	}
	
	
	/**
	 * Fetch an individual entry
	 * @param  int|string $id 
	 * @return null|Zend_Db_Table_Row_Abstract
	 */
	public function fetchEntry($event_id,$brand_id)
	{
		return $this->getTable()->find($event_id,$brand_id)->current();
	}
	
	/**
	 * Fetch an individual entry
	 * @param  int $event_id 
	 * @param  int $brand_id 
	 * @return null|Zend_Db_Table_Row_Abstract
	 */
	public function fetchEntries($event_id,$brand_id)
	{
		$table = $this->getTable();
		
		return $table->find($event_id,$brand_id)->toArray();
	}
	
	/**
	 * Fetch key value pairs of template ids and name for drop down lists
	 * @param  int	$event_id
	 * @return array()
	 */
	public function getPairs($event_id,$brand_id)
	{
		$table = $this->getTable();
		$select = $table->select();
		$select->from($table, array('template_id', 'name'))->where("event_id = ?",$event_id)->where("brand_id = ?",$brand_id);
		
		return $this->_db->fetchPairs($select);
	}	 
}
?>

<?php
/**
 * This is the DbTable class for the Users table.
 * @package Models-DBTable
 * @author 	KBedi
 * @version	1.0
 * @todo 	Add logging
 */
class Model_DbTable_Users extends Zend_Db_Table_Abstract
{
    /** Table info */
    protected $_name    = 'user';
    protected $_primary = 'user_id';
    
    /* Relationships */
	protected $_dependentTables = array('Model_DbTable_Brands','Model_DbTable_Events','Model_DbTable_BrandEvents','Model_DbTable_Templates','Model_DbTable_TemplateTypes');
	
    /**
     * Insert new row
     * Ensure that a timestamp is set for the created field.
     * @param  array $data 
     * @return int|boolean
     */
    public function insert(array $data)
    {
    	//Set the date created to now
    	$data['date_created'] = 'NOW()';
    	
        return parent::insert($data);
    }

    /**
     * Method to delete a row
     * @param $where
     * @return int|boolean
     */
    public function delete($where)
    {
        return parent::delete($where);
    }

	/**
	 * Update Row(s)
	 * @param  array $data 
	 * @param  mixed $where 
	 * @return int|boolean
	 */
    public function update(array $data, $where)
    {
    	try
    	{
     		return parent::update($data,$where);
        }
        catch(Zend_Db_Exception $e)
        {
    		return false;
    	}
    }
}
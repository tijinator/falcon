<?php
/**
 * This is the DbTable class for the Brands table.
 * @package Models-DBTable
 * @author 	KBedi
 * @version	1.0
 * @todo 	Add logging
 */
class Model_DbTable_Brands extends Zend_Db_Table_Abstract
{
    /** Table info */
    protected $_name    = 'brand';
    protected $_primary = 'brand_id';
    
    /* Relationships */
	protected $_dependentTables = array('Model_DbTable_BrandEvents','Model_DbTable_Templates');
	
	/* Dependencies */
    protected $_referenceMap    = array(
        'Modifier' => array(
            'columns'           => 'updated_by',
            'refTableClass'     => 'Model_DbTable_Users',
            'refColumns'        => 'user_id'
        ),
        'Creator' => array(
            'columns'           => 'created_by',
            'refTableClass'     => 'Model_DbTable_Users',
            'refColumns'        => 'user_id'
        ),
    );

    /**
     * Insert new row
     * Ensure that a timestamp is set for the created field.
     * @param  array $data 
     * @return int
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
        //return parent::delete($where);
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

?>

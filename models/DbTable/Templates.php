<?php
/**
 * This is the DbTable class for the Brands table.
 * @package Models-DBTable
 * @author 	KBedi
 * @version	1.0
 * @todo 	Add logging
 */
class Model_DbTable_Templates extends Zend_Db_Table_Abstract
{	
    /** Table info */
    protected $_name    	= 'template';
    protected $_primary 	= array('event_id', 'brand_id');
    protected $_sequence 	= 'template_id';
	
    /* Relationships */
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
								        'Type' => array(
								            'columns'           => 'template_type_id',
								            'refTableClass'     => 'Model_DbTable_TemplateTypes',
								            'refColumns'        => 'template_type_id'
								        ),
								        'Event' => array(
								            'columns'           => 'event_id',
								            'refTableClass'     => 'Model_DbTable_Events',
								            'refColumns'        => 'event_id'
								        ),
								        'Brand' => array(
								            'columns'           => 'brand_id',
								            'refTableClass'     => 'Model_DbTable_Brands',
								            'refColumns'        => 'brand_id'
								        ),
								    );

    /**
     * Insert new row
     * Ensure that a timestamp is set for the created field.
     * @param  array $data 
     * @return int|boolean
     */
    public function insert(array $data)
    {
    	try
    	{
	    	//Set the date created to now
	    	$data['date_created'] = 'NOW()';
	        return parent::insert($data);    
    	}
    	catch(Zend_Db_Exception $e)
    	{
    		return false;
    	}
    }


    /**
	 * Method to delete a Row
	 * @param $where
	 * @return int|boolean
	 */
    public function delete($where)
    {
        return parent::delete($where);
    }


    /**
	 * Update Rows(s)
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

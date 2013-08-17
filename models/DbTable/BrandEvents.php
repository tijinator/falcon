<?php
/**
 * This is the DbTable class for the Brands Events table.
 * @package Models-DBTable
 * @author 	KBedi
 * @version	1.0
 * @todo 	Add logging
 */
class Model_DbTable_BrandEvents extends Zend_Db_Table_Abstract
{
	/** Table info */
    protected $_name = 'brand_events';
    protected $_primary 	= array('event_id', 'brand_id');
    protected $_sequence 	= 'id';
	
	/* Dependencies */
    protected $_referenceMap    = array(
								        'Brand' => array(
								            'columns'           => array('brand_id'),
								            'refTableClass'     => 'Model_DbTable_Brands',
								            'refColumns'        => array('brand_id')
								        ),
								        'Event' => array(
								            'columns'           => array('event_id'),
								            'refTableClass'     => 'Model_DbTable_Events',
								            'refColumns'        => array('event_id')
								        )
								    );
								    

    /**
     * insert new row into the table
     * Ensure that a timestamp is set for the created field.
     * @param  array $data 
     * @return int
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
	 * Method to delete a row
	 * @param $where
	 * @return int|boolean
	 */
    public function delete($where)
    {
    	try
    	{
    		return parent::delete($where);	
    	}
    	catch(Zend_Db_Exception $e)
    	{
    		return false;
    	}
        
    }


    /**
	 * Update Row(S)
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
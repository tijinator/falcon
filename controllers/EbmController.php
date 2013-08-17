<?php
/**
 * Ebm Trigger controller (Naming convention based on cheetah mail service)
 * @package Controllers
 * @author 	KBedi
 * @version	1.0
 */
class ebmController extends Zend_Controller_Action 
{
	/**
	 * Execute / Trigger an email request
     * @return void
     */
	public function ebmtrigger1Action() 
	{	
		try
		{
			ini_set("display_errors" , 0);
			    		
			//Disable View Rendering
			$this->_helper->layout()->disableLayout();
    		$this->_helper->viewRenderer->setNoRender(true);
			
    
			//Include the service class file
			require_once APPLICATION_PATH . '/classes/FalconService.php';
			
			//Initial Falcon Service
			$service = new FalconService();
			
			//Execute Mail and echo response.
			echo $service->processEmailRequest();
		
		}
		catch(Zend_Rest_Server_Exception $e)
		{
			//Echo exception
			echo $e->getMessage();
		}
	}
}
?>

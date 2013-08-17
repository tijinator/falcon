<?php
/**
 * Web service controller for authentication (Naming convention of actions and service was Force based on Cheetah Mail Service)
 * @package Controllers
 * @author 	KBedi
 * @version	1.0
 */
class apiController extends Zend_Controller_Action 
{
	/**
	 * Authenticate User
	 * @return String 
	 */
	public function login1Action()
	{
		//Fake request 
		//Disable loading the view
		$this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    
		echo "OK";
		
	}
}
?>

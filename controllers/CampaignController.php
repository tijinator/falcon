<?php
/**
 * Campaign Controller
 * This controller will let you send emails to a list of users.
 * 
 * @package Controllers
 * @author 	KBedi
 * @version	1.0
 */
class CampaignController extends Zend_Controller_Action
{
	/**
	 * Pre dispatch action, called before rendering begins
	 * Being used for authentication.
	 * @return void
	 */
	function preDispatch()
	{
	    $auth = Zend_Auth::getInstance();
	    if (!$auth->hasIdentity()) 
	    {
	            $this->_redirect('user/login');
	    }
	    else
	    {
	            $this->_user = Zend_Auth::getInstance()->getStorage()->read();
	    }
	}
	
	/**
	 * Main / Default action
	 * @return void
	 */
    public function indexAction()
	{
		$this->_helper->actionStack('index', 'menu');
		$this->view->headTitle ('Home Page');	
		$this->view->user = $this->_user;
    }
	
}

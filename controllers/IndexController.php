<?php
/**
 * Index Controller, this can also be refered to as the main controller as all default requests are passed via the Index Controllers
 * @package Controllers
 * @author 	KBedi
 * @version	1.0
 */
class IndexController extends Zend_Controller_Action 
{
	
	protected $_user;
	
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


    /**
	 * List action
	 * @return void
	 */
	public function listAction()
	{
		$selected_brand = $this->getRequest()->getParam('brand_id');
		$selected_event = $this->getRequest()->getParam('event_id');
		
		//Event was selected, display all Templates
		if($selected_event > 0)
		{
			$this->view->headTitle ('Templates');
			$this->view->brandSelected = false ;
			$this->view->eventSelected = true ;	
			
			$model = $this->_getEventsModel();
        	$this->view->event = $model->fetchEntry($selected_event);
        	$this->eventSelected = $selected_event;
        	
			$model = $this->_getTemplatesModel();
	        $this->view->result = $model->fetchEntries($selected_event,$selected_brand);  

	        $this->view->AddNewLink = $this->_helper->url("edit/template_id/0/event_id/$selected_event/brand_id/$selected_brand");
	        $this->view->ShowEvents = $this->_helper->url("list/brand_id/$selected_brand");
		}    
	    //Brand was selected, display all Events
		elseif($selected_brand > 0)
		{
			$this->view->headTitle ('Events');
			$this->view->brandSelected = true ;	
			$this->view->eventSelected = false ;	
			
			$model = $this->_getBrandsModel();
        	$this->view->result = $model->getEvents($selected_brand);
        	$this->view->brand	= $model->fetchEntry($selected_brand);
        	
        	$this->view->AddNewLink = $this->_helper->url("editevent/event_id/0/brand_id/$selected_brand");
        	$this->view->ShowBrands = $this->_helper->url("list");
		}	   
		//Nothing was selected display all Brands
		else
		{
			$this->view->headTitle ('Brands');
			$this->view->brandSelected = false ;
			$this->view->eventSelected = false ;
			
			$model = $this->_getBrandsModel();
	        $this->view->result = $model->fetchAll();
		}		
	}
	
	
	/**
	 * Show the Event Form
	 * @return void
	 */
	public function editeventAction()
	{
		$this->view->headTitle ('Event Template');
		
		$request = $this->getRequest();
		$selected_event = $this->getRequest()->getParam('event_id');
		$form = $this->_getEditEventForm();
	
		// Check to see if this action has been POSTed to
        if ($this->getRequest()->isPost())
        {
        	//Get data for form
			$events_model = $this->_getEventsModel();
			$event = $events_model->fetchEntry($selected_event);


       	    // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form            
            if ($form->isValid($request->getPost()))
            {	           	
           		$model = $this->_getEventsModel();
           		 
            	if($selected_event <= 0)
            	{
	            	if(($selected_event = $model->save($this->getRequest()->getParams()))!==false)
	            	{	
	            		$event = $model->fetchEntry($selected_event['event_id']);
	            		$this->view->message = "Event was successfully saved.";
	            	}
	            	else
	            	{            
	            		$this->view->message = "Event was not saved please try again.";
	            	}
	            }
	            else
	            {	
            		if($events_model->save($this->getRequest()->getParams())!==false)
            		{						
	            		$this->view->message = "Event was successfully saved.";
	            	}
	            	else
	            	{     
	            		$this->view->message = "Event was not saved please try again.";
	            	}
            	}
            }   	
        	
            $this->view->event = $event;
        }
        else
        {        	
			if($selected_event > 0)
			{				
				//Set form up for existing data
				$events_model = $this->_getEventsModel();
				$event = $events_model->fetchEntry($selected_event);
				
				$this->view->event = $event;
				
				$form_data = $event->toArray();
				$form_data['brand_id'] = 0;				
				$form->populate($form_data);		
			}
			else
			{
				$event = $this->_getEventsModel();
				$event->name = "Create New Event";							
				$event->data['brand_id'] = $this->getRequest()->getParam('brand_id');			
				$event->event_id =0;
				
				$this->view->event = $event;
				$form->populate($event->data);
			}
        }
        
        $cancelButton = $form->getElement('cancelButton');
		$cancelButton->setAttrib('onclick','window.location.href="'.$this->_helper->url('list/brand_id/'.$this->getRequest()->getParam('brand_id')).'";');
		$form->setAction($this->_helper->url('editevent/brand_id/'.$this->getRequest()->getParam('brand_id')));
		$this->view->form = $form;
	}
	
	
	/**
	 * Show the template form
	 * @return void
	 */
	public function editAction()
	{
		require_once APPLICATION_PATH . '/models/DbTable/Brands.php';
		require_once APPLICATION_PATH . '/models/DbTable/Events.php';
		
		$this->view->headTitle ('Event Template');

		//Get the selected template
		$selected_template = $this->getRequest()->getParam('template_id');
		$form = $this->_getEditTemplateForm($selected_template);
        $request = $this->getRequest();
       
		
		// Check to see if this action has been POSTed to
        if ($this->getRequest()->isPost()) 
        {
        	//Get data for form
			$templates_model = $this->_getTemplatesModel();
			$template = $templates_model->fetchEntry($_REQUEST['event_id'],$_REQUEST['brand_id']);
			
       	    // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form            
            if ($form->isValid($request->getPost()))
            {
            	$model = $this->_getTemplatesModel();            	
           		
            	if($selected_template <= 0){
	            	if(($selected_template = $model->save($this->getRequest()->getParams()))!==false)
	            	{
	            		$template = $model->fetchEntry($selected_template['event_id'],$selected_template['brand_id']);
	            		$this->view->message = "Template was successfully saved.";   
	            		$this->view->brand = $template->findParentRow('Model_DbTable_Brands');
						$this->view->event = $template->findParentRow('Model_DbTable_Events');        		
	            	}
	            	else
	            	{            
	            		$this->view->message = "Template was not saved please try again.";
	            	}
            	}
            	else
            	{	
            		if($model->save($this->getRequest()->getParams())!==false)
            		{						
	            		$this->view->message = "Template was successfully saved.";
	            	}
	            	else
	            	{     
	            		$this->view->message = "Template was not saved please try again.";
	            	}
	            	$this->view->brand = $template->findParentRow('Model_DbTable_Brands');
					$this->view->event = $template->findParentRow('Model_DbTable_Events');
            	}   
            } 
            else
            {
				$template = $this->_getTemplatesModel();
				$template->name ='Create New Template';
				$template->event_id = $this->getRequest()->getParam('event_id');	
				$template->brand_id = $this->getRequest()->getParam('brand_id');			
				$template->data['event_id'] =  $template->event_id;
				$template->data['brand_id'] =  $template->brand_id;
				$template->template_id =0;
            } 	
        	
            $this->view->template = $template;                 
        }
        else
        {
        	//Initial Page load - No Post
        		
			//Get template information for display purposes
			if ($selected_template > 0)
			{
				//Set form up for existing data
				$templates_model = $this->_getTemplatesModel();
				$template = $templates_model->fetchEntryByTemplateId($selected_template);
				$this->view->template = $template;
				$this->view->template_text = "return date('m-d-y');";
				$form->populate($template->toArray());		
				
				$this->view->brand = $template->findParentRow('Model_DbTable_Brands');
				$this->view->event = $template->findParentRow('Model_DbTable_Events');
			} 
			else
			{
				
				//Set form up for new data
				$template = $this->_getTemplatesModel();
				$template->name ='Create New Template';
				$template->event_id = $this->getRequest()->getParam('event_id');	
				$template->brand_id = $this->getRequest()->getParam('brand_id');			
				$template->data['event_id'] =  $template->event_id;
				$template->data['brand_id'] =  $template->brand_id;
				$template->template_id =0;
				
				$this->view->template = $template;
				$form->populate($template->data);
			}
        }
        
        $cancelButton = $form->getElement('cancelButton');
		$cancelButton->setAttrib('onclick','window.location.href="'.$this->_helper->url('list/event_id/').$template->event_id.'/brand_id/'.$template->brand_id.'";');
		$form->setAction($this->_helper->url('edit'));
		$this->view->form = $form;
	}
	

	/**
	* Get the Template Form
	* @return Form_Import
	*/
	protected function _getEditTemplateForm()
	{
		require_once APPLICATION_PATH . '/forms/Template.php';
		$form = new Form_Template();
		
		return $form;	
	}
	
	
	/**
	* Get the Events Form
	* @return Form_Import
	*/
	protected function _getEditEventForm()
	{
		require_once APPLICATION_PATH . '/forms/Event.php';
		$form = new Form_Event();
		
		return $form;	
	}
	
	
	/**
	 * Get Protected Brands Model
	 * @return Brands Model
	 */
	protected function _getBrandsModel()
	{
		require_once APPLICATION_PATH . '/models/Brands.php';	
		return new Model_Brands();
	}
	
	
	/**
	 * Get Protected Brand Events Model
	 * @return Brand Events Model
	 */
	protected function _getBrandEventsModel()
	{
		require_once APPLICATION_PATH . '/models/BrandEvents.php';
		return new Model_BrandEvents();
	}
	

	/**
	 * Get Protected Events Model
	 * @return Events Model
	*/
	protected function _getEventsModel()
	{
		require_once APPLICATION_PATH . '/models/Events.php';
		return new Model_Events();
	}
	

	/**
	 * Get Protected Templates Model
	 * @return Tempates Model
	 */
    protected function _getTemplatesModel()
    {
    	require_once APPLICATION_PATH . '/models/Templates.php';
		return new Model_Templates();
    }
}

?>

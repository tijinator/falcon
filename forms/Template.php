<?php
/**
 * Template Form
 * @package Forms
 * @author KBedi
 * @version	1.0
 */
class Form_Template extends Zend_Form
{
    
	/**
	 * form initialization
	 * @return void
	 */
    public function init()
    {
    	// Dojo-enable the form:
        Zend_Dojo::enableForm($this);
        
		$templateTypes = $this->_getTemplateTypesModel();

		//set the method for the display form to POST
        $this->setMethod('post');
		
        // add an email element
        $this->addElement('textBox', 'name', array(
            'label'      => 'Event Template:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a valid template name.'))),
								
				            ),		
			'class' => 'inputbox',
        ));
        
        // From
        $this->addElement('textBox', 'from_name', array(
            'label'      => 'From Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a valid Name'))),
								
				            ),		
			'class' => 'inputbox',
        ));
        
        // From
        $this->addElement('textBox', 'from_email', array(
            'label'      => 'From Email:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a valid Email'))),
								array('EmailAddress', true, array('messages' => array('emailAddressInvalid' => 'Email Address is not Valid'))),
				            ),		
			'class' => 'inputbox',
        ));
        
        // From
        $this->addElement('textBox', 'reply_to', array(
            'label'      => 'Reply to:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a valid reply to address'))),
								array('EmailAddress', true, array('messages' => array('emailAddressInvalid' => 'Email Address is not Valid'))),
				            ),		
			'class' => 'inputbox',
        ));
        
		// template type
        $this->addElement('FilteringSelect', 'template_type_id', array(
            'label'      => 'Template Type:',
			'required'  => true,
			'multiOptions' => $templateTypes->getPairs(),
		     array(
		        'autocomplete' => false,
		     	'dojoType'		=> 'dijit.Tree'
		    ),	
        ));

        
       	// Subject
        $this->addElement('textBox', 'subject', array(
            'label'      => 'Subject:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a valid subject'))),
								
				            ),		
			'class' => 'long inputbox',
        ));
        
        // body html
        $this->addElement(
            'textarea',
            'body_html',
            array(
				'required'   		=> 	true,
		        'validators' 		=> 	array(
											array('NotEmpty', true, array('messages' => array('isEmpty' => 'Template body cannot be empty.'))),
						            	),	
                'label'				=> 	'Template Body Html',
			    'editActionInterval'=> 	2,
			    'focusOnLoad'		=> 	false,
			    'inheritWidth'		=> 	true,
            	//'dojoType'			=> 	'dijit.Editor',
            	'dojoType'			=> 	'dijit.form.SimpleTextarea',
				'style'				=>	'width: 100%;height: 350px;'
            ));
         
        // add an email element
        $this->addElement(
            'textarea',
            'body_text',
            array(
				'required'   		=> 	true,
		        'validators' 		=> 	array(
											array('NotEmpty', true, array('messages' => array('isEmpty' => 'Template body cannot be empty.'))),
						            	),	
                'label'				=> 	'Template Body Text',
				'focusOnLoad'		=> 	false,
			    'inheritWidth'		=> 	true,
				'dojoType'			=> 	'dijit.form.SimpleTextarea',
				'style'				=>	'width: 100%;height: 350px;'
            ));
            
     
        // Required Fields
        $this->addElement('textBox', 'required_params', array(
            'label'      => 'Required Params:',
            'filters'    => array('StringTrim'),
			'class' => 'long inputbox',
        ));
        
        // add template_id element
        //this was added here as a space will be added which will act as a separator between the textarea and buttons
        $this->addElement('hidden', 'template_id');
        
		// add the submit button
        $this->addElement('SubmitButton', 'submitButton', array(
				            'label'    => 'Save',
			        	));
		//Make it clean	        	
		$element = $this->getElement('submitButton');
		$element->removeDecorator('DtDdWrapper');
			
										        	 
       	// add a cancel button
        $this->addElement('button', 'cancelButton', array(
            'label'    => 'Back'
        ));
        
        //Make it clean
        $element = $this->getElement('cancelButton');
		$element->removeDecorator('DtDdWrapper');
		
		//add event_id element
        //this was added here as a space will be added which will act as a separator between the textarea and buttons
        $this->addElement('hidden', 'event_id');
        
       	//add brand_id element
        //this was added here as a space will be added which will act as a separator between the textarea and buttons
        $this->addElement('hidden', 'brand_id');
        
    }
    
    /**
     * Get the templates model
     * @return Data Model
     */
    protected function _getTemplatesModel()
    {
    	require_once APPLICATION_PATH . '/models/Templates.php';
		return new Model_Templates();
    }
    
    /**
     * Get the Template Types Model
     * @return Data Model
     */
    protected function _getTemplateTypesModel()
    {
    	require_once APPLICATION_PATH . '/models/TemplateTypes.php';
		return new Model_TemplateTypes();
    }
}

?>
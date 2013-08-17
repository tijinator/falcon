<?php
/**
 * Event Form
 * @package Forms
 * @author 	KBedi
 * @version	1.0
 */

class Form_Event extends Zend_Form
{
    
	/**
	 * form initialization
	 * @return void
	 */
    public function init()
    {
     	// Dojo-enable the form:
        Zend_Dojo::enableForm($this);

		//set the method for the display form to POST
        $this->setMethod('post');
		
        // add an email element
        $this->addElement('textBox', 'name', array(
            'label'      => 'Event Name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a valid Event Name.'))),
								
				            ),		
			'class' => 'inputbox',
        ));
        
        		//add event_id element
        //this was added here as a space will be added which will act as a separator between the textarea and buttons
        $this->addElement('hidden', 'event_id');
        
               
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
		
		$this->addElement('hidden', 'brand_id');
        
    }

}

?>
<?php
/**
 * This is the guestbook form.  It is in its own directory in the application 
 * structure because it represents a "composite asset" in your application.  By 
 * "composite", it is meant that the form encompasses several aspects of the 
 * application: it handles part of the display logic (view), it also handles 
 * validation and filtering (controller and model).  
 */
class Form_ImportProcess extends Zend_Form
{
    /**
     * init() is the initialization routine called when Zend_Form objects are 
     * created. In most cases, it make alot of sense to put definitions in this 
     * method, as you can see below.  This is not required, but suggested.  
     * There might exist other application scenarios where one might want to 
     * configure their form objects in a different way, those are best 
     * described in the manual:
     *
     * @see    http://framework.zend.com/manual/en/zend.form.html
     * @return void
     */ 
    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');
				
		// add an email element
        $this->addElement('hidden', 'recordsToImport',array('style'=>'height: 0px;'));

		// add the submit button
        $this->addElement('submit', 'submit', array(
            	'label'    => 'Continue, Process Records >>',
		'id' => 'submit1'
	));		
		
		// add an email element
        $this->addElement('hidden', 'website');
		
		$this->setElementDecorators(array(
		    'ViewHelper',
		    'Errors',
		    array(
					array('data' => 'HtmlTag'), 
					array('tag' => 'div'),
					array('Label', array('tag' => 'div',),
									array(array('row' => 'HtmlTag'), array('tag' => 'div')),						
					)
			)
		));
		$this->removeDecorator('submit');
	$this->setAttrib("onsubmit","var e = document.getElementById('submit1');e.value='Importing, please wait....';e.disabled = true;");	
    }
}

?>


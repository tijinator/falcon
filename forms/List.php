<?php
// application/forms/Import.php

class Form_List extends Zend_Form
{
    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');
		
		/** 
		 * Add file element
		 * 
		 * $fileElement = new Zend_Form_Element_File('smartIndexFile');
		 * $fileElement->setLabel('Upload the file to import from:');
		 * //->setDestination('/var/www/lamp_root/wwwapps/temp');\
		 * //limit to 100K
		 * //$fileElement->addValidator('Size', false, 102400);
		 * // Tab delimited or Csv files
		 * $fileElement->setRequired(true);		
		 * $fileElement->addValidator('Extension', false, 'txt,csv,xls');
		 * $fileElement->addValidator('MimeType', false ,array('text/csv', 'text/plain','application/vnd.ms-excel')); 		
		 * $this->addElement($fileElement, 'smartIndexFile');
		*/
		
		$this->addElement('select',
							'website',
							array(
								'label'     => 'Please Select the Website You wish to import into:',
								'required'  => true,
								 'value' => 'value',
					             'Options' => array(
					                 'puritan' => 'Puritan',
									 'vitaminworld' => 'Vitamin World' 									 
								) 
							)
		);
						
		// add the submit button
		$this->addElement('submit', 'submit', array(
			'id'=> 'submit1',
			'label'    => 'Import',
		));
		
		
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setAttrib('onsubmit', 'var e=document.getElementById("submit1");e.value = "Processing, please wait...";e.disabled = true;');
    }
}

?>


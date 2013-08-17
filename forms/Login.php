<?php
/**
 * Login Form
 * @package Forms
 * @author 	KBedi
 * @version	1.0
 */
class Form_Login extends Zend_Form
{
	/**
	 * form initialization
	 * @return void
	 */
    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');

        // add an email element
        $this->addElement('text', 'username', array(
            'label'      => 'Email / Username:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
								array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter an Username.'))),
								//array('EmailAddress',true,array('messages' => array('emailAddressInvalid' => 'Please enter a valid email address.'))),								
				            ),			
        ));

        // add an email element
        $this->addElement('password', 'password', array(
            'label'      => 'Password:',
            'required'   => true,
            'filters'    => array('StringTrim'),
			'validators' =>array(array('NotEmpty', true, array('messages' => array('isEmpty' => 'Please enter a password.')))),
		));

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => 'Login',
        ));
        
        /*$this->addElement(
		    'ValidationTextBox',
		    'template_email',
		    array(
		        'label'          => 'Email From',
		        'required'       => true,
		        'regExp'         => '.+\@.+\..+',
		        'invalidMessage' => 'Email address is not formatted properly.',
		    )
		);*/
    }
}

?>


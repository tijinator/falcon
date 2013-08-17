<?php
/**
 * Falcon Service Class to Send Emails via SendMail and Local Database with Templates
 * @package Classes
 * @author KBedi
 * @version 1.0
 */
class FalconService
{
	protected $_bodyHtml;
	protected $_bodyText;
	protected $_subject;
	protected $_fromName;
	protected $_fromEmailAddress;
	protected $_toName;
	protected $_toEmailAddress;
	protected $_replyTo;
	protected $_data;
	protected $_html = 0; //Email Format
	
 	/**                
     * Process the request, validate data, finally will send the email.
     * @param array|$inputParams
     * @return string
     */
    public function processEmailRequest($inputParams = array()) 
    {	
    	try
    	{	
    		//Internal call will send all the required data as params
    		//HTTP Request load all the data from Request object
    		if(!empty($inputParams))
    		{
    			$requestParams = $inputParams;
    		}
    		else
    		{
    			$requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    		}	
    		
    		//Instantiate the logger 
			$logger = Zend_Registry::getInstance()->logger;		
			$ebmTrackingData = "eid = ".$requestParams['eid']." : aid = ".$requestParams['aid']." : email = ".$requestParams['email'];

	    	//Validate Data
	    	$validate = $this->_validate($requestParams);
	    	if ($validate['Error'])
	    	{
				$logger->log( "$ebmTrackingData : ErrCode = ".$validate['ErrorCode'] . " : Err = ".$validate['ErrorMessage'],Zend_Log::WARN);
	    		return "Err:".$validate['ErrorMessage']; 
	    	}
	    	
	    	//Load data into local variables
	    	$populate = $this->_loadData($requestParams);
    		if ($populate['Error'])
    		{
				$logger->log( "$ebmTrackingData : ErrCode = ".$populate['ErrorCode'] . " : Err = ".$populate['ErrorMessage'],Zend_Log::WARN);
	    		return "Err:".$populate['ErrorMessage']; 
	    	}
	    	
    		//Load data into local variables
	    	$sendMail = $this->_sendMail();
    		if ($sendMail['Error'])
    		{
				$logger->log( "$ebmTrackingData : ErrCode = ".$sendMail['ErrorCode'] . " : Err = ".$sendMail['ErrorMessage'],Zend_Log::WARN);
	    		return "Err:".$sendMail['ErrorMessage']; 
	    	}
	    	
	    	$logger->log("$ebmTrackingData Sent successfully",Zend_Log::INFO);
	    	
	    	
		//All went well return OK 
       		return "OK";
    	}
    	catch(Zend_Exception $e)
		{
			$logger->log( "$ebmTrackingData : ErrCode = 100 : Err = Exception Occurred".$e->getMessage(),Zend_Log::WARN);
		    return "Err:Internal Error";
		}
    }
    
    
    /**
     * Validate the input params and make sure the user supplies all required values. 
     * @param array|$requestParams
     * @return array()|$result
     */
    protected function _validate($requestParams)
    {	
    	//Default positive result if all goes good.
    	$result = array('Error'			=> false,
		    			'ErrorCode'		=> 0,
		    			'ErrorMessage'	=> '');
    	
    	try{
    		
    		$email_validator = new Zend_Validate_EmailAddress();
    		
    		//Verify Brands ID Provided
    		if(!isset($requestParams['aid'])){
    			$result = array('Error'			=> true,
				    			'ErrorCode'		=> 101,
				    			'ErrorMessage'	=> 'Invalid Brand Identifier');
    		
	    	//Verify Event ID Provided
	    	}elseif(!isset($requestParams['eid'])){
    			$result = array('Error'			=> true,
				    			'ErrorCode'		=> 102,
				    			'ErrorMessage'	=> 'Invalid Event Identifier');
    		
	    	}elseif(!isset($requestParams['email'])){
	    	
	    		$result = array('Error'			=> true,
				    			'ErrorCode'		=> 105,
				    			'ErrorMessage'	=> 'Invalid Email Address');	  	    		
	    	}elseif(!$email_validator->isValid($requestParams['email'])){
	    	
	    		$result = array('Error'			=> true,
				    			'ErrorCode'		=> 106,
				    			'ErrorMessage'	=> 'Invalid Email Address ' . implode(" | ",$email_validator->getMessages()));	  	    		  		
	    	}
	    
	    //Exception occurred
    	}catch(Zend_Exception $e){
    		
        	$result = array('Error'			=> true,
			    			'ErrorCode'		=> 100,
			    			'ErrorMessage'	=> 'Internal error occurred while (Validating).' . $e->getMessage());	
    	}	    		

    	//Return result
	    return $result;
    }
    
    
    /**
     * Load data using request into local params
     * @return array
     */
    protected function _loadData($requestParams){
    	
    	//Default positive result if all goes good.
    	$result = array('Error'			=> false,
		    			'ErrorCode'		=> 0,
		    			'ErrorMessage'	=> '');
    	
    	//Try to load the data
    	try{
    		
    		//Check to see if HTML parameter is passed
    		if(isset($requestParams['html']))
    		{
    			$this->_html = $requestParams['html'];
    		}
    		
    		//Check if Brand exists
     		$brands_model = $this->_getBrandsModel();
    		$brand = $brands_model->fetchEntry($requestParams['aid']);   		
    		if(count($brand) <=0)
    		{
        		$result = array('Error'	=> true,
				    			'ErrorCode'		=> 104,
				    			'ErrorMessage'	=> 'Invalid Brand Identifier');
        		return $result;
    		}
    			   
    		//Get template data
    		$templates_model = $this->_getTemplatesModel();
    		$template = $templates_model->fetchEntry($requestParams['eid'],$requestParams['aid']);
    		
    		//Populate Data
    		if(count($template) > 0)
    		{
    			/** 
    			 * Check if advanced template
    			 * If so parse any php code inside template
    			 */
    			if($template->template_type_id == 2){
    				$template->body_text  = $this->_evaluateCodeInTemplate($template->body_text);
    				$template->body_html  = $this->_evaluateCodeInTemplate($template->body_html);
    			}
    			
    		    //Check to see if any required params are missing
	    		$diff = (trim($template->required_params) !="" ) ? array_diff(explode(",",$template->required_params),array_keys($requestParams)):array();
				
	    		//If required params are missing return error
	    		if(count($diff) > 0 )
	    		{
	    			$result = array('Error'			=> true,
					    			'ErrorCode'		=> 501,
					    			'ErrorMessage'	=> 'Internal error occurred (Required params missing).' . implode(" | ",$diff));
	    			return $result;
	    		}
    		
    			//Parse HTML for any variable substitution
    			$this->_bodyHtml = $this->_parseTemplate($template->body_html , $requestParams);
    		    if(!$this->_bodyHtml)
    		    {
    				$result = array('Error'			=> true,
					    			'ErrorCode'		=> 502,
					    			'ErrorMessage'	=> 'Internal error occurred (Data load).' );
    				return $result;
    			}

    			//Parse Text for any variable substitution
    			$this->_bodyText = $this->_parseTemplate($template->body_text , $requestParams);
    			if(!$this->_bodyText)
    			{
    				$result = array('Error'			=> true,
					    			'ErrorCode'		=> 503,
					    			'ErrorMessage'	=> 'Internal error occurred (Data load).');
    				return $result;
    			}
    			
    			//Parse any variables in the subject field
				$this->_subject = $this->_parseTemplate($template->subject , $requestParams);
				
				//Populate to and from fields
				$this->_fromName = (isset($requestParams['fromName']) && $requestParams['fromName']!='')?$requestParams['fromName']:$template->from_name;
				$this->_fromEmailAddress = (isset($requestParams['fromEmail']) && $requestParams['fromEmail']!='')?$requestParams['fromEmail']:$template->from_email;
				$this->_replyTo = (isset($requestParams['fromEmail']) && $requestParams['fromEmail']!='')?$requestParams['fromEmail']:$template->reply_to;
				$this->_toName = (isset($requestParams['to']))?$requestParams['to']:"";
				$this->_toEmailAddress = $requestParams['email'];
	
    		//Template not found
    		}
    		else
    		{	
	    		$result = array('Error'			=> true,
				    			'ErrorCode'		=> 103,
				    			'ErrorMessage'	=> 'Invalid Template Identifier');	    	
    		}
	    
    	//Catch General exception
    	}
    	catch(Zend_Exception $ze)
    	{
    		$result = array('Error'			=> true,
			    			'ErrorCode'		=> 401,
			    			'ErrorMessage'	=> 'Internal error occurred (Loading Data).' . $ze->getMessage());
    		
    	//Zend db exception	
    	}
    	catch(Zend_Db_Exception $zbe)
    	{
    		$result = array('Error'			=> true,
			    			'ErrorCode'		=> 401,
			    			'ErrorMessage'	=> 'Internal error occurred (Database).' . $zbe->getMessage());
    	}
    	
		return $result;
    }
    
    
    /**
     * Send the email
     * @return array
     */
    protected function _sendMail()
    {	
    	//Default positive result if all goes good.
    	$result = array('Error'			=> false,
		    			'ErrorCode'		=> 0,
		    			'ErrorMessage'	=> '');
    	
    	try
    	{
    		//Check if all required Data is populated in local object
    		if($this->_isDataReady())
    		{
	    		$mail = new Zend_Mail();
	    	 
				$mail->setBodyHtml($this->_bodyHtml);
				$mail->setBodyText($this->_bodyText);
				$mail->setFrom($this->_fromEmailAddress, $this->_fromName);
				$mail->addTo($this->_toEmailAddress, $this->_toName);
				$mail->setSubject($this->_subject);
				$mail->addHeader('Reply-To',$this->_replyTo);
				
				if(!$mail->send())
				{
					    	//Default positive result if all goes good.
		    		$result = array('Error'			=> true,
			    					'ErrorCode'		=> 403,
			    					'ErrorMessage'	=> 'Unable to send email due to system error');
				}
    		}
    		else
    		{
	    		$result = array('Error'			=> true,
				    			'ErrorCode'		=> 404,
				    			'ErrorMessage'	=> 'Not all data was found to complete request');    			
    		}
    	}	
		//Catch General exception
    	catch(Zend_Exception $ze)
    	{
    		$result = array('Error'			=> true,
			    			'ErrorCode'		=> 401,
			    			'ErrorMessage'	=> 'Internal error occurred (General Sending).' . $ze->getMessage());
    	}
    	//Catch Mail Exception
    	catch(Zend_Mail_Exception $zme)
    	{
    		$result = array('Error'			=> true,
			    			'ErrorCode'		=> 402,
			    			'ErrorMessage'	=> 'Internal error occurred (Mail Sending).' . $zme->getMessage());
    	}
    	
    	return $result;
    }
    
    
    /**
     * Replace all place holder variables with data values
     * @param string|$template
     * @param array|$data
     * @return string
     */
    protected function _parseTemplate($template = "",$data = array()){    	
    	try
    	{
	    	foreach ($data as $key => $value)
	    	{
	    		$template = str_replace("%%".strtoupper($key)."%%",$value,$template);	
	    	}
	    	
	    	//Clean any left over variables whose value might not have been supplied
	    	preg_match_all("(\%\%[A-Z0-9\.]+\%\%)",$template,$matches);
	    	foreach($matches[0] as $var)
	    	{
	    		$template = str_replace($var,"",$template);
	    	}
	    	
	    	return $template;
    	}
    	catch(Zend_Exception $e)
    	{
    		return false;
    	}
    }
    
    
    /**
     * Evaluate any code 
     * @param $template
     * @return unknown_type
     */
    protected function _evaluateCodeInTemplate($template = "")
    {	
	    //Replace fake PHP tags with real ones
	    $template = str_replace("%%PHPSTART%%","<?php",$template);
	    $template = str_replace("%%PHPEND%%","?>",$template);
	    
    	// turn on output buffering
	    ob_start();
	    ini_set('display_errors', 0);
	    
	    // this parses the php code in out $php variable; this is the heart of our templating logic
	    eval("?>" . $template . "<?");
	    
	    // store the final parsed output in a variable $output
	    $evaluatedText = ob_get_contents();
	    
	    // clear buffer and stop buffering
	    ob_end_clean();
 		
	    //Return the parsed text
    	return $evaluatedText;
    }
    
    
    /**
     * Confirm that all data required to send email is present
     * @return Boolean
     */
    protected function _isDataReady()
    {
    	return true;
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
	 * Get Protected Templates Model
	 * @return Tempates Model
	 */
    protected function _getTemplatesModel()
    {
    	require_once APPLICATION_PATH . '/models/Templates.php';
		return new Model_Templates();
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
}
?>

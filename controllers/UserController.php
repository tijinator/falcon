<?php
/**
 * User / Authentication / ACL controller
 * @package Controllers
 * @todo	ACL functionality to be added.
 * @author 	KBedi
 * @version	1.0
 */
class UserController extends Zend_Controller_Action
{
    /**
     * Login Action to authenticate Request or Show Login Form
     * Sets up Zend Session
     * @return void
     */
	public function loginAction()
	{
		if (Zend_Auth::getInstance()->hasIdentity())
		{
            $this->_redirect('/');
        }
		
		$request = $this->getRequest();
       	$form    = $this->_getLoginForm();
		$logger = Zend_Registry::getInstance()->logger;
		
        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost())
        {
            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid($request->getPost()))
            {
				//Do the authentication 
                $auth = Zend_Auth::getInstance();
				
                 // do the authentication                 
				$request = $this->getRequest();
				$username = $request->getParam('username');
				$password = $request->getParam('password');
				
				$options = array(
									'server1' => Array(											
											'host' => 'enterprise.nbty.global',
											'useStartTls' => 1,		
											'accountDomainName' => 'enterprise.nbty.global',	
											'accountDomainNameShort' => 'enterprise.nbty.global',
											'accountCanonicalForm' => 3,
											'baseDn' => 'OU=NBTY,DC=Enterprise,DC=NBTY,DC=Global',
											//'accountFilterFormat' => '(&(objectClass=user)(sAMAccoutName=%s))',									
											),
									'server2' => Array(											
											'host' => 'enterprise.nbty.global',
											'useStartTls' => 1,		
											'accountDomainName' => 'enterprise.nbty.global',	
											'accountDomainNameShort' => 'enterprise.nbty.global',
											'accountCanonicalForm' => 3,
											'baseDn' => 'OU=HnB,DC=Enterprise,DC=NBTY,DC=Global',
											),
									'server3' => Array(											
											'host' => 'nydc01.inet.nbty.com',
											'useStartTls' => 1,		
											'accountDomainName' => 'inet.nbty.com',	
											'accountDomainNameShort' => 'inet.nbty.com',
											'accountCanonicalForm' => 3,
											'baseDn' => 'dc=inet,dc=nbty,dc=com',
										)
								);
                $authAdapter = new Zend_Auth_Adapter_Ldap($options, $username,$password);                                     
				
                $result = $auth->authenticate($authAdapter);
				print_r($result);
		
                if ($result->isValid())
                {  
					//Login Succeeded
					$logger->log("$username Login Success.", Zend_Log::INFO);
					
					$conn = ldap_connect("enterprise.nbty.global") or die("Could not connect to server");  
					ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
					ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
					$bindUsername = 'cn=svcLDAP,ou=Service Accounts,ou=Global Resources,dc=Enterprise,dc=NBTY,dc=Global';
					$bindPassword = 'ldapsvc';

					// bind to the LDAP server specified above 
					$r = ldap_bind($conn,$bindUsername,$bindPassword) or die("Could not bind to server");     
					
					// start searching
					// specify both the start location and the search criteria
					// in this case, start at the top and return all entries $result =
					$ldap_result = ldap_search($conn,"DC=Enterprise,DC=NBTY,DC=Global", "(&(objectClass=user)(sAMAccountName=$username))") or die ("Error in search
					query");  
					
					$authorizedUsers = Zend_Registry::getInstance()->get("authorizedUsers");
					
					if(!in_array(strtolower($username),$authorizedUsers))
					{
	                    // failure: clear database row from session
	                    $info = ldap_get_entries($conn, $ldap_result);	
						$logger->log("$username Login Aborted. (Not authorized)", Zend_Log::WARN);
	                    $this->view->message = $info[0]['givenname'][0]." you are not authorized to access this application. <br />Please contact KristineSchlosser@nbty.com if you would like access to this application.";
	                    Zend_Auth::getInstance()->clearIdentity();						
					}
					else
					{
						// get entry data as array
						$info = ldap_get_entries($conn, $ldap_result);						
						$auth->getStorage()->write($info[0]);
	                    			$this->_redirect('/index');	
					}
					
                }
                else
                {
                    // failure: clear database row from session
					$logger->log("$username Login Failed.", Zend_Log::WARN);
                    $this->view->message = 'Login failed.';
                }
            }
        }
        
		$this->view->headTitle ('Login');
		 // assign the form to the view
        $this->view->form = $form;
	}

	/**
     * Log user out, and end session  redirect to homepage
     * @return void
     */
	function logoutAction()
    {
    	//Get user info before logout for logging
    	$user = Zend_Auth::getInstance()->getStorage()->read();
    	
    	//Log user out
        Zend_Auth::getInstance()->clearIdentity();
        
        //Logging
        $logger = Zend_Registry::getInstance()->logger;
        $logger->log($user['uid'][0]." Logout success.", Zend_Log::WARN);
        
        //Redirect to home
        $this->_redirect('/');
    }

    /**
     * Get Login Form
     * @return Zend_Form
     */
    protected function _getLoginForm()
    {
        require_once APPLICATION_PATH . '/forms/Login.php';
        $form = new Form_Login();
        $form->setAction($this->_helper->url('login'));
        return $form;
    }

}

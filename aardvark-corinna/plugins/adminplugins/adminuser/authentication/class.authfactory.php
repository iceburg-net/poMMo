<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 04.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

include_once(bm_baseDir.'/plugins/adminplugins/adminuser/interfaces/interface.plugin.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.db_authhandler.php');


class AuthFactory implements iPlugin {

	private $name 	= "";			// is the identifier for the object

	private $authmethod = NULL;		// Authentification method
	private $logger 	= NULL;
	private $dbo 		= NULL;


	public function __construct($dbo, $logger) {
		$this->setName("Authentication Factory");
		$this->logger = $logger;
		$this->dbo = $dbo;
	}
	
	public function __destruct() {
		unset($this->name);
		unset($this->logger);
		unset($this->dbo);
		unset($this->authmethod);
	}
	

	public function selectReturnObject() {

		// Get from database the authentication method that is set in the authentication Main module
		$authhandler = new AuthHandler($this->dbo);
		$this->setAuthMethod($authhandler->dbGetAuthMethod());

		switch ($this->authmethod) {
			case 'simpleldapauth':
					include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.simpleldapauth.php');
					$this->handleMessage("Authenticationmethod: {$this->authmethod}");
					return new SimpleLdapAuth($this->dbo, $this->logger);
					break;
					
			case 'queryldapauth':
					//TODO
					$this->handleMessage("Authenticationmethod: {$this->authmethod}");
					break;
					
			case 'dbauth':
					include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.dbauth.php');
					$this->handleMessage("Authenticationmethod: {$this->authmethod}");
					return new DbAuth($this->dbo, $this->logger);
					break;
					
			case 'simpledbldapauth':
					include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.simpledbldapauth.php');
					$this->handleMessage("Authenticationmethod: this: {$this->authmethod}");
					return new SimpleDbLdapAuth($this->dbo, $this->logger);
					break;
					
			case 'querydbldapauth':
					//TODO
					$this->handleMessage("Authenticationmethod: this: {$this->authmethod}");
					break;
					
			default:
					//TODO return a standard authentication object, nor none?
					include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.dbauth.php');
					$this->handleMessage("Authenticationmethod: DEFAULT {$this->authmethod}<br> No Authentication Method set.");
					return new DbAuth($this->dbo, $this->logger);
					break;
		}
			
	} //selectReturnObject


	public function execute() {
		// Point to main function of this Factory
		return $this->selectReturnObject();
	}
	
	


	public function getAuthMethod() {
		return $this->authmethod;
	}
	private function setAuthMethod($authmethod) {
		$this->authmethod = $authmethod;
	}


	public function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	public function registerlogger($logger) {
		$this->logger = $logger;
	}
	


	public function setVariable($name, $value) {
		$this->vars[$name] = $value;
	}
	public function getVariable() {
		return $this->vars;
	}
	protected function setName($name) {
  		$this->name = $name;
  	}
	protected function getName() {
  		return $this->name;
  	}
  	
 	public function handleMessage($msg) {
		$str = "<span style='color: lightblue;'>Authfactory.msg: {$msg}</span><br>";
   		$this->logger->addMsg($str);
	}
	public function handleError($msg) {
   		//try error catching??? and Error Handling with Exceptions
   		$str = "<span style='color: lightblue;'>Authfactory.err: {$msg}</span><br>";
   		$this->logger->addErr($str);
	}
	public function __toString() {
		$str = "<span style='color: lightblue;'>Authfactory.str: {$this->authmethod}</span><br>";
		return $str;
	}

	
} // class AuthFactory

?>

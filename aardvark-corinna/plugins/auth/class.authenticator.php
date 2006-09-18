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

include_once(bm_baseDir.'/inc/interface.plugin.php');
include_once(bm_baseDir.'/pluginadmin/utils/db_confighandler.class.php');


class Authenticator implements iPlugin {

	/**
	 * Class Variables
	 */
	protected $name 	= "";			// is the identifier for the object
	protected $var		= array();		// get/set Variables array
	protected $ldapconf = array();		// holds the information given in modconf on our ldap connection
	private $dbo;						// database handle
	
	
	/**
	 * Class Methods
	 */
	public function __construct($dbo) {
		$this->setName("Authenticator Object");
		$this->registerdbo($dbo);
		$this->initconfig();
	} //Constructor
	
	public function __destruct() { 	
	}

	public function registerdbo($dbo) {
		$this->dbo = $dbo;
	}

	public function initConfig() {

		//TODO registerdbo machen
		if ($this->dbo != NULL) {

			//read config from database
			$conf = new ConfigHandler($this->dbo);
			//$authid = $conf->dbGetIdForName("Auth");
			//$config = $conf->dbGetConfig($authid);
			$config = $conf->dbGetConfigByName("Auth");

			//CHECK AND MAKE CONFIG
			$this->ldapconf['ldap_server']		= $config['ldap_server'];
			$this->ldapconf['ldap_port']		= $config['ldap_port'];
			$this->ldapconf['ldap_authmethod']	= $config['ldap_authmethod'];
			$this->ldapconf['ldap_user']		= $config['ldap_user'];
			$this->ldapconf['ldap_pass']		= $config['ldap_pass'];
			$this->ldapconf['ldap_base']		= $config['ldap_base'];
			$this->ldapconf['ldap_dn']			= $config['ldap_dn'];
	
		} else {
			$this->handleError("Configuration could not be loaded from Database");
		}
	} //initConfig 


	public function execute() {
		/* I do nothing because im a dummy *haha */
	}



	public function getAuthMethod() {
		return $this->ldapconf['ldap_authmethod'];
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
		//TODO 
		//For non errors like: User not accepted by ldap server
	}
	public function handleError($msg) {
   		//try error catching??? and Error Handling with Exceptions
   		//TODO ALTER TO LOGGER
   		echo "<div style='border: 1px #FF0033 dotted; width:300px; text-align:center; padding:10px;'>" .
   				"<b>->Authenticator Error::: {$msg}</b><br></div>";
	}
  	
	public function __toString() {
   		$str  = "<br>--------{$this->name}--------<br>";
   		$str .= "-----ldapconf-----<br>";
   		$str .= "Server: " . $this->ldapconf['ldap_server'] 	. "<br>";
		$str .= "Port:   " . $this->ldapconf['ldap_port'] 		. "<br>";
		$str .= "Authen: " . $this->ldapconf['ldap_authmethod'] . "<br>";
		$str .= "User:   " . $this->ldapconf['ldap_user'] 		. "<br>";
		$str .= "Pass:   " . $this->ldapconf['ldap_pass'] 		. "<br>";
		$str .= "Base:   " . $this->ldapconf['ldap_base'] 		. "<br>";
		$str .= "DNbase: " . $this->ldapconf['ldap_dn'] 		. "<br>";
		$str .= "-----var-----<br>";
		foreach(array_keys($this->vars) as $key=>$value) {
			$str .= "<b>". $value ." ----> ". $this->vars[$value] ."</b><br>";
		}
   	   	$str .= "----------------------------------------------<br><br>";
   	   	return $str;
	}
	



	/***** TODO  -> überlegen ***/
  	public function checkParameter() {  		
  	}
  	
	public function register() {
	}
	
	public function getIdentity() {
	}
   
   
} // class Authenticator

?>

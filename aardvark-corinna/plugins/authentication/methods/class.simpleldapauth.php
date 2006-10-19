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

include_once(bm_baseDir.'/plugins/pluginregistry/interfaces/interface.authent.php');

//include_once(bm_baseDir.'/plugins/pluginregistry/inc/class.db_confighandler.php');
include_once(bm_baseDir.'/plugins/authentication/methods/class.db_authhandler.php');
include_once(bm_baseDir.'/plugins/authentication/class.auth.php');


class SimpleLdapAuth extends Auth implements iAuthent {
 	
 	private $ldapconf	= NULL;
 	private $dbhandler = NULL;
 	private $authmethod = "simpleldapauth";
 
 	public function __construct($dbo, $logger) {
 		parent::__construct($dbo, $logger);
 		$this->dbhandler = new AuthHandler($dbo);
		$this->getConfigFromDb();
 	}
 	public function __destruct() {
 		unset($this->ldapconf);
 	}


 	public function printAuthMethod() {
 		return $this->authmethod;
 	}


 	public function getConfigFromDb() {

		$mydbo = $this->getdbo();
		
		if ($mydbo != NULL) {

			// Read config from database & get Configuration for SIMPLELDAPAUTH
			//$conf = new ConfigHandler($mydbo);
			//$config = $conf->dbGetConfigByName($this->authmethod);
			$config = $this->dbhandler->dbGetConfigByName($this->authmethod);
			
			//CHECK AND MAKE CONFIG
			$this->ldapconf['simpleldap_server']	= $config['simpleldap_server'];
			$this->ldapconf['simpleldap_port']		= $config['simpleldap_port'];
			$this->ldapconf['simpleldap_dn']		= $config['simpleldap_dn'];
			
		} else {
			$this->handleMessage("Configuration could not be loaded from database. Database is null.");
		}

 	} //getConfigFromDb
 	
 	
 	public function authenticate($user, $md5pass) {

		$ldapconn = "";
		
		//$this->handleMessage("<br> Testing user {$user} with pass {$md5pass}<br>");												//TODO weg

		//Construct server url 
		//TODO ADD MORE CHECKS
		$server = $this->ldapconf['simpleldap_server'];
		if(stristr($server, $this->ldapconf['simpleldap_port']) === FALSE) {
			$server .= $this->ldapconf['simpleldap_port'];
		} 
		//echo "New server: {$server}<br>";

		// Connect to server
		//TODO Add more checks
		if ($this->ldapconf['simpleldap_server']) {
			$ldapconn = ldap_connect($this->ldapconf['simpleldap_server']);		// or die( "connect: Connection to {$this->ldapuri} unavailable.<br>" );	// is dirty
		} elseif ($server) {
			$ldapconn = ldap_connect($server);
		} else {
			$this->handleError("Host not reachable: {$this->ldapconf['simpleldap_server']}. Check simpleldapauth config.");
			return FALSE;
		}
		
		
		if ($ldapconn) {
			ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

			//Set Protocol to LDAPv3
			if (!ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
				$this->handleError("Failed to set LDAP Protocol version to 3, TLS not supported.");
				return FALSE;
			}
			
			// if user or pass is empty
			if ( (empty($user)) || (empty($md5pass)) )  {

				$this->handleMessage("Password or user field empty.");
				return FALSE;
				
			} else {

				// Check if DN Substring is provided in the input
				if(stristr($user, $this->ldapconf['simpleldap_dn']) === FALSE) {
					//echo "-> {$this->ldapconf['simpleldap_dn']} not found. concatenating.<br>";				//TODO weg
					$user .= $this->ldapconf['simpleldap_dn'];
				} /* else {
					echo "-> {$this->ldapconf['simpleldap_dn']} found.<br>";								//TODO weg
				}*/


				//What to do to get rid of the warning? 
				//Warning: ldap_bind() [function.ldap-bind]: Unable to bind to server: 
				//$ldapbind = ldap_bind($ldapconn, $user, $md5pass);
				//if ($ldapbind) {

				if (ldap_bind($ldapconn, $user, $md5pass)) {
					// Bind with this credentials went ok! Authentication ok!
					$this->handleMessage("LDAP Authentication passed.");
					ldap_close($ldapconn);
					return TRUE;
				} else {
					//Invalid credentials, Authentication failed
					$this->handleMessage("LDAP Authentication failed!");
					ldap_close($ldapconn);
					return FALSE;
				}
			}
		} else {
			//Connect not ok!
			$this->handleError("Connect to LDAP/ADS DB failed.");
			ldap_close($ldapconn);
			return FALSE;
		}
		
	} //authenticate
	

} // SimpleLdapAuth

?>

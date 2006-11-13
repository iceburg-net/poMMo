<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 22.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


include_once(bm_baseDir.'/plugins/adminplugins/adminuser/interfaces/interface.authent.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/class.auth.php');

//include_once(bm_baseDir.'/plugins/pluginregistry/inc/class.db_confighandler.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.db_authhandler.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.simpleldapauth.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.dbauth.php');

class SimpleDbLdapAuth extends Auth implements iAuthent { 

 	private $dbconf	= NULL;
 	private $dbhandler = NULL;
 	private $ldapauth;
 	private $dbauth;
 	
 	private $authmethod = "simpledbldapauth";
 	
 	public function __construct($dbo, $logger) {
 		parent::__construct($dbo, $logger);
 		$this->dbhandler = new AuthHandler($dbo);
 		$this->getConfigFromDb();
 	}
 	public function __destruct() {
 		unset($this->dbconf);
  		unset($this->ldapauth);
  		unset($this->dbauth);
 	}

 	public function printAuthMethod() {
 		return $this->authmethod;
 	}

 	public function getConfigFromDb() {

		$mydbo = $this->getdbo();
		if ($mydbo != NULL) {

				// get new configration also
				// TODO
				//$config = $this->dbhandler->dbGetConfigByName($this->authmethod);

				$this->ldapauth = new SimpleLdapAuth($mydbo, $this->getlogger());
				$this->dbauth = new DbAuth($mydbo, $this->getlogger());

		} else {
			$this->handleError("Configuration could not be loaded from Database");
		}

 	} //getConfig


	public function authenticate($user, $md5pass) {
		
		//TODO
		// IF NOT IN DB auth per LDAP and then insert in DB
		// with flag for administrator
	
	/*	if ($this->dbauth->authenticate($user, $md5pass)) {
			echo "JA-true<br>";
		} else {
			echo "NEIN-FALSE<br>";
		}
		if ($this->ldapauth->authenticate($user, $md5pass)) {
			echo "LDAP JA-true<br>";
		} else {
			echo "LDAP NEIN-FALSE<br>";
		}	
		*/
		
		$dba = $this->dbauth->authenticate($user, $md5pass);
		$ldapa = $this->ldapauth->authenticate($user, $md5pass);

		if ($ldapa && $dba) {
			// LDAP auth ok und db auth auch
			$this->handleMessage("authenticated with both.");
			return TRUE;
		} elseif ($ldapa && !$dba) {
			// LDAP ok aber db nicht
			$this->handleMessage("with LDAP but not in DB.");
			// das das fehlt in DB schreiben
			return FALSE;
		} elseif (!$ldapa && $dba) {
			//DB ok aber LDAP nicht
			$this->handleMessage("with DB but not in LDAP.");
			// das das fehlt in DB schreiben
			return FALSE;
		} elseif (!$ldapa && !$dba) {
			$this->handleMessage("both authentication failed.");
			return FALSE;
		} else {
			$this->handleMessage("both not passed.");
			return FALSE;
		}

	
	} //authenticate
	
} // SimpleDbLdapAuth

?>


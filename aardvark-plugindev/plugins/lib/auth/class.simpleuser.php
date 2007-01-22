<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 19.01.2007
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


class SimpleUser implements User {

	private $_usertype;
	private $_uid;
	private $_username;
	private $_md5pass;
	private $_permissionLevel;
	private $_authenticated;
	
	private $_authmethod;
	private $_verifier;
	
	private $dbauth;
	private $sldapauth;
	private $qldapauth;
	
	public function __construct($username, $md5pass) {
		$this->_usertype = "simpleuser";
		$this->_uid = NULL;
		$this->_username = $username;
		$this->_md5pass = $md5pass;
		$this->_permissionLevel = 0;
		$this->_authenticated = FALSE;
		
		$this->dbauth = FALSE;
		$this->sldapauth = FALSE;
		$this->qldapauth = FALSE;
		
		$this->_authmethod = array();
		$this->_verifier = array();
	} //Constructor
	public function __destruct() {
		unset($this->_uid);
		unset($this->_username);
		unset($this->_md5pass);
		unset($this->_permissionLevel);
		unset($this->_authenticated);
		
		//unset
		$this->dbauth = FALSE;
		$this->sldapauth = FALSE;
		$this->qldapauth = FALSE;
		
		unset($this->_authmethod);
		unset($this->_verifier);
	} //Destructor
	
	/**
	 *  authenticate with auth methods that are activated in the GENERAL PLUGIN SETUP
	 */
	public function authenticate() {

		global $pommo;

		if (isset($this->_authmethod) AND isset($this->_verifier)) {
			
			$dba = $sldapa = $qldapa = FALSE;
			
			
			// AUTH METHODS COMBINATIONS
			
			//TRUES
			if ($this->dbauth AND !$this->sldapauth AND !$this->qldapauth) {

				// only dbauth activated
				
				$dba = $this->_verifier['dbauth']->verifyUser($this->_username, $this->_md5pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth active AND {$dba}.</div>');
				$this->_authenticated = $dba;
				
				return $dba;
				
			} elseif (!$this->dbauth AND $this->sldapauth AND !$this->qldapauth) {
				
				// only simple ldapauth activated
				
				$sldapa = $this->_verifier['simpleldapauth']->verifyUser($this->_username, $this->_md5pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: simpleldapauth active AND {$sldapa}.</div>');
				$this->_authenticated = $sldapa;
				
				return $sldapa;
				
			} elseif (!$this->dbauth AND !$this->sldapauth AND $this->qldapauth) {
			
				//only query ldap auth activated
				
				$qldapa = $this->_verifier['queryldapauth']->verifyUser($this->_username, $this->_md5pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: queryldapauth active AND {$qldapa}.</div>');
				$this->_authenticated = $qldapa;
				return $qldapa;
			
			} elseif ($this->dbauth AND $this->sldapauth AND !$this->qldapauth) {
			
				// dbauth AND simple ldapauth activated
				
				$dba = $this->_verifier['dbauth']->verifyUser($this->_username, $this->_md5pass);
				$sldapa = $this->_verifier['simpleldapauth']->verifyUser($this->_username, $this->_md5pass);
				
				//TRUE
				if ($dba AND $sldapa) {
					//passed both
					$this->dbWriteLastLogin($this->_username);
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, passed both: {$dba} - {$sldapa}.</div>');
					$this->_authenticated = TRUE;
					return TRUE;
				} elseif (!$dba AND $sldapa) {
					// not in db but ldap passed
					//TODO if (dbauth_writeldapusertodb)
					$this->dbAddLDAPUser($this->_username, $this->_md5pass);
					$this->dbWriteLastLogin($this->_username);
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, ldap passed, db not: {$dba} - {$sldapa}.</div>');
					$this->_authenticated = TRUE;
					return TRUE;

				// FALSE
				} elseif ($dba AND !$sldapa) {
					//in db but not ldapauth
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active AND db passed, ldap not: {$dba} - {$sldapa}.</div>');
					$this->_authenticated = FALSE;
					return FALSE;
				} elseif (!$dba AND !$sldapa ) {
					// both not passed
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, both not passed: {$dba} - {$sldapa}.</div>');
					$this->_authenticated = FALSE;
					return FALSE;
				} else {
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, something else wrong: {$dba} - {$sldapa}.</div>');
					$this->_authenticated = FALSE;
					return FALSE;
				}
				
			
			
			//FALSES
			} elseif (!$this->dbauth AND !$this->sldapauth AND !$this->qldapauth) {
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: all three auth methods active.</div>');
				return FALSE;
			} else {
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: Corrupted auth methods.</div>');
				return FALSE;
			}
	
		} else {
			$pommo->_logger->addMsg('SimpleUser: No authmethod set. See General Plugin Setup.');
			$this->_authenticated = FALSE;
			return FALSE;
		}
		
	} //authenticate


	public function isAuthenticated() {
		if (isset($this->_authenticated))
			return $this->_authenticated;
		else
			return FALSE;
	}
	public function getUsertype() {
		return $this->_usertype;
	}
	public function getUsername() {
		return $this->_username;
	}
	public function getPermissionLevel() {
		return $this->_permissionLevel;
	}
	
	
	/**
	 * The $authmethod is selected , maybe move all this tho the constructor? -> shorter
	 * Get the activated authentication methods db/ldap from db and generate a checkl object for each one
	 * and store them in the verifier array.
	 */
	public function setAuthMethod() {

		global $pommo;
		$this->_authmethod = $this->dbGetAuthMethod();
		
		for ($i = 0; $i < sizeof($this->_authmethod); $i++) {
			 
			switch ($this->_authmethod[$i]) {

					case 'dbauth':
							Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.dbauth.php');
							$this->_verifier['dbauth'] = new DbAuth();
							$this->dbauth = TRUE;
						break;
					
					case 'simpleldapauth':
							Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.simpleldapauth.php');
							$this->_verifier['simpleldapauth'] = new SimpleLdapAuth();
							$this->sldapauth = TRUE;
						break;
							
					case 'queryldapauth':
							Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.queryldapauth.php');
							$this->_verifier['queryldapauth'] = new QueryLdapAuth();
							$this->qldapauth = TRUE;
						break;
							
					default:
							//TODO return a standard authentication object, nor none?
							$pommo->_logger->addMsg("No Authentication Method set. Check the General plugin Setup.<br>");	
						break;
						
				} //switch
				
		} //for
			
	} //setAuthMethod
	



	/**
	 * Get the active authentication methods from DB
	 * returns a array of activated auth methods
	 */
	private function dbGetAuthMethod() {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$m = array();
		
		// other query
		$query = "SELECT plugin_uniquename FROM " . $dbo->table['plugin'] .
				" WHERE (plugin_uniquename = 'simpleldapauth' AND plugin_active = 1) OR " .
				" (plugin_uniquename = 'queryldapauth' AND plugin_active = 1) OR " .
				" (plugin_uniquename = 'dbauth' AND plugin_active = 1)";

		// Can be a array if more plugins are activated
		
		$query = $dbo->prepare($query);
		
		$i = 0;
		while ($row = $dbo->getRows($query)) {
			$m[$i] = $row['plugin_uniquename'];
			$i++;
		}
		return $m;
		
	} //dbGetAuthMethod



	 
	public function dbWriteLastLogin($username) {
	
		global $pommo;
		$dbo = $pommo->_dbo;
		
		$sql = $dbo->_safeSQL->query("UPDATE %s SET user_lastlogin=NOW() WHERE user_name='%s' ",
				array($dbo->table['user'], $username) );
			$dbo->query($sql);
		$sql = $dbo->_safeSQL->query("UPDATE %s SET user_lastlogin=NOW() WHERE user_name='%s' ",
				array($dbo->table['user'], $username) );
			$dbo->query($sql);
	}
	
	public function dbAddLDAPUser($user, $pass) {

		global $pommo;
		$dbo = $pommo->_dbo;

		$sql = $dbo->_safeSQL->query("INSERT INTO %s (user_name, user_pass, user_perm, user_created) " .
				"VALUES ('%s', '%s', NULL, NOW()) ",
				array($dbo->table['user'], $user, $pass ) );
			$dbo->query($sql);
	}
	 
	 
	 
	 



} //SimpleUser


/***
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
		* /
		
	
		$ldapa = $this->ldapauth->authenticate($user, $md5pass);	
		// if true??
		$dba = $this->dbauth->authenticate($user, $md5pass);	//$md5pass

		// TRUES!
		if ($ldapa && $dba) {
			// LDAP auth ok und db auth auch
			$pommo->_logger->addMsg("authenticated with both.");
			$this->dbhandler->dbWriteLastLogin($user); 
			return TRUE;
		} elseif ($ldapa && !$dba) {
			// LDAP ok aber db nicht
			$pommo->_logger->addMsg("with LDAP but not in DB.");
			// das das fehlt in DB schreiben
			$this->dbhandler->dbAddLDAPUser($user, $md5pass);	// ein komisches Passwort generieren
			$this->dbhandler->dbWriteLastLogin($user); 
			return TRUE;	//!! IF  	dbldap_insertldaptodb aus CONFIG!!!
		} elseif (!$ldapa && $dba) {
			//DB ok aber LDAP nicht
			$pommo->_logger->addMsg("with DB but not in LDAP.");
			// das das fehlt in DB schreiben
			$this->dbhandler->dbWriteLastLogin($user); 
			return FALSE;
			
		// FALSES!
		} elseif (!$ldapa && !$dba) {
			$pommo->_logger->addMsg("both authentication failed.");
			return FALSE;
		} else {
			$pommo->_logger->addMsg("both not passed.");
			return FALSE;
		}
		


	
	} //authenticate

 */

?>

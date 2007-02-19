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


class SimpleUser {

	var $_usertype;
	var $_uid;
	var $_username;
	var $_md5pass;
	var $_permissionLevel;
	
	var $_verifier;
	var $dbauth;
	var $sldapauth;
	var $qldapauth;
	
	function SimpleUser($username, $md5pass) {

		$this->_usertype = "simpleuser";
		$this->_uid = NULL;
		$this->_username = $username;
		$this->_md5pass = $md5pass;
		$this->_permissionLevel = 0;
		
		$this->_verifier = NULL;
		
		global $pommo;
		
		
		$this->dbauth = $pommo->_plugindata['authmethod']['dbauth'];
		$this->sldapauth = $pommo->_plugindata['authmethod']['simpleldapauth'];
		$this->qldapauth = $pommo->_plugindata['authmethod']['queryldapauth'];
		
		if ($pommo->_plugindata['authmethod']['dbauth']) {
			Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.dbauth.php');
			$this->_verifier['dbauth'] = new DbAuth();
			//$this->dbauth = $pommo->_plugindata['authmethod']['dbauth'];
		}
		if ($pommo->_plugindata['authmethod']['simpleldapauth']) {
			Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.simpleldapauth.php');
			$this->_verifier['simpleldapauth'] = new SimpleLdapAuth();
			//$this->sldapauth =  $pommo->_plugindata['authmethod']['simpleldapauth'];
		}
		if ($pommo->_plugindata['authmethod']['queryldapauth']) {
			Pommo::requireOnce($pommo->_baseDir.'plugins/lib/auth/methods/class.queryldapauth.php');
			$this->_verifier['queryldapauth'] = new QueryLdapAuth();
			//$this->qldapauth =  $pommo->_plugindata['authmethod']['queryldapauth'];
		}
		
	} //Constructor
	
	function __destruct() {
		unset($this->_uid);
		unset($this->_username);
		unset($this->_md5pass);
		unset($this->_permissionLevel);
	} //Destructor


	function getPermissionLevel() {
		return $this->_permissionLevel;
	}
	function getUserID() {
		return $this->_uid;
	}




	
	/**
	 *  authenticate with auth methods that are activated in the GENERAL PLUGIN SETUP
	 */
	function authenticate() {

		global $pommo;

			$dba = $sldapa = $qldapa = FALSE;
		
			
			// AUTH METHODS COMBINATIONS
			
			//TRUES
			if ($this->dbauth AND !$this->sldapauth AND !$this->qldapauth) {

				// only dbauth activated
				
				$dba = $this->_verifier['dbauth']->verifyUser($this->_username, $this->_md5pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth active AND {$dba}.</div>');
				$this->_permissionLevel = $this->dbGetPermissionLevel();
				return $dba;
				
			} elseif (!$this->dbauth AND $this->sldapauth AND !$this->qldapauth) {
				
				// only simple ldapauth activated
				
				$sldapa = $this->_verifier['simpleldapauth']->verifyUser($this->_username, $this->_md5pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: simpleldapauth active AND {$sldapa}.</div>');
				$this->_permissionLevel = $this->dbGetPermissionLevel();
				return $sldapa;
				
			} elseif (!$this->dbauth AND !$this->sldapauth AND $this->qldapauth) {
			
				//only query ldap auth activated
				
				$qldapa = $this->_verifier['queryldapauth']->verifyUser($this->_username, $this->_md5pass);
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: queryldapauth active AND {$qldapa}.</div>');
				$this->_permissionLevel = $this->dbGetPermissionLevel();
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
					$this->_permissionLevel = $this->dbGetPermissionLevel();
					return TRUE;
					
				} elseif (!$dba AND $sldapa) {
					// not in db but ldap passed
					//TODO if (dbauth_writeldapusertodb)
					$this->dbAddLDAPUser($this->_username, $this->_md5pass);
					//$this->dbWriteLastLogin($this->_username);
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, ldap passed, db not: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = $this->dbGetPermissionLevel();
					return TRUE;

				// FALSE
				} elseif ($dba AND !$sldapa) {
					//in db but not ldapauth
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active AND db passed, ldap not: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = 0;
					return FALSE;
					
				} elseif (!$dba AND !$sldapa ) {
					// both not passed
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, both not passed: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = 0;
					return FALSE;
					
				} else {
					$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: dbauth&sldap active, something else wrong: {$dba} - {$sldapa}.</div>');
					$this->_permissionLevel = 0;
					return FALSE;
				}
				
			
			
			//FALSES
			} elseif (!$this->dbauth AND !$this->sldapauth AND !$this->qldapauth) {
				$pommo->_logger->addMsg('SimpleUser: No authmethod set. See General Plugin Setup.');
				$this->_permissionLevel = 0;
				return FALSE;
			} else {
				$pommo->_logger->addMsg('<div style="color: blue;">SimpleUser: Corrupted auth methods.</div>');
				$this->_permissionLevel = 0;
				return FALSE;
			}
		
	} //authenticate
	


	function dbGetPermissionLevel() {
		
		global $pommo;
		//$this->dbo =& $pommo->_dbo; 
		$dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT user_permissionlvl FROM " . $dbo->table['user'] . 
			" WHERE user_name = '". $this->_username ."' LIMIT 1 "; 

		$query = $dbo->prepare($query);
		
		if ($row = $dbo->getRows($query))
			$a = $row['user_permissionlvl'];
		
		return $a;
	
	} //dbGetPermissionLevel
	

} //SimpleUser


?>

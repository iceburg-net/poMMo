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


/*
 * Having a separate Admin class makes sense: (linux way)
 * - there should only be this authentification with the config values,
 * 	so that in case someone corrupts the authentication plugin
 * 	the "real" administrator can always reset the authentication methods 
 * 	to the intended method. You could not login into the system anymore if
 *  the auth goes only through the user management system
 * - You can define other administrators as well with the user functionality
 * 	with all the necessary rights/permissions.
 *  If these permissions are somehow corrupted you have the option to reset 
 *  them with to the intended state through this separated Admin class because 
 *  the admin has always all privileges and these are not checked when you log 
 *  on with the config admin. 
 * - OR ldap server hangs/is broken and you don't reach pommo anymore
 * - This is some kind of linux style root user, whitch is a common way.
 */

class AdminUser implements User {
	
	private $_usertype;
	private $_uid;
	private $_username;
	private $_md5pass;
	private $_permissionLevel;
	private $_authenticated;


	public function __construct($username, $md5pass) {
		$this->_usertype = "adminuser";
		$this->_uid = NULL;
		$this->_username = $username;
		$this->_md5pass = $md5pass;
		$this->_permissionLevel = 0;
		$this->_authenticated = FALSE;
	} //Constructor

	public function __destruct() {
		unset($this->_uid);
		unset($this->_username);
		unset($this->_md5pass);
		unset($this->_permissionLevel);
		unset($this->_authenticated);
	} //Destructor
	

	/**
	 * authenticate standard administrator way, data lies in the config table
	 */
	public function authenticate() {

		global $pommo;

		if ($this->dbVerifyAdmin()) {
			$pommo->_logger->addMsg("Administrator found. Welcome.");
			$this->_authenticated = TRUE;
			return TRUE;
		} else {
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
	 *  don' allow access from outside: private
	 *	maybe split up in 2 db / data management classes
	 *	i let it compact at this time we have no other database system
	 *
	 *	returns TRUE if administrator is ok, else FALSE
	 */ 
	private function dbVerifyAdmin() {

		global $pommo;
		//TODO clone question -> talk to brice about this
		$dbo = clone $pommo->_dbo;

		$a = array();
		
		$query = "SELECT config_value FROM ".$dbo->table['config'].
				 " WHERE config_name = 'admin_password' AND config_value = '".$this->_md5pass."' LIMIT 1  "; 

		$query = $dbo->prepare($query);
		
		if ($row = $dbo->getRows($query)) {
		
					if ($dbo->affected() == 1) {
						$this->_permissionLevel = 5;
						$this->_uid = 0; // Administrator has Id:0 (unix way);
						return TRUE;
					}
		}
		
		$pommo->_logger->addMsg("AdminUser: Login failed.");
		return FALSE;
		
	} //dbVerifyAdmin
	
	
} //AdminUser

?>

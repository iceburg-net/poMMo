<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 16.01.2007
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

// MultiUser Authentication class
// this class exchanges the inc/classes/auth.php class PommoAuth {}


Pommo::requireOnce($this->_baseDir . 'plugins/lib/class.pluginhandler.php');
Pommo::requireOnce($this->_baseDir . 'plugins/lib/auth/class.simpleuser.php');
Pommo::requireOnce($this->_baseDir . 'plugins/lib/auth/class.adminuser.php');


/**
 * Defines the authentication method use 
 * database auth, LDAP auth ....
 * This is defined in the GENERAL PLUGIN SECTION, where you can choose
 * one or more of these options (all methods for very restrictive and 
 * intensive checks). Although a standard pommo installation probably
 * does not need LDAP authentication.
 */
class MultAuth { 

	var $user;
	var $authenticated;

	var $_username;	// current logged in user (default: null|session value)
	var $_permissionLevel; // permission level of logged in user
	var $_requiredLevel; // required level of permission (default: 1)
	
	

	function MultAuth($args = array ()) {

		global $pommo;

		$this->user = null;
		$this->authenticated = FALSE;

		$defaults = array (
			'username' => null,
			'requiredLevel' => 0
		);
		
		$p = PommoAPI :: getParams($defaults, $args);
		
		
		if (empty($pommo->_session['username']))
			$pommo->_session['username'] = $_SESSION['pommo123456']['username'];//$p['username'];
		

		
		$this->_username = & $pommo->_session['username'];
		$this->_permissionLevel = $_SESSION['pommo123456']['permlvl'];//$this->getPermissionLevel($this->_username);
		$this->_requiredLevel = $p['requiredLevel'];
		

		if ($p['requiredLevel'] > $this->_permissionLevel) {
			global $pommo;
			Pommo::kill(sprintf(Pommo::_T('Denied access. You must %slogin%s to access this page...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
		}
		
	} //constructor






	function authenticate($username, $md5pass) {
		
		// auth process dann in dbauth und so
		
		global $pommo;

		$dbhelper = new PluginHandler();
		$alias = $dbhelper->dbGetAdminAlias();
		

		//construct the user object	
		if ($username == $alias) {
			$this->user = new AdminUser($alias, $md5pass);
		} else {

			if ($pommo->_plugindata['pluginmultiuser']) {
				$this->user = new SimpleUser($username, $md5pass);
			} else {
				$pommo->_logger->addMsg("MultAuth: plugins not enabled. try to enable in config.php");
			}

		}
		
		// if userobject is constructed do the authentication
		if ($this->user) {
			if ( $this->user->authenticate() ) {
				$key = '123456';
				$_SESSION['pommo'.$key]['username'] = $username;
				$_SESSION['pommo'.$key]['md5pass'] = $md5pass;
				$_SESSION['pommo'.$key]['id'] = $this->user->getUserID();
				$_SESSION['pommo'.$key]['permlvl'] = $this->user->getPermissionLevel();
				$dbhelper->dbWriteLastLogin($username);
				$dbhelper->dbIncreaseLoginTries($username);
				$this->authenticated = TRUE;
				return TRUE;
			} else {
				$this->authenticated = FALSE;
				session_destroy();
				return FALSE;
			}
		} else {
			$pommo->_logger->addMsg("MultAuth: authenticate: No user object found.");
			$this->authenticated = FALSE;
			return FALSE;
		}
		
			
	} //authenticate


	function isAuthenticated() {
		return $this->authenticated;
	}


	function getPermissionLevel($username = null) {
		/*if ($username)
			return 5;
		return 0;*/
		
		if ($this->username AND $this->authenticated) {
			$key = '123456';
			$permlvl = $_SESSION['pommo'.$key]['id'];
			
			return $permlvl;
		}
		
		// no permission
		return 0;
	}
	
	function logout() {
		$this->_username = null;
		$this->_permissionLevel = 0;
		session_destroy();
		return;
	}
	
	function login($username) {
		$this->_username = $username;
		return;
	}
	


	/**
	 * permissiontype is a STRING that denotes the permission needed/enabled for a user to enter the site
	 */
	/**
	 all permissions table:
	 SELECT user_name, permgroup_name, perm_name FROM pommomod_user AS u RIGHT JOIN pommomod_permgroup AS pg ON u.permgroup_id=pg.permgroup_id
RIGHT JOIN pommomod_pg_perm AS pgp ON pg.permgroup_id=pgp.permgroup_id 
RIGHT JOIN pommomod_permission AS p ON pgp.perm_id=p.perm_id
ORDER BY user_name


1 permission for a user:
SELECT user_name, permgroup_name, perm_name FROM pommomod_user AS u RIGHT JOIN pommomod_permgroup AS pg ON u.permgroup_id=pg.permgroup_id
RIGHT JOIN pommomod_pg_perm AS pgp ON pg.permgroup_id=pgp.permgroup_id 
RIGHT JOIN pommomod_permission AS p ON pgp.perm_id=p.perm_id
WHERE u.user_name='corinna' AND p.perm_name='PLUGINADMIN'
ORDER BY user_name
	 */
	function dbCheckPermission($permissiontype) {
	
		if ($this->_username == 'admin'){ // AND $this->isAuthenticated()) {
		
			return TRUE;
		
		} else {
			global $pommo;
			//$this->dbo =& $pommo->_dbo; 
			$dbo = clone $pommo->_dbo;
			
			//a = array();
			
			$query = "SELECT user_name, permgroup_name, perm_name " .
					"FROM ".$dbo->table['user']." AS u RIGHT JOIN ".$dbo->table['permgroup']." AS pg ON u.permgroup_id=pg.permgroup_id " .
					"RIGHT JOIN ".$dbo->table['pg_perm']." AS pgp ON pg.permgroup_id=pgp.permgroup_id " .
					"RIGHT JOIN ".$dbo->table['permission']." AS p ON pgp.perm_id=p.perm_id " .
					"WHERE u.user_name='".$this->_username."' AND p.perm_name='".$permissiontype."' ";
	
			$query = $dbo->prepare($query);
			
			if ($row = $dbo->getRows($query)) {
				if ($dbo->affected() == 1) {
					$pommo->_logger->addMsg("PERMISSION found for this plugin");
					return TRUE;	
				} else {
					Pommo::kill("Permission not found");
					return FALSE;
				}
			}
			
			Pommo::kill("Permission not found!");
			return FALSE;
		}
		
		Pommo::kill("Permission error");
		return FALSE;
		
	} //dbCheckPermission





} //MultAuth

/*
 * 
 * 
 * 
 * 
 * 	function MultAuth() {
		
		global $pommo;
		
		$this->user = null;
		$this->md5pass = null;
		$this->id = null;
		$this->permlvl = null;
		
		// Take session data
		session_start();
		
		if ($_SESSION['pommo']) {
			$this->user = $_SESSION['pommo']['coruser'];
			$this->md5pass = $_SESSION['pommo']['corpass'];
			$this->id = $_SESSION['pommo']['corid'];
			$this->permlvl = $_SESSION['pommo']['corid'];
		} else {
			//echo "HTTPPPPPPPPPP:".$this->_http;
			//echo "BASEURLLLLLLL:".$this->_baseUrl;	
			//Pommo::redirect($pommo->_http . $pommo->_baseUrl . 'index.php');
		}
		
	} //constructor



	function authenticate($user, $md5pass) {
		
		if ($user == "corinna" AND $md5pass == "corinna") {
			$_SESSION['pommo']['coruser'] = $user;
			$_SESSION['pommo']['corpass'] = $md5pass;
			$_SESSION['pommo']['corid'] = 5;
			$_SESSION['pommo']['permlvl'] = 5;
			return TRUE;

		} else {

			session_destroy();

			return FALSE;

		}
		
	} //authenticate
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
	var $authenticated;
	
	
	function MultAuth() {

		$this->user = NULL;
		$this->authenticated = FALSE;
		
		$key = "123456";
		if (!empty($_SESSION['pommo'.$key]['username'])) {
			echo "not empty";
			
		} else {
			//Pommo::kill(sprintf(Pommo::_T('Multiuser: Denied access. You must %slogin%s to access this page...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
		}
		
		
	
	} //Constructor


	// User Constructor constructs a object of type SimpleUser or AdminUser
	// depending if the loginscreen name is the admin alias or an other login name
	// because the authentication process is done later then the $pommo generating
	// i designed it a separate function
	function constructUser($username, $md5pass) {

		global $pommo;

		//$dbhelper = new PluginHandler();
		$alias = "admin"; //$dbhelper->dbGetAdminAlias();
		
	
		if ($username == $alias) {
			$this->user = new AdminUser($alias, $md5pass);
		} else {
			if ($pommo->_plugindata['pluginmultiuser']) {
				$this->user = new SimpleUser($username, $md5pass);
			} else {
				$pommo->_logger->addMsg("MultAuth: constructuser: plugins not enabled. try to enable in config.php");
			}
		}
		
	} //constructuser


	// Function needed because in index.php before the authentication 
	// process we execute this function
	function isAuthenticated() {

		//  maybe recheck here with authenticate?


	
		return FALSE;
	
	}


	//if daten vorhanden sonst FALSE
	function authenticate() {
	
		global $pommo; 
		
		if ($this->user) {
			if ( $this->user->authenticate() ) {
				$this->authenticated = TRUE;
				return TRUE;
			} else {
				$this->authenticated = TRUE;
				return FALSE;
			}
		} else {
			$pommo->_logger->addMsg("MultAuth: authenticate: No user object found.");
			return FALSE;
		}
	}



	function logout() {
		unset($this->user);
		unset($this->authenticated);
		session_destroy();
		return;
	}


	// default constructor. Get current logged in user from session. Check for permissions.
	function login() {
		
		$key = "123456";
		
		if ($this->user AND $this->authenticated) {

			if (!empty ($_SESSION['pommo'.$key])) {
				$_SESSION['pommo'.$key]['username'] = $this->user->_username;
				$_SESSION['pommo'.$key]['usertype'] = $this->user->_usertype;
				$_SESSION['pommo'.$key]['uid'] = $this->user->_id;
			} else {
				Pommo::kill(sprintf(Pommo::_T('MultiUser: No Session.')));
			}
			
		} else {
			Pommo::kill(sprintf(Pommo::_T('Multiuser: Denied access. You must %slogin%s to access this page...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));		
		}
	
	} //login

 */
	
?>

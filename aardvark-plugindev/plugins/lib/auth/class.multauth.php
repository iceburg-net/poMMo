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
// FACTORY PATTERN Authentication Method Chooser


Pommo::requireOnce($this->_baseDir.'plugins/lib/auth/class.user.php');


/* Defines the authentication method use 
 * database auth, LDAP auth ....
 * This is defined in the GENERAL PLUGIN SECTION, where you can choose
 * one or more of these options (all methods for very restrictive and 
 * intensive checks). Although a standard pommo installation probably
 * does not need LDAP authentication.
 */
class MultAuth { 

	private $user;
	
	public function __construct($args = array ()) {
		$this->user = NULL;
	} //Constructor


	// User Constructor constructs a object of type SimpleUser or AdminUser
	// depending if the loginscreen name is the admin alias or an other login name
	// because the authentication process is done later then the $pommo generating
	// i designed it a separate function
	public function constructUser($username, $md5pass) {

		$alias = $this->dbGetAdminAlias();
		
		if ($username == $alias) {
			$this->user = new AdminUser($alias, $md5pass);
		} else {
			$this->user = new SimpleUser($username, $md5pass);
			$this->user->setAuthMethod();
		}
		
	} //constructuser


	// Function needed becaus in index.php before the authentication 
	// process we execute this function
	public function isAuthenticated() {
		if (isset($this->user)) {
			return $this->user->isAuthenticated();
		} else {
			return FALSE;
		}
	}


	//if daten vorhanden sonst FALSE
	public function authenticate() {
		if ($this->user) {
			return $this->user->authenticate();	
		} else {
			//TODO message
			return FALSE;
		}
	}



	public function logout() {
		unset($this->user);
		session_destroy();
		return;
	}


	// default constructor. Get current logged in user from session. Check for permissions.
	public function login() {

		global $pommo;

		if (isset($this->user) AND $this->user->isAuthenticated()) {

			$key = "123456";	//hmmmmm
			
			if (!empty ($_SESSION['pommo'.$key])) {
				$_SESSION['pommo'.$key]['username'] = $this->user->getUsername();
				$_SESSION['pommo'.$key]['sonstiges'] = "blahblahblah";
			} else {
				Pommo::kill(sprintf(Pommo::_T('Denied access. You must %slogin%s to access this page (Session empty*)...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
			}
			$pommo->_session =& $_SESSION['pommo'.$key];

			/*echo "<div style='color: red;'>session:";
			print_r($_SESSION);
			echo "<br><br>pommo->_session: ";
			print_r($_SESSION);
			echo "<br>";*/
			
		} else {
			Pommo::kill(sprintf(Pommo::_T('Denied access. You must %slogin%s to access this page(user not set*)...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
		}
		
		
		/*
		 * 		if (empty ($_SESSION['pommo'.$key])) {
			$_SESSION['pommo'.$key] = array (
				'data' => array (),
				'state' => array (),
				//WAS:'username' => null				//corinna: TODO put away?
				'username' => null, //$this->_username,    //init empty session username
				'sonstiges' => 'blah'
			);
		}
		
		
		$this->_session =& $_SESSION['pommo'.$key];
		
		 */
		
		
		
		/*global $pommo;
		
		$defaults = array (
			'username' => null,
			'requiredLevel' => 0
		);
		//$p = PommoAPI :: getParams($defaults, $args);
		
		if (empty($pommo->_session['username']))
			$pommo->_session['username'] = $this->user->//$p['username'];
		
		$this->_username = & $pommo->_session['username'];
		//$this->_permissionLevel = $this->getPermissionLevel($this->_username);
		$this->_permissionLevel = 5;

		if ($p['requiredLevel'] > $this->_permissionLevel) {
			global $pommo;
			Pommo::kill(sprintf(Pommo::_T('Denied access. You must %slogin%s to access this page...'), '<a href="' . $pommo->_baseUrl . 'index.php?referer=' . $_SERVER['PHP_SELF'] . '">', '</a>'));
		}
		*/
	}

	// return FALSE if there is no user object
	/*


	public function getUsertype() {
		if (isset($this->user)) {
			return $this->user->getUsertype();	
		} else {
			return FALSE;
		}
	}*/
	
	
	

	/* Returns the alias for the Administrator if its different than 'admin'
	 * This name is written in the config table of the pommo main db
	 */
	private function dbGetAdminAlias() {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT config_value FROM " . $dbo->table['config'] . 
			" WHERE config_name = 'admin_username' LIMIT 1 "; 

		$query = $dbo->prepare($query);
		
		if ($row = $dbo->getRows($query))
			$a = $row['config_value'];
		
		return $a;
		
	} //dbGetAdminAlias



} //MultAuth

	
?>

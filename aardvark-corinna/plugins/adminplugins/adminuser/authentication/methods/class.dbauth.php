<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 21.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

include_once(bm_baseDir.'/plugins/adminplugins/adminuser/interfaces/interface.authent.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/methods/class.db_authhandler.php');
include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/class.auth.php');


class DbAuth extends Auth implements iAuthent { 

 	private $authmethod = "dbauth";
 	
 	private $dbconf	= NULL;
 	
 	private $dbhandler = NULL;
 	
 	
 	public function __construct($dbo, $logger) {
 		parent::__construct($dbo, $logger);
 		$this->dbhandler = new AuthHandler($dbo);
 		$this->getConfigFromDb();
 	}
 	public function __destruct() {
 		unset($this->dbconf);
 	}
 	

 	public function printAuthMethod() {
 		return $this->authmethod;
 	}


 	public function getConfigFromDb() {

		$mydbo = $this->getdbo();
		if ($mydbo != NULL) {

		/*	// Read config from database $conf = new ConfigHandler($mydbo);	$config = $conf->dbGetConfigByName($this->authmethod); */
			
			// Read config from database 
			$config = $this->dbhandler->dbGetConfigByName($this->authmethod);

			//print_r($config);
			//TODO CHECK AND MAKE CONFIG
			//$this->dbconf['db_server'] = $config['ldap_server'];

		} else {
			$this->handleError("Configuration could not be loaded from database.");
		}

 	} //getConfigFromDb


	public function authenticate($user, $md5pass) {
	
		// if exists dbhandler
		
		// query in database
		if ($this->dbhandler->dbVerifyUser($user, $md5pass)) {
			$this->handleMessage("User and Pass found in database.");
			return TRUE;
		} else {
			$this->handleMessage("User and Pass not found in database.");
			return FALSE;
		}
		
	} //authenticate
	
} //dbAuth

?>

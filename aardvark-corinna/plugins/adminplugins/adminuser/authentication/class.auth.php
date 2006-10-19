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


include_once(bm_baseDir.'/plugins/pluginregistry/interfaces/interface.module.php');


class Auth implements iModule { 


	private $dbo = NULL;				// database handle
	private $logger = NULL;				// logger handle
				
				
	public function __construct($dbo, $logger) {
		$this->registerdbo($dbo);
		$this->registerlogger($logger);
	}
	
	public function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	public function registerlogger($logger) {
		$this->logger = $logger;
	}
	
	public function getdbo() {
		return $this->dbo;
	}
	public function getlogger() {
		return $this->logger;
	}
	

	//public abstract function getConfigFromDb($dbo);
	//public abstract function authenticate($user, $md5pass);

	



	public function __destruct() {
		unset($this->dbo);
		unset($this->logger);
	}
	
	

	//For non errors like: User not accepted by ldap server
 	public function handleMessage($msg) { //, $logger) {
   		$str = "<span style='color: pink;'>{$msg}</span>";
		$this->logger->addMsg($str);
	}
	//try error catching??? and Error Handling with Exceptions
	public function handleError($msg) {//, $logger) {
   		$str = "<span style='color: pink;'>{$msg}</span>";
   		$this->logger->addErr($str);
	}


	/*public function setVariable($name, $value) {
		$this->vars[$name] = $value;
	}
	public function getVariable() {
		return $this->vars;
	}*/

	
} // class Auth

?>

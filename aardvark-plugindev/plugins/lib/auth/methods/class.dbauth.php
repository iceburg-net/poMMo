<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 17.01.2007
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


// try now one way how to bring $dbo to execute more than 1 queries! waiting for brices response
// clone is certainliy not a good way to do that -> more objects more memory + resources


class DbAuth {
	
	var $name = "dbauth";
	var $dbconfig;


	function DbAuth() {
		$this->dbconfig = array();
		$this->getConfigFromDb();
	}
	function __destruct() {
		unset($this->dbconfig);
	}

	function getName() {
		return $this->name;
	}


	/**
	 * if the user & password is found in the database return TRUE; else FALSE;
	 */
	function verifyUser($user, $md5pass) {

		global $pommo;
		$dbo = clone $pommo->_dbo;

		$a = array();
		//, user_permissionlvl
		$query = "SELECT user_id, user_name FROM " . $dbo->table['user'] . 
					" WHERE user_name='".$user."' AND user_pass='".$md5pass."' AND user_active=1 LIMIT 1"; 

		$query = $dbo->prepare($query);
		
		if ($row = $dbo->getRows($query)) {
			if ($dbo->affected() == 1) {
				$pommo->_logger->addMsg("Dbauth: USER found & verified return TRUE.");
				return TRUE;	
			}
		}
		
		return FALSE;
		
	} //verifyUser
	


	/**
	 * Read config controlled by GENERAL PLUGIN SETUP
	 */
	function getConfigFromDb() {

		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$data = array();
		
		$query = "SELECT d.data_name, d.data_value FROM " . $dbo->table['plugindata'] .
				 " AS d, " . $dbo->table['plugin'] . " AS p " .
				 "WHERE d.plugin_id=p.plugin_id AND p.plugin_uniquename='%s'";
		
		$query = $dbo->prepare($query, 
			array($this->name) );

		while ($row = $dbo->getRows($query)) {
			$this->dbconfig["$row[data_name]"] = $row['data_value'];
		}

		return $data;
		
	} //dbGetConfigFromDb



	
} //DbAuth

?>

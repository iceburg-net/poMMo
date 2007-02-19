<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 24/gen/07
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


/**
 * Get essential data for plugin mode
 */
class PluginHandler {
	
	var $dbo;
	
	
	function PluginHandler() {
		$this->dbo = NULL;
	}
	

	/**
	 * Returns the alias for the Administrator if its different than 'admin'
	 * This name is written in the config table of the pommo main db
	 */
	function dbGetAdminAlias() {
		
		global $pommo;
		//$this->dbo =& $pommo->_dbo; 
		$this->dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT config_value FROM " . $this->dbo->table['config'] . 
			" WHERE config_name = 'admin_username' LIMIT 1 "; 

		$query = $this->dbo->prepare($query);
		
		if ($row = $this->dbo->getRows($query))
			$a = $row['config_value'];
		
		return $a;
		
	} //dbGetAdminAlias


	/**
	 * Checks if a plugin, specified by its uniquename is enabled
	 */
	function dbGetPluginEnabled($pluginname) {
		
		global $pommo;
		//$this->dbo =& $pommo->_dbo; 
		$this->dbo = clone $pommo->_dbo;
		
		$a = array();
		
		$query = "SELECT plugin_active FROM " . $this->dbo->table['plugin'] . 
			" WHERE plugin_uniquename = '". $pluginname ."' LIMIT 1 "; 

		$query = $this->dbo->prepare($query);
		
		if ($row = $this->dbo->getRows($query))
			$a = $row['plugin_active'];
		
		//returns TRUE OR FALSE wether multiuser is active or not
		return $a;
		
	} //dbGetPluginEnabled



	function dbWriteLastLogin($username) {
	
		global $pommo;
		$dbo = clone $pommo->_dbo;
		
		$query = "UPDATE ".$dbo->table['user']." SET user_lastlogin=NOW() WHERE user_name='".$username."' ";
		$query = $dbo->prepare($query);
		$dbo->query($query);
		
	} //dbWriteLastLogin
	
	function dbIncreaseLoginTries($username) {
		
		global $pommo;
		$dbo = clone $pommo->_dbo;

		$query = "SELECT user_logintries FROM ".$dbo->table['user']." WHERE user_name='".$username."' LIMIT 1 ";
		$query = $dbo->prepare($query);
		
		$l = array();
		while ($row = $dbo->getRows($query)) {
			$l = $row['user_logintries'];
		}
		$l = $l + 1;
		$query2 = "UPDATE ".$dbo->table['user']." SET user_logintries=".$l." WHERE user_name='".$username."' ";
		$query2 = $dbo->prepare($query2);
		$dbo->query($query2);
				
	} //dbIncreaseLoginTries
	
	function dbAddLDAPUser($user, $pass) {

		global $pommo;
		$dbo = clone $pommo->_dbo;

		$query = "INSERT INTO ".$dbo->table['user']." (user_name, user_pass, permgroup_id, user_created) " .
				 "VALUES ('".$user."', '".md5($pass)."', NULL, NOW()) ";
		$query = $dbo->prepare($query);
		$dbo->query($query);

	} //addLDAPUser




	/**
	 * Get the active authentication methods from DB
	 * returns a array of activated auth methods
	 */
	function dbGetAuthMethod() {
		
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
	
	
} //PluginHandler

?>

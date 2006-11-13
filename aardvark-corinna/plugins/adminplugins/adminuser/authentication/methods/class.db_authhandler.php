<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 27.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

defined('_IS_VALID') or die('Move along...');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');
require_once (bm_baseDir . '/plugins/adminplugins/adminuser/interfaces/interface.dbhandler.php');


class AuthHandler { // implements iDbHandler {


	private $dbo;
	private $safesql;
	
	public function __construct($dbo) {
		$this->registerdbo($dbo);
		$this->safesql =& new SafeSQL_MySQL;
	}
	public function __destruct() {
		unset($this->dbo);
	}


	public function dbVerifyUser($user, $md5pass) {
		//TODO Das kommt dann weg Das an in db handler!
		$sql = $this->safesql->query("SELECT user_name FROM %s " .
				"WHERE user_name='%s' AND user_pass='%s' LIMIT 1",
			array('pommomod_user', $user, $md5pass) ); 	
		 // note, this will return "false" if no row returned -- though count always returns 0 (mySQL)!
		return $this->dbo->query($sql,0);		//$this->getdbo()->query($sql,0);
	}
	
	public function & dbGetAuthMethod() {
		//TODO id hinzu? UNIQUENAME
		/*$sql = $safesql->query("SELECT data_value FROM pommomod_plugindata " .
				"WHERE data_name = 'authentication_method' LIMIT 1",
				array() );*/
		$sql = $this->safesql->query("SELECT plugin_uniquename FROM %s " .
				"WHERE plugin_super=33 AND plugin_active=1 LIMIT 1", 
				array('pommomod_plugin') );
		$method = $this->dbo->query($sql,0);
		return $method;
		
	}

	public function & dbGetConfigByName($modname) {
		$sql = $this->safesql->query("SELECT d.data_name, d.data_value FROM %s AS d, %s AS p " .
				"WHERE d.plugin_id=p.plugin_id AND p.plugin_uniquename='%s'",
			array('pommomod_plugindata', 'pommomod_plugin', $modname) );
		$data = array();
		while ($row = $this->dbo->getRows($sql)) {
			$data["$row[data_name]"] = $row['data_value'];
		} 
		return $data;
	}
	
	



	
	public function registerdbo($dbo) {
		$this->dbo = $dbo;
	}


} //AuthHandler

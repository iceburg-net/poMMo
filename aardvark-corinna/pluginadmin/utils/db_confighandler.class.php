<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 15.09.2006
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


/**
 * Registers the database and gets the configuration for a plugin
 * For the Plugin classes. Use:
 * $confhandler = new ConfigHandler($dbo)	//$dbo is from previous pommo initialization
 * $confhandler->dbGetConfig("ID")
 */
class ConfigHandler {

	private $dbo;
	
	
	public function __construct($dbo) {
		$this->registerdbo($dbo);
	}
	private function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	
	public function dbIsActive($pluginid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_active FROM %s WHERE plugin_id=%i",
			array('pommomod_plugin', $pluginid) );
		$state = $this->dbo->query($sql,0);
		return $state;
	}
	public function dbIsActiveByName($plugname) {
		//Get is active by name
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_active FROM %s WHERE plugin_name='%s'",
			array('pommomod_plugin', $plugname) );
		$state = $this->dbo->query($sql,0);
		return $state;
	}
	
	/**
	 * Returns a array with the data pairs (configuration) for a plugin 
	 * (given the) plugin id
	 */
	public function & dbGetConfig($pluginid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT * FROM %s WHERE plugin_id=%i",
			array('pommomod_plugindata', $pluginid) );
		$data = array();
		while ($row = $this->dbo->getRows($sql)) {
			$data["$row[data_name]"] = $row['data_value'];
		} 
		return $data;
	}

	/**
	 * Gets the ID for a Pluginname
	 */
	public function & dbGetIdForName($pluginname) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id FROM %s WHERE plugin_name='%s'",
			array('pommomod_plugin', $pluginname) );
		$name = $this->dbo->query($sql,0);
		return ($name) ? $name : 0;
	}
	
	/**
	 * Returns a array with the data pairs (configuration) for a plugin 
	 * (given the) plugin name
	 */
	public function & dbGetConfigByName($plugname) {
//TODO: Make some checks -> Return false if Name is not in DB or something
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT d.data_name, d.data_value FROM %s AS d, %s AS p " .
				"WHERE p.plugin_id=d.plugin_id AND p.plugin_name='%s'",
			array('pommomod_plugindata', 'pommomod_plugin', $plugname) );
		$data = array();
		while ($row = $this->dbo->getRows($sql)) {
			$data["$row[data_name]"] = $row['data_value'];
		} 
		return $data;
	}

} //ConfigHandler


?>

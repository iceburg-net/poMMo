<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 07.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


/** 
 * Don't allow direct access to this file. Must be called from elsewhere
 */
defined('_IS_VALID') or die('Move along...');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');


/**
 * General Database Handler for the "Plugin Admin Tool"
 */
class DatabaseHandler {

	private $dbo;
	
	public function __construct($dbo) {
		$this->registerdbo($dbo);
	}
	
	private function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	
	
	/**
	 * Fetches all the available Plugins from DB with their description
	 */
	public function & dbFetchPlugins() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_name, plugin_version, plugin_desc, " .
				"plugin_active FROM %s",
				array('pommomod_plugin') );
		
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$plugins[$i] = array(
				'plugin_id' 	=> $row['plugin_id'],
				'plugin_name'	=> $row['plugin_name'],
				'plugin_version'=> $row['plugin_version'],
				'plugin_desc'	=> $row['plugin_desc'],
				'plugin_active' => $row['plugin_active']
				);
			$i++;
		}
		return $plugins;
	} //dbFetchPlugin
	
	
	/**
	 * Fetches Information for one Plugin
	 */
	public function & dbFetchPluginInfo($pluginid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_name, plugin_version, plugin_desc, plugin_active " .
				"FROM %s WHERE plugin_id=%i",
			array('pommomod_plugin', $pluginid) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$plugindata = array(
				'plugin_id' 	=> $row['plugin_id'],
				'plugin_name'	=> $row['plugin_name'],
				'plugin_version'=> $row['plugin_version'],
				'plugin_desc'	=> $row['plugin_desc'],
				'plugin_active'	=> $row['plugin_active'],
			);
			$i++;
		}
		return $plugindata;
	}
	
	public function & dbFetchPluginData($pluginid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT data_id, data_name, data_value, data_type, plugin_id " .
				"FROM %s WHERE plugin_id=%i",
			array('pommomod_plugindata', $pluginid) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'data_id' 		=> $row['data_id'],
				'data_name'		=> $row['data_name'],
				'data_value'	=> $row['data_value'],
				'data_type'		=> $row['data_type'],
				'plugin_id'		=> $row['plugin_id'],
				);
			$i++;
		}
		return $data;	// Should be written in 'data' field of the plugins	
	}
	
	/**
	 * Update posted changes in thedatebase
	 * $nevval is a array with all the information
	 */
	 //TODO: maybe change this that only 1 value is changed
	public function dbUpdatePluginData($id, $newval) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("UPDATE %s SET data_value='%s' WHERE data_id=%i",
			array('pommomod_plugindata', $newval, $id ) );
		$count = $this->dbo->query($sql);
		return "Data {$id}:{$newval} changed.<br>";
	}
	
	public function dbActivatePlugin($pluginid, $setto) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("UPDATE %s SET plugin_active=%i WHERE plugin_id=%i",
			array('pommomod_plugin', $setto, $pluginid ) );
		$count = $this->dbo->query($sql);
		$setted = ($setto=='1') ? 'on' : 'off';
		return "Plugin State changed to {$setted}.<br>";
	}
	
	

	
	
	
	
	
	/****************************************************/
	
	public function & dbGetActive() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_name FROM %s WHERE plugin_active=1",
			array('pommomod_plugin') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'plugin_id' => $row['plugin_id'],
				'plugin_name' => $row['plugin_name'],
			);
			$i++;
		}
		return $user;
	/*	$count = $dbo->query($sql,0); // note, this will return "false" if no row returned -- though count always returns 0 (mySQL)!
		return ($count) ? $count : 0;*/
	} //dbGetActive
	
	/* Get the number of mailings in the table mailing_history of the database */
	public function & dbGetPluginCount() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT count(plugin_id) FROM %s ",
			array('pommomod_plugin') );
			//array($dbo->table['mailing_history']) );
		$count = $this->dbo->query($sql,0); // note, this will return "false" if no row returned -- though count always returns 0 (mySQL)!
		return ($count) ? $count : 0;
	} //dbGetPluginCount
	
	public function & dbGetPluginIDs() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id FROM %s ",
			array('pommomod_plugin') );
	
		$i = 0;
		while ($row = $this->dbo->getRows($sql)) {
			$pluginids[$i] = $row['plugin_id'];
		 	$i++;
		}
		return ($pluginids) ? $pluginids : 0;
	} //dbGetPluginIDs
	
	public function & dbUpdatePlugin($dbo, $newdata, $newval, $plugid) {
	
	}
	
	/**
	 * Fetches all data - value pairs for a plugin
	 */
	public function & dbFetchPluginPairs($pluginid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT * FROM %s WHERE plugin_id=%i",
			array('pommomod_plugindata', $pluginid) );
		$data = array();
		while ($row = $this->dbo->getRows($sql)) {
			$data["$row[data_name]"] = $row['data_value'];
		} 
		return $data;	// Should be written in 'data' field of the plugins	
		
	} //dbFetchPluginPairs
	
	// TODO brainstoms:
	// aus /plugin/ DIR lesen
	// irgendwo muss ein file sein mit angaben welche Daten das Plugin braucht..
	// im Plugin file oder so ein required kA
	// Evtl name, version usw fix machen
	// eigenschaften in db schreiben
	// aus db auslesen 
	// ändern
	

} //DatabaseHandler


?>

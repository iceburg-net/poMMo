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
require_once (bm_baseDir . '/inc/safesql/SafeSQL.class.php');
require_once (bm_baseDir . '/plugins/pluginregistry/interfaces/interface.dbhandler.php');


/**
 * General Database Handler for the "Plugin Admin Tool"
 */
class PluginHandler implements iDbHandler {

	private $dbo;
	
	public function __construct($dbo) {
		$this->registerdbo($dbo);
	}
	public function __destruct() {
		$this->dbo = NULL;
	}
	
	public function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	
	public function & dbPluginIsActive($pluginame) {
		
	}
	
	/**
	 * Fetches all the available Plugins from DB
	 */
	public function & dbFetchPluginMatrix() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super " .
				"FROM %s ORDER BY plugin_super ",
				array('pommomod_plugin') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'plugin_id' 		=> $row['plugin_id'],
				'plugin_uniquename'	=> $row['plugin_uniquename'],
				'plugin_name'		=> $row['plugin_name'],
				'plugin_desc'		=> $row['plugin_desc'],
				'plugin_active'		=> $row['plugin_active'],
				'plugin_super'		=> $row['plugin_super'],
				);
			$i++;
		}
		return $data;
	}
	/*public function & dbFetchPluginMatrix2() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super " .
				"FROM %s WHERE plugin_id = (SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super" .
				"FROM %s plugin_super)",
				array('pommomod_plugin') );
		$sql = $safesql->query("SELECT plugin_id FROM pommomod_plugin WHERE plugin_super IN (SELECT plugin_id FROM pommomod_plugin WHERE ) ",
			 array() );
			 //SELECT plugin_uniquename FROM pommomod_plugin WHERE plugin_id=(SELECT plugin_super FROM pommomod_plugin )
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'plugin_id' 		=> $row['plugin_id'],
				//'plugin_uniquename'	=> $row['plugin_uniquename'],
				//'plugin_name'		=> $row['plugin_name'],
				//'plugin_desc'		=> $row['plugin_desc'],
				//'plugin_active'		=> $row['plugin_active'],
				//'plugin_super'		=> $row['plugin_super'],
				);
			$i++;
		}
		return $data;
	}	*/


	//TODO AS A SELECT!!!!!!
	public function & dbFetchPluginMatrix3() {
		$data = NULL;
		$mains = $this->fetchMain();
		$i = 0;
		foreach ($mains as $plug) {
			$data[$i] = $plug;
			$subid = $plug['plugin_id'];
			if ($subid != 0) {
				$sub = $this->fetchSub($subid);
				for ($j = 0; $j < sizeof($sub); $j++) {
					$i++;
					$data[$i] = $sub[$j];
				}
			}
			$i++;
		}
		return $data;
	}
	private function fetchMain() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super " .
					"FROM %s WHERE plugin_super=0", 
					array('pommomod_plugin') );
			$i=0;
			while ($row = $this->dbo->getRows($sql)) {
				$data[$i] = array(
					'plugin_id' 		=> $row['plugin_id'],
					'plugin_uniquename'	=> $row['plugin_uniquename'],
					'plugin_name'		=> $row['plugin_name'],
					'plugin_desc'		=> $row['plugin_desc'],
					'plugin_active'		=> $row['plugin_active'],
					'plugin_super'		=> $row['plugin_super'],
					);
				$i++;
			}
			return $data;
	}
	private function fetchSub($superid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super " .
					"FROM %s WHERE plugin_super=%i", 
					array('pommomod_plugin', $superid) );
			$i=0;
			while ($row = $this->dbo->getRows($sql)) {
				$data[$i] = array(
					'plugin_id' 		=> $row['plugin_id'],
					'plugin_uniquename'	=> $row['plugin_uniquename'],
					'plugin_name'		=> $row['plugin_name'],
					'plugin_desc'		=> $row['plugin_desc'],
					'plugin_active'		=> $row['plugin_active'],
					'plugin_super'		=> $row['plugin_super'],
					);
				$i++;
			}
			return $data;
	}
	
	
	
	

	/**
	 * Fetches Information for one Plugin
	 */
	public function & dbFetchPluginInfo($pluginid) {
		$safesql =& new SafeSQL_MySQL;
			$sql = $safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super " .
					"FROM %s WHERE plugin_id=%i LIMIT 1",
			array('pommomod_plugin', $pluginid) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$plugindata = array(
					'plugin_id' 		=> $row['plugin_id'],
					'plugin_uniquename'	=> $row['plugin_uniquename'],
					'plugin_name'		=> $row['plugin_name'],
					'plugin_desc'		=> $row['plugin_desc'],
					'plugin_active'		=> $row['plugin_active'],
					'plugin_super'		=> $row['plugin_super'],
			);
			$i++;
		}
		return $plugindata;
	}

	/**
	 * Fetch configuration data pommomod_plugindata for one plugin
	 */
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
	
	
	
	
	
	

	
	
	
	/******** think of functions we need ***********/
	

	
	public function & dbFetchPlugins() {		//$pluginid = NULL

		$safesql =& new SafeSQL_MySQL;

		// If there is no id fetch all the plugins
		$sql = $safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_subrelation " .
				"FROM %s ",
				array('pommomod_plugin') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'plugin_id' 		=> $row['plugin_id'],
				'plugin_uniquename'	=> $row['plugin_uniquename'],
				'plugin_name'		=> $row['plugin_name'],
				'plugin_desc'		=> $row['plugin_desc'],
				'plugin_active'		=> $row['plugin_active'],
				'plugin_subrelation'=> $row['plugin_subrelation'],
				);
			$i++;
		}
		return $data;

	} //dbFetchPluginMatrix
	

	
	/**
	 * Update posted changes in the database
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
	/*public function dbSwitchPlugin($pluginid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("UPDATE %s SET plugin_active=%i WHERE plugin_id=%i",
			array('pommomod_plugin', $pluginid ) );
		$count = $this->dbo->query($sql);
		$setted = ($setto=='1') ? 'on' : 'off';
		return "Plugin State changed to {$setted}.<br>";
	}*/
	

	
	
	
	
	
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
	
	public function & dbGetActiveCount() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT count(plugin_id) FROM %s WHERE plugin_active=1",
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
	
	
	
	public function & dbGetDropDown($editid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT plugin_id, plugin_uniquename FROM %s WHERE plugin_super=33",
			array('pommomod_plugin') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'plugin_id' => $row['plugin_id'],
				'plugin_uniquename' => $row['plugin_uniquename'],
			);
			$i++;
		}
		return $data;
	}
	
	
	
	// TODO brainstoms:
	// aus /plugin/ DIR lesen
	// irgendwo muss ein file sein mit angaben welche Daten das Plugin braucht..
	// im Plugin file oder so ein required kA
	// Evtl name, version usw fix machen
	// eigenschaften in db schreiben
	// aus db auslesen 
	// ändern
	

} //PluginHandler


?>


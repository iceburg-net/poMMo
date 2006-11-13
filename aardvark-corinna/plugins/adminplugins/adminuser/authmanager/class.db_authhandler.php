<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 10.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

defined('_IS_VALID') or die('Move along...');


require_once (bm_baseDir.'/plugins/adminplugins/adminuser/interfaces/interface.dbhandler.php');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');


class AuthDBHandler implements iDbHandler {
	
	
	private $dbo;
	private $safesql;


	public function __construct($dbo) {
		$this->dbo = $dbo;
		$this->safesql =& new SafeSQL_MySQL;
	}



	/** Returns if the Plugin itself is active */
	public function & dbPluginIsActive($pluginame) {
		$sql = $this->safesql->query("SELECT plugin_active FROM %s " .
				"WHERE plugin_uniquename='%s' ", 
			array(pommomod_plugin, $pluginame) );
		return $this->dbo->query($sql, 0);	//row 0
	}


	public function dbFetchCurrentMethod() {
		$sql = $this->safesql->query("SELECT plugin_id, plugin_uniquename " .
				"FROM %s WHERE plugin_super=33 AND plugin_active=1 LIMIT 1",
				array('pommomod_plugin') );
		$data = NULL;
		while ($row = $this->dbo->getRows($sql)) {
			$data['plugin_id'] = $row['plugin_id'];
			$data['plugin_uniquename'] = $row['plugin_uniquename'];
		}
		return $data;
	}


	public function dbFetchAuthPlugins() {		//$pluginid = NULL

		// If there is no id fetch all the plugins
		$sql = $this->safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active " .
				"FROM %s WHERE plugin_super=33",
				array('pommomod_plugin') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'id' 			=> $row['plugin_id'],
				'uniquename'	=> $row['plugin_uniquename'],
				'name'			=> $row['plugin_name'],
				'desc'			=> $row['plugin_desc'],
				'active'		=> $row['plugin_active'],
				//'plugin_subrelation'=> $row['plugin_subrelation'],
				);
			$i++;
		}
		return $data;

	} //dbFetchAuthPlugins
	
	

	/*public function dbGetAuthSetup($pluginid) {
		$sql = $this->safesql->query("SELECT * FROM %s WHERE plugin_id=%i",
			array('pommomod_plugindata', $pluginid) );
		$data = array();
		$row = NULL;
		while ($row = $this->dbo->getRows($sql)) {
			$data["$row[data_name]"] = $row['data_value'];
		}
		return $data;	// Should be written in 'data' field of the plugins	
	}*/

	public function & dbGetAuthSetup($pluginid) {
		$sql = $this->safesql->query("SELECT data_id, data_name, data_value, data_type, plugin_id " .
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
		
		$sql = $safesql->query("UPDATE %s SET plugin_active=0 WHERE plugin_id!=%i",
			array('pommomod_plugin', $pluginid ) );
		$countoff = $this->dbo->query($sql);
		
		$setted = ($setto=='1') ? 'on' : 'off';
		return "Plugin State changed to {$setted}. ($count set to ON, $countoff set to OFF)<br>";
	}













	/**
	 * Update posted changes in the database
	 * $nevval is a array with all the information
	 */
	/* //TODO: maybe change this that only 1 value is changed
	public function dbUpdatePluginData($id, $newval) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("UPDATE %s SET data_value='%s' WHERE data_id=%i",
			array('pommomod_plugindata', $newval, $id ) );
		$count = $this->dbo->query($sql);
		return "Data {$id}:{$newval} changed.<br>";
	}*/







/*
	public function & dbFetchPlugins() {		//$pluginid = NULL

		// If there is no id fetch all the plugins
		$sql = $this->safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active " .
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
				//'plugin_subrelation'=> $row['plugin_subrelation'],
				);
			$i++;
		}
		return $data;

	} //dbFetchPluginMatrix

	public function & dbFetchPluginInfo($pluginid) {

		$sql = $this->safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_super " .
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
	*/
	
}



?>

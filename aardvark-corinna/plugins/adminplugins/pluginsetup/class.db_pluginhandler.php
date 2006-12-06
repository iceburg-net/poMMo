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


require_once (bm_baseDir.'/plugins/adminplugins/lib/interfaces/interface.dbhandler.php');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');


class PluginDBHandler implements iDbHandler {

	private $dbo;
	private $safesql;


	public function __construct($dbo) {
		$this->dbo = $dbo;
		$this->safesql =& new SafeSQL_MySQL;
	}

	/** Returns if the Plugin itself is active */
	public function & dbPluginIsActive($pluginname) {
		return TRUE;
	}
	

	/* Get all active Plugins + configuration in a Matrix */
	public function dbGetPluginMatrix() {
		$sql = $this->safesql->query("SELECT plugin_id, plugin_uniquename, plugin_name, plugin_desc, plugin_active, " .
				"c.cat_id, cat_name, cat_active, plugin_version " .
				"FROM %s AS p RIGHT JOIN %s AS c ON p.cat_id=c.cat_id WHERE c.cat_active=1 " .
				"ORDER BY cat_name",
			array( 'pommomod_plugin', 'pommomod_plugincategory' ) );
		$i=0; $plugins = NULL;
		while ($row = $this->dbo->getRows($sql)) {
			$plugins[$i] = array(
				'pid' 		=> $row['plugin_id'],
				'uniquename'=> $row['plugin_uniquename'],
				'name'		=> $row['plugin_name'],
				'desc'		=> $row['plugin_desc'],
				'pactive'	=> $row['plugin_active'],
				'version'	=> $row['plugin_version'],
				'cid'		=> $row['cat_id'],
				'category'	=> $row['cat_name'],
				'cactive'	=> $row['cat_active'],
				);
			$i++;
		}
		return $plugins;
	}

	/* Get categories, that are active Or inactive, or all */
	public function dbGetCategories($active = NULL) {	
		$sql = NULL;
		if ($active == 'inactive') {	//ALL INACTIVE
			$sql = $this->safesql->query("SELECT cat_id, cat_name, cat_desc, cat_active FROM %s WHERE cat_active=0 ",
				array( 'pommomod_plugincategory') );
		} elseif ($active == 'active') {	//ACTIVE ONES
			$sql = $this->safesql->query("SELECT cat_id, cat_name, cat_desc, cat_active FROM %s WHERE cat_active=1 ",
				array( 'pommomod_plugincategory') );
		} else {	//ALL CATEGORIES
			$sql = $this->safesql->query("SELECT cat_id, cat_name, cat_desc, cat_active FROM %s ORDER BY cat_active ",
				array( 'pommomod_plugincategory' ) );
		}
		
		$i=0; $cat = NULL;
		while ($row = $this->dbo->getRows($sql)) {
			$cat[$i] = array(
				'cid' 		=> $row['cat_id'],
				'name'	=> $row['cat_name'],
				'desc'	=> $row['cat_desc'],
				'cactive'	=> $row['cat_active'],
				);
			$i++;
		}
		return $cat;
	}



	/** Get the setup values for one given Plugin ID */
	public function & dbGetPluginSetup($pluginid) {
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
	
	
	
	/************************ PLUGIN USE CASES ************************/
	
	public function dbSwitchPlugin($pluginid, $setto = NULL) {
		
		if (!setto) {
			$sql = $this->safesql->query("UPDATE %s SET NOT(plugin_active) WHERE plugin_id=%i",
				array('pommomod_plugin', $pluginid ) );
			$count = $this->dbo->query($sql);
		} else {
			$sql = $this->safesql->query("UPDATE %s SET plugin_active=%i WHERE plugin_id=%i",
				array('pommomod_plugin', $setto, $pluginid ) );
			$count = $this->dbo->query($sql);
		}
		// TODO
		// Switch all other options from this category. (Mostly we want only 1 configuration active e.g. the authentication method)
		/*$sql = $this->safesql->query("UPDATE %s SET NOT(plugin_active) WHERE plugin_id!=%i",
			array('pommomod_plugin', $pluginid ) );
		$countoff = $this->dbo->query($sql);*/
		
		$setted = ($setto=='1') ? 'on' : 'off';
		return "Plugin State changed to {$setted}. ($count set to ON, $countoff set to OFF)<br>";
	}

	/**
	 * Update posted changes in the database
	 * $nevval is a array with all the information
	 */
	public function dbUpdatePluginData($id, $newval) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $this->safesql->query("UPDATE %s SET data_value='%s' WHERE data_id=%i",
			array('pommomod_plugindata', $newval, $id ) );
		$count = $this->dbo->query($sql);
		return "Data {$id}:{$newval} changed.<br>";
	}


	/**************************** CATEGORY USE CASES ******************************/
	
	
	/* TODO: Name toggle better for functions like thios?? */
	public function dbSwitchCategory($catid, $setto) {

		if ($setto==0) {
			// Set all plugins in this category to 0?
			$sql1 = $this->safesql->query("UPDATE %s SET plugin_active=0 WHERE cat_id=%i",
				array('pommomod_plugin', $catid ) );
			$count2 = $this->dbo->query($sql1);
		}
		$sql = $this->safesql->query("UPDATE %s SET cat_active=%i WHERE cat_id=%i",
				array('pommomod_plugincategory', $setto, $catid ) );
		
		$count = $this->dbo->query($sql);
		
		//$setted = ($setto=='1') ? 'on' : 'off';
		return "Plugin State changed to {$setto}. ($count set to ON, $countoff set to OFF)<br>" .
			   "Plugin State changed to {$setto}. ($count2 set to OFF)";
	}



} //PluginDBHandler

?>

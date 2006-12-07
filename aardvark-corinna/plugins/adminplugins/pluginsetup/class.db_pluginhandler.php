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


require_once (bm_baseDir.'/plugins/adminplugins/lib/interface.dbhandler.php');

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
				'name'		=> $row['cat_name'],
				'desc'		=> $row['cat_desc'],
				'cactive'	=> $row['cat_active'],
				);
			$i++;
		}
		return $cat;
	}



	/** Get the setup values for one given Plugin ID */
	public function & dbGetPluginSetup($pluginid) {
		$sql = $this->safesql->query("SELECT data_id, data_name, data_value, data_type, data_desc, plugin_id " .
				"FROM %s WHERE plugin_id=%i",
			array('pommomod_plugindata', $pluginid) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$data[$i] = array(
				'data_id' 		=> $row['data_id'],
				'data_name'		=> $row['data_name'],
				'data_value'	=> $row['data_value'],
				'data_type'		=> $row['data_type'],
				'data_desc'		=> $row['data_desc'],
				'plugin_id'		=> $row['plugin_id'],
				);
			$i++;
		}
		return $data;	// Should be written in 'data' field of the plugins	
	}	
	
	
	
	/************************ PLUGIN USE CASES ************************/

	/**
	 * Update posted parameter changes in the database
	 * $nevval is a array with all the information
	 * returns the amount of changed "items"
	 */
	public function dbUpdatePluginData($id, $newval) {
		$sql = $this->safesql->query("UPDATE %s SET data_value='%s' WHERE data_id=%i",
			array('pommomod_plugindata', $newval, $id ) );
		return $this->dbo->query($sql);
	}
	

	public function dbSwitchPlugin($pluginid, $setto) {	// = NULL

		$sql = NULL;
		// TODO -> This feature below is not needed. I want to be able to activate the options independently e.g. 
		//			if one wants to activate db auth and ldap auth he hast du activate both this plugins
		// Switch all other options from this category. (Mostly we want only 1 configuration active e.g. the authentication method)
		/*$sql = $this->safesql->query("UPDATE %s SET NOT(plugin_active) WHERE plugin_id!=%i", array('pommomod_plugin', $pluginid ) );
		$countoff = $this->dbo->query($sql);*/
		/*if (!setto) {
			$sql = $this->safesql->query("UPDATE %s SET NOT(plugin_active) WHERE plugin_id=%i",
				array('pommomod_plugin', $pluginid ) );
		} else {*/
			$sql = $this->safesql->query("UPDATE %s SET plugin_active=%i WHERE plugin_id=%i",
				array('pommomod_plugin', $setto, $pluginid ) );
		//}
		return $this->dbo->query($sql);
	}




	/**************************** CATEGORY USE CASES ******************************/
	
	/**
	 * Updates category data, sets the given category as active/inactive 
	 * and returns the amaount of changed data values.
	 */
	public function dbSwitchCategory($catid, $setto) {
		$sql = $this->safesql->query("UPDATE %s SET cat_active=%i WHERE cat_id=%i",
				array('pommomod_plugincategory', $setto, $catid ) );
		//returns count of changed data values!
		return $this->dbo->query($sql);
	}



} //PluginDBHandler

?>

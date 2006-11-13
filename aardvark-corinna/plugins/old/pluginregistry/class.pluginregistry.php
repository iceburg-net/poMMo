<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 20.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require_once(bm_baseDir . '/plugins/pluginregistry/inc/class.db_pluginhandler.php');


class PluginRegistry {
	
	private 	$pluginarray = 0;			// Array witch contains plugin data
	private 	$activearray = 0;			// Active Plugins Matrix
	private 	$dbo = 0;
	private 	$phandler = 0;
	
	public function __construct($dbo) {
		$this->init($dbo);
	}
	public function __destruct() {
		// unregister all registered variables
		//unset
		$this->dbo 		= NULL;
		$this->phandler = NULL;
	}

	//brauhcts net
	public function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	
	public function init($dbo) {
		// TODO 
		// Check if db tables are installed and db is functional
		// if there is data in DB and if there is need to install
		$this->phandler = new PluginHandler($dbo);
		$this->updateActive();
	}
	
	public function isActive() {
		
	}
	
	
	public function addPlugin($newplugin) {
		$this->pluginarray[] = $newplugin;
	}
	public function deletePlugin($pluginname) {
		$this->pluginarray[$pluginname] = NULL;
	}
	public function printList() {
		echo "<span style='color: blue;'>";
		print_r($this->pluginarray);
		echo "</span><br>";
	}
	public function printMsg($msg) {
		echo "<span style='color: green;'>";
		print_r($msg);
		echo "</span><br>";
	}
	public function updateActive() {
		$this->pluginarray = $this->phandler->dbGetActive();
		$this->countactive = $this->phandler->dbGetActiveCount();
		$this->printList();
		$this->printMsg($this->countactive);
	}
	
	
	
	
	
	
	public function getPluginMatrix() {
		$blah = $this->phandler->dbFetchPluginMatrix3();
		return $blah; 
	}
	
	public function getPluginData($pluginid) {
		$plugindata = $this->phandler->dbFetchPluginInfo($pluginid);
		$plugindata['plugin_data'] = $this->phandler->dbFetchPluginData($pluginid);
		return $plugindata;
	}
	
	public function updatePluginData($keys, $vals) {
		return $this->phandler->dbUpdatePluginData($keys, $vals);
	}
	
	public function switchPlugin($pluginid, $setto) {
		//return $this->phandler->dbActivatePlugin($pluginid, $setto);
		return $this->phandler->dbActivatePlugin($pluginid, $setto);
	}
	
	
	public function iterate() {
		for ($i=0; $i< count($this->activearray); $i++) {
			$this->activearray[$i].execute();
		}
	}
	
	
	
	
	public function getDropDown($editid) {
		
		//print_r($this->phandler->dbGetDropDown($editid));
		return $this->phandler->dbGetDropDown($editid);
	}
	
	
	private function debug() {
		echo "<div style='color: red;'>";
		print_r($this->pluginarray); echo "<br>";
		print_r($this->activeids); echo "<br>";
		print_r($this->dbo); echo "<br>";
		print_r($this->phandler); echo "<br></div>";
	}

} //PluginRegistry


/*	function init() {
		//Get activation data from the database
		$activeids = dbGetActive($this->mydbo);
		//All the active plugins and build a array from it
		// Parse this array with execute
		// for alle
		// 		$activeplugins[$i]->execute()
		//		$activeplugins
		for ($i = 0; $i < sizeof($activeids); $i++) {
			echo "Plugin: {$activeids[$i]['plugin_id']}: <b>{$activeids[$i]['plugin_name']}</b> " .
					"is active and loaded.<br>";
		}
		echo "--loading finished--<br><br>";
		
	} //Init
	
	function printlinks() {
		echo "<br><div style='border: 1px dashed silver; background-color:#eeeeee; width: 200px; text-align: center;'>";
		echo "<a href='http://localhost:9999/pommo/aardvark-corinna/plugins/pluginregistry/plugins.php'>Plugins</a><br>";
		echo "<a href='http://localhost:9999/pommo/aardvark-corinna/plugins/pluginregistry/usermanager.php'>User</a><br>";
		echo "</div><br>";
	}
*/
/**
 * beim constructen:
 * von den plugins in plugin/ folder alles laden...
 * mit O zum anklicken ob aktiv oder nicht,
 * bei aktiven generiert factory die Objekte...
 * und wenn ich zB
 * 
 * Factory =  new Factory();
 * neuesauthObj = Factory->selectClass(Auth); 	// Auth ist name des plugins // ort an dem es kommen soll
 * 			// dieser returniert ein OBjwekt !!! das der richtigen Obj entspricht:
 * 			// zB wenn ich Auth LDAP ankreuzle soll es Auth LDAP machen + zurückliefern
 * 			// bei normalen Auth noch ein OPbjekt machen und dann
 * 
 * $authent = new Authentificator();
 * $authent = neuesauthObj.clone();		//neues objekt drüberladen mit spezielleren funktionen
 * 										// zB die implementierte execute methoden
 * $authent->execute();			// Funktion aufrufen, die dann pro Objekt verschieden ist...
 * 
 */ 


?>

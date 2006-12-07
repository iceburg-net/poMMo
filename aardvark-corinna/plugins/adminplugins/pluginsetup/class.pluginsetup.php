<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 15.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require_once (bm_baseDir . '/plugins/adminplugins/pluginsetup/class.db_pluginhandler.php'); 
//require_once (bm_baseDir . '/inc/class.pager.php');


/**
 * This Plugin has no general options to setup
 * You cannot deactivate/disable this plugin...
 * This should only be visible to the trusted :) administrator/s
 */
class PluginSetup {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration from the database through this name.
	private $pluginname = "pluginsetup";
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $plugindbhandler;
	

	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		$this->plugindbhandler = new PluginDBHandler($this->dbo);
	}
	
	public function __destruct() {
	}


	public function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		// return $this->userdbhandler->dbPluginIsActive($this->pluginname);
		// This plugin should always be activated! You can only activate/deactivate it through 
		// the DB, but its a bad idea
		
		// maybe check if $useplugins in config.php is activated/deactivated
		
		return TRUE; 
	}
	public function getPermission($user) {
		//TODO select the permissions from DB 
		// like isActive()
		return TRUE;
	}
	
	
	public function execute($data) {

		$smarty = & bmSmartyInit();
		

		if ($data['changeid']) {
			$this->editSetup($data['old'],$data['new']);
			//$this->switchPlugin($data['changeid'], $data['active']); remove -> switch is controlled solely by the icon button
		}
		if ($data['setupid']) {
			$setup = $this->plugindbhandler->dbGetPluginSetup($data['setupid']);	 
			$smarty->assign('plugsetup' , $setup);
		}
		if ($data['switchid']) {
			$this->switchPlugin($data['switchid'], $data['active']);
		}
		if ($data['switchcid']) {
			$this->switchCategory($data['switchcid'], $data['active']);
		}
		
		// PLugin Matrix
		$plugins = $this->plugindbhandler->dbGetPluginMatrix();
		$smarty->assign('plugins' , $plugins);
		$smarty->assign('nrplugins', count($plugins));

		//Matrix of incative categories
		$smarty->assign('inactive', $this->plugindbhandler->dbGetCategories('inactive'));


		//$smarty->assign('active', $this->plugindbhandler->dbGetCategories('active'));
		//$smarty->assign('categories', $this->plugindbhandler->dbGetCategories());
		
		//$categories = $this->plugindbhandler->dbGetCategories();
		//$smarty->assign('categories', $categories);
		//echo "<div style='color:red;'>"; print_r($categories); echo "</div><br>";
		
		// ALles Categoiries zurückliefern mit GROUP BY plugin_category und dann
		// im template <h1>{category}</h1>
		//	und alle plugins vom typ "category"auflisten
		// nein -> im if???
		// alte categorie, neue categorien??
		/*$smarty->assign('categories2', $plugins['category']);
		echo "<div style='color:red;'>"; print_r($plugins[0]['category']); echo "</div><br>";
		*/
		
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/pluginsetup/plugin_main.tpl');
		bmKill();
	} //execute()
	
	
	
	/*********** use cases **************/
	
	//TODO: make atomic action for data consistency?
	//$changed[0] = $this->authdbhandler->dbActivatePlugin($pluginid, $active);
	public function editSetup($old, $new) {
		
		//TODO -> prevent WARNING
		$keyarray = array_keys($new);
		$valarray = array_values($new);
		
		for ($i=0; $i <= count($new); $i++) {
			//Change only if its altered
			if ($valarray[$i] != $old[$i]) {
				$ret = $this->plugindbhandler->dbUpdatePluginData($keyarray[$i], $valarray[$i]);
				$changed[$i] = "Dataid: {$keyarray[$i]} altered to: {$valarray[$i]}. ({$ret} records altered.)";
			}
		}
		if ($changed == NULL) {
			$this->logger->addMsg(_T('Data altered somehow?'));
		} else {
			$this->logger->addMsg(_T('Config altered:<br> ' . implode("<br>", $changed)));
		}
		
	}
	
	public function switchPlugin($pluginid, $setto) {
		$ret = $this->plugindbhandler->dbSwitchPlugin($pluginid, $setto);
		$setted = ($setto=='1') ? 'on' : 'off';
		$str = "Plugin id {$pluginid}: State changed to {$setted}. ($ret records altered.)";
		$this->logger->addMsg($str);	
	}
	
	public function switchCategory($catid, $setto) {
		$ret = $this->plugindbhandler->dbSwitchCategory($catid, $setto);
		$setted = ($setto=='1') ? 'on' : 'off';
		$str = "Category id " . $catid . ": " . $ret . " records to " . $setted;
		$this->logger->addMsg($str);
	}
	
	

		
} //PluginSetup



?>

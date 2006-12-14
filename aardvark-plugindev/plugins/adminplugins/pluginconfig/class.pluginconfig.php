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


//Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.db_confighandler.php'); 
//Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');
//require_once ($pommo->_baseDir . '/inc/class.pager.php');

/**
 * This Plugin has no general options to setup
 * You cannot deactivate/disable this plugin...
 * This should only be visible to the trusted :) administrator/s
 */
class PluginConfig { //implements iPlugin
	
	// UNIQUE Name of the Plugin
	private $pluginname = "pluginconfig";
	private $pommo;
	private $logger;
	private $configdbhandler;
	

	public function __construct($pommo) {
		$this->pommo = $pommo;
		$this->logger = $pommo->_logger;
		$this->configdbhandler = new ConfigDBHandler($pommo->_dbo);
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

		Pommo::requireOnce($this->pommo->_baseDir.'inc/classes/template.php');
		$smarty = new PommoTemplate();
		
		if ($data['changeid']) {
			$this->editSetup($data['old'],$data['new']);
			//$this->switchPlugin($data['changeid'], $data['active']); remove -> switch is controlled solely by the icon button
		}
		if ($data['setupid']) {
			$setup = $this->configdbhandler->dbGetPluginSetup($data['setupid']);	 
			$smarty->assign('plugsetup' , $setup);
		}
		if ($data['switchid']) {
			$this->switchPlugin($data['switchid'], $data['active']);
		}
		if ($data['switchcid']) {
			$this->switchCategory($data['switchcid'], $data['active']);
		}
		if ($data['allpluginsoff']) {
			$this->configdbhandler->dbSetPlugins('OFF');
			$this->logger->addMsg("All Plugins disabled.");
		}
		
		// Plugin Matrix
		$plugins = $this->configdbhandler->dbGetPluginMatrix();
		$smarty->assign('plugins' , $plugins);
		$smarty->assign('nrplugins', count($plugins));

		//Matrix of incative categories
		$smarty->assign('inactive', $this->configdbhandler->dbGetCategories('inactive'));

		$smarty->assign($_POST);	//no edit data
		$smarty->display('plugins/adminplugins/pluginconfig/config_main.tpl');
		
		Pommo::kill();
		
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
				$ret = $this->configdbhandler->dbUpdatePluginData($keyarray[$i], $valarray[$i]);
				$changed[$i] = "Dataid: {$keyarray[$i]} altered to: {$valarray[$i]}. ({$ret} records altered.)";
			}
		}
		if ($changed == NULL) {
			$this->logger->addMsg('Data altered.');
		} else {
			$this->logger->addMsg('Config altered:<br> ' . implode("<br>", $changed));
		}
		
	}
	
	public function switchPlugin($pluginid, $setto) {
		$ret = $this->configdbhandler->dbSwitchPlugin($pluginid, $setto);
		$setted = ($setto=='1') ? 'on' : 'off';
		$str = "Plugin id {$pluginid}: State changed to {$setted}. ($ret records altered.)";
		$this->logger->addMsg($str);	
	}
	
	public function switchCategory($catid, $setto) {
		$ret = $this->configdbhandler->dbSwitchCategory($catid, $setto);
		$setted = ($setto=='1') ? 'on' : 'off';
		$str = "Category id " . $catid . ": " . $ret . " records to " . $setted;
		$this->logger->addMsg($str);
	}
	

} //PluginSetup


?>

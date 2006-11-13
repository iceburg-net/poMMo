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

require_once (bm_baseDir . '/plugins/adminplugins/adminuser/authmanager/class.db_authhandler.php'); 
require_once (bm_baseDir . '/inc/class.pager.php');


class AuthPlugin {	//implements Plugin

	private $pluginname = "authadmin";
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $currentmethod;
	
	private $authdbhandler;
	
	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		
		$this->authdbhandler = new AuthDBHandler($this->dbo);
	}
	public function __destruct() {
		
	}

	public function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->userdbhandler->dbPluginIsActive($this->pluginname);
	}
	
	public function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}	
	
	public function execute($data) {	

		// Smarty Init
		$smarty = & bmSmartyInit();

		if ($data['changeid']) {
			$this->editSetup($data['old'],$data['new']);
			$this->switchPlugin($data['changeid'], $data['active']);
		}
		if ($data['setupid']) {
			$setup = $this->authdbhandler->dbGetAuthSetup($data['setupid']);	 
			$smarty->assign('authsetup' , $setup);
		} 



		$plugins = $this->authdbhandler->dbFetchAuthPlugins(); 
		$this->currentmethod = $this->authdbhandler->dbFetchCurrentMethod();
		
			
		$smarty->assign('authmethods' , $plugins);
		$smarty->assign('currentmethod' , $this->currentmethod['plugin_uniquename']);
		$smarty->assign('currentid' , $this->currentmethod['plugin_id']);




		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/adminuser/authmanager/auth_main.tpl');
		bmKill();

	}
	
	
	
	public function editSetup($old, $new) {
		
		//$changed[0] = $this->authdbhandler->dbActivatePlugin($pluginid, $active);
		
		$keyarray = array_keys($new);
		$valarray = array_values($new);
		
		for ($i=1; $i <= count($new); $i++) {
			//Change only if its altered
			if ($valarray[$i] != $old[$i]) {
				$changed[$i] = $this->authdbhandler->dbUpdatePluginData($keyarray[$i], $valarray[$i]);
			}
		}
		$this->logger->addMsg(_T('Config altered: ' . implode("<br>", $changed)));
	}
	
	public function switchPlugin($pluginid, $setto) {
		$ret = $this->authdbhandler->dbActivatePlugin($pluginid, $setto);
		$this->logger->addMsg($ret);
	}
	
	

} //AuthPlugin


?>

<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 13.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

//require_once (bm_baseDir . '/plugins/adminplugins/adminuser/usermanager/class.db_userhandler.php'); 
//require_once (bm_baseDir . '/inc/class.pager.php');

class BouncePlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "bouncemanager";	
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $userdbhandler;
	

	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		
		//$this->userdbhandler = new UserDBHandler($this->dbo);
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
		$smarty = & bmSmartyInit();
		
		
		
		
		//$smarty->assign('user' , $user);
		
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/adminbounce/bounce_main.tpl');
		bmKill();
	}
	
	
	
}



?>

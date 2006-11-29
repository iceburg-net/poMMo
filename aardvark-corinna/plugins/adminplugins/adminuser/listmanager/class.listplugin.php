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

require_once (bm_baseDir . '/plugins/adminplugins/adminuser/listmanager/class.db_listhandler.php'); 
//require_once (bm_baseDir . '/plugins/adminplugins/adminuser/usermanager/class.listplugin.php');
//require_once (bm_baseDir . '/inc/class.pager.php');



class ListPlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "listmanager";	
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $userdbhandler;
	

	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		
		$this->listdbhandler = new listDBHandler($this->dbo);
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
		
		
		echo "<div style='color:blue;'>"; print_r($data); echo "</div>";
		
		if ($data['showAdd']) {
			$smarty->assign('showAdd' , 'TRUE');
			$mailgroups = $this->listdbhandler->dbGetMailGroups();
			$smarty->assign('mailgroups', $mailgroups);
		} elseif ($data['showEdit']) {
			$listdata = $this->listdbhandler->dbGetListInfo($data['listid'], $data['userid']);
			$mailgroups = $this->listdbhandler->dbGetMailGroups();
			$smarty->assign('listdata', $listdata);
			$smarty->assign('mailgroups', $mailgroups);
			$smarty->assign('showEdit', 'TRUE');
		} elseif ($data['showDelete']) {
			$listdata = $this->listdbhandler->dbGetListInfo($data['listid'], $data['userid']);
			$smarty->assign('listdata', $listdata);
			$smarty->assign('showDelete', 'TRUE');
		}
		
		if ($data['action']) {
			$smarty->assign('action', $data['action']);
			$smarty->assign('showformid', $data['userid']);	//needed for forms
		}
		
		
		//$user = $this->listdbhandler->dbFetchUser();
		$user = $this->listdbhandler->dbFetchUserLists();
		$smarty->assign('userlist' , $user);
		$smarty->assign('nrusers', count($user) );
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/adminuser/listmanager/list_main.tpl');
		bmKill();
	}
	
	
	//TODO some checks
	public function addList($name, $desc, $userid) {
		return $this->listdbhandler->dbAddList($name, $desc, $userid);
	}
	public function editList($listid, $name, $desc) {
		return $this->listdbhandler->dbEditList($listid, $name, $desc);
	}
	public function deleteList($id, $userid) {
		return $this->listdbhandler->dbDeleteList($id, $userid);
	}
	
	
}



?>


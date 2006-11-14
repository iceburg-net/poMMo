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
		} elseif ($data['showEdit']) {
			$smarty->assign('showEdit', 'TRUE');
			$listdata = $this->listdbhandler->dbGetListInfo($data['id'], $data['userid']);
			echo "LISTDATA:"; print_r($listdata); echo "<br><br>";
		} elseif ($data['showDelete']) {
			$smarty->assign('showDelete', 'TRUE');
		}
		
		if ($data['action']) {
			$smarty->assign('action', $data['action']);
			$smarty->assign('id', $data['id']);
		}
		
		
		//$user = $this->listdbhandler->dbFetchUser();
		$user = $this->listdbhandler->dbFetchUserLists();
		$smarty->assign('userlist' , $user);
		$smarty->assign('nrusers', count($user) );
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/adminuser/listmanager/list_main.tpl');
		bmKill();
	}
	
	
	public function addList($name, $desc, $userid) {
		echo "ADD!!!!!!!!!";
		return $this->listdbhandler->dbAddList($name, $desc, $userid);
	}
	public function editList($id, $name, $desc) {
		echo "EDIT!!!!!!!!!!!!";
		return $this->listdbhandler->dbEditList($id, $name, $desc);
	}
	public function deleteList($id, $userid) {
		echo "DELETE!!!!!!!!!!";
		return $this->listdbhandler->dbDeleteList($id, $userid);
	}
	
	
}



?>


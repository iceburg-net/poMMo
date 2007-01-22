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


class ListPlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "listmanager";	
	
	private $logger;
	private $pommo;
	
	private $listdbhandler;
	

	public function __construct($pommo) {
		$this->pommo = $pommo;
		$this->logger = $pommo->_logger;
		
		$this->listdbhandler = new ListDBHandler($pommo->_dbo);
	}
	public function __destruct() {
	}

	public function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->listdbhandler->dbPluginIsActive($this->pluginname);
	}
	
	public function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}
	
	
	public function execute($data) {	

		Pommo::requireOnce($this->pommo->_baseDir.'inc/classes/template.php');
		$smarty = new PommoTemplate();
		
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
			echo "<br><br>LISTDATA:<br>im"; print_r($listdata);
			$smarty->assign('listdata', $listdata);
			$smarty->assign('showDelete', 'TRUE');
		}
		
		/*
		if ($data['action']) {
			$smarty->assign('action', $data['action']);
			$smarty->assign('showformid', $data['userid']);	//needed for forms
		}*/
		
		
		$list = $this->listdbhandler->dbFetchLists();
		$smarty->assign('list' , $list);
		$smarty->assign('nrlists', count($list) );
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/useradmin/listmanager/list_main.tpl');
		Pommo::kill();
		
	}
	
	
	//TODO some checks
	public function addList($name, $desc, $email, $user, $group) {
		return $this->listdbhandler->dbAddList($name, $desc, $email, $user, $group);
	}
	public function editList($listid, $name, $desc) {
		return $this->listdbhandler->dbEditList($listid, $name, $desc);
	}
	public function deleteList($id, $userid) {
		return $this->listdbhandler->dbDeleteList($id, $userid);
	}
	
	
}



?>



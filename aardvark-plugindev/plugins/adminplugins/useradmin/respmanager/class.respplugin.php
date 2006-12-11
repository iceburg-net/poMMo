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



require_once ($pommo->_baseDir.'plugins/adminplugins/useradmin/respmanager/class.db_resphandler.php'); 
//require_once ($pommo->_baseDir.'/inc/class.pager.php');



class RespPlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "respmanager";	
	
	private $dbo;
	private $logger;
	private $pommo;
	
	private $respdbhandler;
	

	public function __construct($pommo) {
		$this->dbo = $pommo->_dbo;
		$this->logger = $pommo->_logger;
		$this->pommo = $pommo;
		
		$this->respdbhandler = new RespDBHandler($this->dbo);
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

		Pommo::requireOnce($this->pommo->_baseDir.'inc/classes/template.php');
		$smarty = new PommoTemplate();
		
		
		echo "<div style='color:blue;'>"; print_r($data); echo "</div>";
		
		if ($data['showAdd']) {
			$smarty->assign('showAdd' , TRUE);
			$smarty->assign('user', $this->respdbhandler->dbFetchUser());
			$smarty->assign('groups', $this->respdbhandler->dbGetGroups());
			
		} elseif ($data['showEdit']) {
			$editdata = $this->respdbhandler->dbFetchUserData($data['editid']);
			$smarty->assign('groups', $this->respdbhandler->dbGetGroups());
			$smarty->assign('user', $this->respdbhandler->dbFetchUser());
			
			$smarty->assign('edit', $editdata);
			$smarty->assign('showEdit', 'TRUE');
		} elseif ($data['showDel']) {
			$deldata = $this->respdbhandler->dbFetchUserData($data['delid']);
			$smarty->assign('del', $deldata);
			$smarty->assign('showDel', 'TRUE');
		}
		
		/*
		if ($data['action']) {
			$smarty->assign('action', $data['action']);
			$smarty->assign('showformid', $data['userid']);	//needed for forms
		}*/
		
		
		$resp = $this->respdbhandler->dbFetchRespMatrix();
		$smarty->assign('resp', $resp);
		$smarty->assign('nrresp', count($resp));
		

		
		
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/useradmin/respmanager/resp_main.tpl');
		Pommo::kill();
		
	}
	
	
	
	
	public function addResponsiblePerson($uid, $realname, $surname, $bounce) {
		return $this->respdbhandler->dbAddResponsiblePerson($uid, $realname, $surname, $bounce);
		
	}
	public function deleteResponsiblePerson($uid) {
		return $this->respdbhandler->dbDeleteResponsiblePerson($uid);
		
	}
	public function editResponsiblePerson($uid, $realname, $surname, $bounce) {
		return $this->respdbhandler->dbAddResponsiblePerson($uid, $realname, $surname, $bounce);
		
	}
	/*
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
	*/
	
} //RespPlugin



?>


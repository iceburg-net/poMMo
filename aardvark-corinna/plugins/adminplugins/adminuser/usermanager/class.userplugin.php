<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 16.10.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require_once (bm_baseDir . '/plugins/adminplugins/adminuser/usermanager/class.db_userhandler.php'); 
require_once (bm_baseDir . '/inc/class.pager.php');


class UserPlugin { //implements plugin

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "useradmin";	
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $userdbhandler;
	

	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		
		$this->userdbhandler = new UserDBHandler($this->dbo);
	}
	public function __destruct() {
		//UNSET
		//$data['action'] = "etwasanderes";
		//$data['mailid'] = "andereid";	
		//echo "destructed";
	}

	public function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->userdbhandler->dbPluginIsActive($this->pluginname);
	}
	
	public function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}
	
	
	// This should be named showUserMatrix()
	// But i think execute as main function for this plugin to show all the users is ok.
	public function execute($data) {	
		
		// Smarty Init
		$smarty = & bmSmartyInit();

		if ($data['showAddForm']) { // == 'addForm') {
			$smarty->assign('showAddForm', TRUE);
			$usergroups = $this->userdbhandler->dbFetchGroupNames();
			$smarty->assign('usergroups', $usergroups);
			$smarty->assign('actionStr', _T('Add new User'));
			
		} elseif ($data['showDelForm']) { // == 'delForm') {
			$smarty->assign('showDeleteForm', TRUE);
			$smarty->assign('actionStr', _T('Delete User'));
			
			//Show deletion info
			$userinfo = $this->userdbhandler->dbFetchUserInfo($data['userid']);
			$smarty->assign('userinfo', $userinfo);
			
		} elseif ($data['showEditForm']) { // == 'editForm') {
			$smarty->assign('showEditForm', TRUE);
			$smarty->assign('actionStr', _T('Edit User'));
			
			//Show data to edit
			$smarty->assign('userinfo', $this->userdbhandler->dbFetchUserInfo($data['userid']));
			$smarty->assign('usergroups',  $this->userdbhandler->dbFetchGroupNames());

		} elseif ($data['showGroupAddForm']) {
			$smarty->assign('showGroupAddForm', TRUE);
		} elseif ($data['showGroupDelForm']) {
			$smarty->assign('showGroupDelForm', TRUE);
			$smarty->assign('groupinfo', $this->userdbhandler->dbFetchGroupInfo($data['groupid']));
		} elseif ($data['showGroupEditForm']) {
			$smarty->assign('showGroupEditForm', TRUE);
			$smarty->assign('groupinfo', $this->userdbhandler->dbFetchGroupInfo($data['groupid']));
		}

		$smarty->assign('showGroups', TRUE);
		$smarty->assign('permgroups', $this->userdbhandler->dbGetGroups());


		/* We need a sorting mechanism here too
		//if (empty($this->poMMo->_state)) {
			// State initialization for sorting options
			$pmState = array(
				'limit' => '10',
				'sortOrder' => 'DESC',
				'sortBy' => 'date'
			);
			$this->poMMo->stateInit('mailings_queue',$pmState);
		//}
		$limit = $this->poMMo->stateVar('limit',$data['mailings_queue']['limit']);
		$sortOrder = $this->poMMo->stateVar('sortOrder',$data['mailings_queue']['sortOrder']);
		$sortBy = $this->poMMo->stateVar('sortBy',$data['mailings_queue']['sortBy']);

		$smarty->assign('state',$this->poMMo->_state);
		*/
	/*	$action = $this->poMMo->stateVar('action',$data['action']);
		$userid = $this->poMMo->stateVar('userid',$data['userid']);
		$smarty->assign('action',$action);
		$smarty->assign('userid',$userid);
		*/
		/* Pager for later
		// Pager part $_GET['page']
		$p = new Pager();
		if ($p->findStart($limit) > $mailcount) $data['page'] = '1';
		$pages = $p->findPages($mailcount, $limit);
		$start = $p->findStart($limit); 
		$pagelist = $p->pageList($data['page'], $pages);
		 */
		
		

		// Display USER TABLE -> Get all the available Users from the database $this->showUserMatrix();
		$user = $this->userdbhandler->dbFetchUser();

		$smarty->assign('nrusers' , count($user)); 
		$smarty->assign('user' , $user); 							//$smarty->assign('mailings', $this->getMailingQueue($start, $limit, $sortBy, $sortOrder));
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/adminuser/usermanager/user_main.tpl');
		bmKill();
		
	} //execute
	


	/* USE CASES */
	
	public function addUser($user, $pass, $passcheck, $group) {

		//TODO mache string aus permission -> soll array sein / SMARTY VALIDATOR
		if (empty($user) OR empty($pass) OR empty($passcheck) OR empty($group)) {
			// No parameter should be empty
			$str = "({$user}, {$group})";
			$this->logger->addMsg(_T('Add User: Parameter is empty. ' . $str));	
		} else {
			
			//write to the database after password check (if its the same)
			if ($pass && $passcheck) {
				$ret = $this->userdbhandler->dbAddUser($user, $pass, $group);
				if (!is_numeric($ret)) {
					$this->logger->addMsg("Add User: User could not be added: ".$ret);
					return FALSE;
				} else {
					if ($ret == 1) {
						$this->logger->addMsg(_T('Add User: User added.'));
						return TRUE;
					} else {
						$this->logger->addMsg(_T('Add User: Problem during adding user.'));	
						return FALSE;
					}
				}
				
			} else {
				$this->logger->addMsg(_T('Add User: Password check failed.'));
				return FALSE;
			}
		}
	} //AddUser
	
	
	
	public function deleteUser($userid) {
		if (!empty($userid)) {
			return $this->userdbhandler->dbDeleteUser($userid);
		} else {
			$this->logger->addMsg(_T('Could not delete: No user id given.'));
			return FALSE;
		}

	}
	
	public function editUser($id, $user, $pass, $group) {
		//if eines leer -> fehler
		
		// Nur das ändern das sich geändert hat? oder alle auf einmal
		$ret = $this->userdbhandler->dbEditUser($id, $user, $pass, $group);
		if ($ret == 1) {
			//Transaktion ok, 1 data altered
			return TRUE;
		} else {
			//Fehlermeldung über logger
			return FALSE;
		}
		
		
	}
	
	
	public function addGroup($name, $perm, $desc) {
		//Checks
		$ret = $this->userdbhandler->dbAddGroup($name, $perm, $desc);
		if ($ret == 1) {
			//Transaktion ok, 1 data altered
			return TRUE;
		} else {
			//Fehlermeldung über logger
			return FALSE;
		}
	}
	//TODO Fehlerbehandlung
	public function deleteGroup($groupid) {
		return $this->userdbhandler->dbDeleteGroup($groupid);
	}
	public function editGroup($groupid, $name, $perm, $desc) {
		return $this->userdbhandler->dbEditGroup($groupid, $name, $perm, $desc);
	}
	
	

} //UserPlugin

?>

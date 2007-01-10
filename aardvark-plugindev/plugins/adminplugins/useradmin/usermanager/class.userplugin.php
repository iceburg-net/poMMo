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

require_once ($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.db_userhandler.php'); 
//require_once ($pommo->_baseDir.'inc/lib/class.pager.php');


class UserPlugin { //implements plugin

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "useradmin";	
	
	private $dbo;
	private $logger;
	private $pommo;
	
	private $userdbhandler;
	

	public function __construct($pommo) {
		$this->dbo = $pommo->_dbo;
		$this->logger = $pommo->_logger;
		$this->pommo = $pommo;
		
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
		
		// TODO test this
		if (!$this->isActive()) {
			//print_r("<b style='color:red;'>NOT ACTIVE!!! Try to enable useradmin plugin in ´the General Plugin setup</b>");
			Pommo::kill("PLUGIN NOT ACTIVE. Try to enable the 'useradmin' plugin in the General Plugin Setup." .
					" <a href='../../pluginconfig/config_main.php'>&raquo; go there</a> &nbsp;&nbsp;");
			//zurückleiten zu seite vorher??? permissions einfügen und $logger
			return;
		}
		
		
		// Smarty Init
		Pommo::requireOnce($this->pommo->_baseDir.'inc/classes/template.php');
		$smarty = new PommoTemplate();

		if ($data['showAddForm']) { // == 'addForm') {
			$smarty->assign('showAddForm', TRUE);
			$smarty->assign('usergroups', $this->userdbhandler->dbFetchPermNames());
			$smarty->assign('actionStr', 'Add new User');
			
		} elseif ($data['showDelForm']) { // == 'delForm') {
			$smarty->assign('showDeleteForm', TRUE);
			$smarty->assign('actionStr', 'Delete User');
			
			//Show deletion info
			$smarty->assign('userinfo', $this->userdbhandler->dbFetchUserInfo($data['userid']));
			
		} elseif ($data['showEditForm']) { // == 'editForm') {
			$smarty->assign('showEditForm', TRUE);
			$smarty->assign('actionStr', 'Edit User');
			
			echo "<div style='color: red'>";
			print_r($this->userdbhandler->dbFetchUserInfo($data['userid']));
			echo "</div>";
			
			//Show data to edit
			$smarty->assign('userinfo', $this->userdbhandler->dbFetchUserInfo($data['userid']));
			$smarty->assign('permgroups',  $this->userdbhandler->dbFetchPermNames());

		} elseif ($data['showGroupAddForm']) {
			$smarty->assign('showGroupAddForm', TRUE);
		} elseif ($data['showGroupDelForm']) {
			$smarty->assign('showGroupDelForm', TRUE);
			$smarty->assign('groupinfo', $this->userdbhandler->dbFetchPermInfo($data['groupid']));
		} elseif ($data['showGroupEditForm']) {
			$smarty->assign('showGroupEditForm', TRUE);
			$smarty->assign('groupinfo', $this->userdbhandler->dbFetchPermInfo($data['groupid']));
		}


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
		
		
		// Permission Groups Matrix
		$perm =  $this->userdbhandler->dbFetchPermissionMatrix();
		$smarty->assign('permgroups', $perm);
		$smarty->assign('nrperm' , count($perm)); 

		// User Matrix
		$user = $this->userdbhandler->dbFetchUserMatrix();		//$smarty->assign('mailings', $this->getMailingQueue($start, $limit, $sortBy, $sortOrder));
		$smarty->assign('user' , $user); 
		$smarty->assign('nrusers' , count($user)); 
		
									
		$smarty->assign($_POST);

		$smarty->display('plugins/adminplugins/useradmin/usermanager/user_main.tpl');
		Pommo::kill();
		
	} //execute
	


	/* USE CASES user */
	
	public function addUser($user, $pass, $passcheck, $group) {

		//TODO mache string aus permission -> soll array sein / SMARTY VALIDATOR
		if (empty($user) OR empty($pass) OR empty($passcheck) OR empty($group)) {
			// No parameter should be empty
			$str = "({$user}, {$group})";
			$this->logger->addMsg('Add User: Parameter is empty. ' . $str);	
		} else {
			
			//write to the database after password check (if its the same)
			if ($pass && $passcheck) {
				$ret = $this->userdbhandler->dbAddUser($user, $pass, $group);
				if (!is_numeric($ret)) {
					$this->logger->addMsg("Add User: User could not be added: ".$ret);
					return FALSE;
				} else {
					if ($ret == 1) {
						$this->logger->addMsg('Add User: User added.');
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
		
		// Es darf nicht nogroup ausgewählt sein
		if ($group=='nogroup') {
			$this->logger->addMsg("No Permissiongroup selected.");
			return FALSE;
		}
		
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
	

	/* USE CASES permission group */
	//TODO Fehlerbehandlung
	
	public function addPermGroup($name, $perm, $desc) {
		//Checks
		$ret = $this->userdbhandler->dbAddPermGroup($name, $perm, $desc);
		if ($ret == 1) {
			//Transaktion ok, 1 data altered
			return TRUE;
		} else {
			//Fehlermeldung über logger
			return FALSE;
		}
	}
	
	public function deletePermGroup($groupid) {
		return $this->userdbhandler->dbDeletePermGroup($groupid);
	}
	
	public function editPermGroup($groupid, $name, $perm, $desc) {
		return $this->userdbhandler->dbEditPermGroup($groupid, $name, $perm, $desc);
	}
	
	

} //UserPlugin

?>

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

require_once (bm_baseDir . '/plugins/adminplugins/adminuser/usermanagement/class.db_userhandler.php'); 
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
	
	
	public function execute($data) {	

		echo "<h3 style='color:blue'>Data: ";
		print_r($data);
		echo "</h3>";

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
		*/

		// Smarty Init
		$smarty = & bmSmartyInit();
		

		/*
		$smarty->assign('state',$this->poMMo->_state);
		$mailid = $this->poMMo->stateVar('mailid',$data['mailid']);
		$smarty->assign('mailid',$mailid);
		*/
		$action = $this->poMMo->stateVar('action',$data['action']);
		$smarty->assign('action',$action);
		
		echo "<h3 style='color:blue'>STATE in $ poMMo: ";
		print_r($this->poMMo->_state);
		echo "</h3>";
		
		switch ($action) {

			case 'adduser':
			
				if ($this->addUser($_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['userpasscheck'], $_REQUEST['usergroup'], $_REQUEST['userperm'])) {
					$this->logger->addMsg(_T('User added.'));
					$data['action'] = 'no';
				} else {
					$this->logger->addMsg(_T('Data fehlt.'));
				}
				$smarty->assign('actionStr', _T('Add new User'));
				//$smarty->assign('returnStr', _T('Mailings Queue'));	//return??
				break;
				
			case 'edit':
				$ret = $this->editUser($userid);
				//print_r($ret);
				$smarty->assign('user', $ret);	//put here a BOOL return value?
				$smarty->assign('actionStr', _T('Edit User'));
				//$smarty->assign('returnStr', _T('Mailings Queue'));	
				break;	
			case 'delete':		
				// Ask for confirmation and then delete
				$ret = $this->deleteUser($userid);
				//print_r($ret);
				$smarty->assign('mailing', $ret);	//put here a BOOL return value?
				$smarty->assign('actionStr', _T('Mailing Delete'));
				//$smarty->assign('returnStr', _T('Mailings Queue'));	
				break;

			default:	
				echo "<h2>switch default: No Action</h2>";
			break;

		} //switch

				// Display USER TABLE -> todo: show table always and changed thins above of it.

				/* Pager for later
				// Pager part $_GET['page']
				$p = new Pager();
				if ($p->findStart($limit) > $mailcount) $data['page'] = '1';
				$pages = $p->findPages($mailcount, $limit);
				$start = $p->findStart($limit); 
				$pagelist = $p->pageList($data['page'], $pages);
				 */
			
				// Get all the available Users from the database
				//$this->showUserMatrix();
				$user = $this->userdbhandler->dbFetchUser($this->dbo);

				$smarty->assign('actionStr', _T('User Table'));
				$smarty->assign('returnStr' , 'poMMo User Manager');	//_T
				$smarty->assign('user' , $user); 	//$smarty->assign('mailings', $this->getMailingQueue($start, $limit, $sortBy, $sortOrder));
				$smarty->assign($_POST);




		//unset($action);
		$this->poMMo->stateVar('action','NULL');

		$smarty->display('plugins/adminplugins/adminuser/usermanagement/useradmin.tpl');
		bmKill();
		
	} //execute
	


	/* USE CASES */
	
	public function addUser($user, $pass, $passcheck, $group, $perm) {
		//TODO mache string aus permission -> soll array sein :/
		if (empty($user)) {
		// if not empty alle 4 parameter
			//$mailing = $this->queuedbhandler->dbGetMailingData($mailid);
			//show add FORM
			//return $mailing;
			return FALSE;
		} else {
			//write to the database
			if ($pass && $passcheck) {
				$ret = $this->userdbhandler->dbAddUser($user, $pass, $group, $perm);
				return TRUE;
			} else {
				$this->logger->addMsg(_T('Add User: Password check failed.'));
				return FALSE;
			}
		}
	}
	public function deleteUser($userid) {
		if (empty($_REQUEST['DeleteUser'])) {
			$this->userdbhandler->dbDeleteUser($userid);
			return TRUE;
		} else {

		}
	}
	public function editUser() {
		
	}
	
	

} //UserPlugin

?>

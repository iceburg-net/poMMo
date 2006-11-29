<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 02.10.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require_once (bm_baseDir . '/plugins/old/mailingqueue/class.db_queuehandler.php'); 
require_once (bm_baseDir . '/inc/class.pager.php');


class QueuePlugin {
	
	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "mailingqueue";	
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $queuedbhandler;


	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		
		$this->queuedbhandler = new QueueDBHandler($this->dbo);
	}
	public function __destruct() {
		//UNSET
		$data['action'] = "etwasanderes";
		$data['mailid'] = "andereid";	
		echo "destructed";
	}
		
	
	public function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		return $this->queuedbhandler->dbPluginIsActive($this->pluginname);
	}
	
	public function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}
	
	

	public function execute($data) {

		/*echo "<h3 style='color:blue'>Data: ";
		print_r($data);
		echo "</h3>";*/

		echo "Dies wird noch angepasst<br>";

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



		/*echo "<h3 style='color:blue'>STATE in $ poMMo: ";
		print_r($this->poMMo->_state);
		echo "</h3>";*/


		// Smarty Init
		$smarty = & bmSmartyInit();
		$smarty->assign('state',$this->poMMo->_state);
		$action = $this->poMMo->stateVar('action',$data['action']);
		$mailid = $this->poMMo->stateVar('mailid',$data['mailid']);

		$smarty->assign('mailid',$mailid);
		$smarty->assign('action',$action);


		switch ($action) {

			case 'view': 		
				// View one mail and the details
				$smarty->assign('mailing', $this->viewMailing($mailid));
				$smarty->assign('actionStr', _T('Mailing View'));
				$smarty->assign('returnStr', _T('Mailings Queue'));
				break;

			case 'delete':		
				// Ask for confirmation and then delete
				$ret = $this->deleteMailing($mailid);
				//print_r($ret);
				$smarty->assign('mailing', $ret);	//put here a BOOL return value?
				$smarty->assign('actionStr', _T('Mailing Delete'));
				$smarty->assign('returnStr', _T('Mailings Queue'));	
				break;

			case 'send':   		
				// load mail to send from database and redirect
				$this->sendMailing($mailid);	
				break;
		
			default:	//$this->showMailingQueue();
				$mailcount = $this->getMailingCount();

				// Pager part $_GET['page']
				$p = new Pager();
				if ($p->findStart($limit) > $mailcount) $data['page'] = '1';
				$pages = $p->findPages($mailcount, $limit);
				$start = $p->findStart($limit); 
				$pagelist = $p->pageList($data['page'], $pages);
				
				$smarty->assign('actionStr', _T('Mailing Queue'));
				$smarty->assign('returnStr', _T('Mailings Page'));	
				$smarty->assign('pagelist', $pagelist);
				$smarty->assign('rowsinset', $mailcount);
				$smarty->assign('mailings', $this->getMailingQueue($start, $limit, $sortBy, $sortOrder));

			break;

		} //switch

		//unset($action);
		$this->poMMo->stateVar('action','NULL');

		$smarty->display('plugins/mailingqueue/queue_main.tpl');
		bmKill();

	} //execute


	/* = 'Forward functionality' -> maybe change this and call the DB functions directly */
	public function getMailingCount() {
		return $this->queuedbhandler->dbGetMailingCount();
	}
	public function getMailingQueue($start, $limit, $sortBy, $sortOrder) {
		return $this->queuedbhandler->dbGetMailingQueue($start, $limit, $sortBy, $sortOrder);
	}

	
	// PROCESSES
	
	public function showMailingQueue() {
		// Show Mailing Queue Matriux we goit from DV with getMailingQueue.
		/*public function getMailingQueue($start, $limit, $sortBy, $sortOrder) {
		return $this->queuedbhandler->dbGetMailingQueue($start, $limit, $sortBy, $sortOrder);
		}*/
	}
	
	
	/** Ask confirmation and delete from database */
	// implement Errorcodes or something FEHLERCODES return TRUE; or mailing
	public function deleteMailing($mailid) {
		
		if (empty($_REQUEST['deleteMailings'])) {
			// Get mailing detail for confirmation
			$mailing = $this->queuedbhandler->dbGetMailingData($mailid);
			return $mailing;
		} else {
			//delete action
			$ret = $this->queuedbhandler->dbRemoveMailFromQueue($mailid);
			$this->logger->addMsg(_T('Mailing deleted.'));
			bmRedirect('../../../plugins/mailingqueue/queue_main.php?action=no');	//PUT AWAY!!! TODO delete action=no
			return TRUE;	//Never reached?
		}
	} //deleteMailing
	
	
	public function sendMailing($mailid) {
		
		if (is_numeric($mailid)) {
			
			// Get Mail Data and put in the $pommo variable for the send procedure in mailings_send1,2,3,4.php
			$mailing = $this->queuedbhandler->dbGetMailingData($mailid); //current(dbGetMailingInfo($dbo, $mailid));
			$this->poMMo->set(array(
				'mailingData' => array (
					'fromname' => $mailing['fromname'],
					'fromemail' => $mailing['fromemail'],
					'frombounce' => $mailing['frombounce'],
					'subject' => $mailing['subject'],
					'ishtml' => $mailing['ishtml'],
					'charset' => $mailing['charset'],
					/*'mailgroup' => ($mailing['mailgroup'] == 'all')? 'all' :
						getGroupId($this->dbo,$mailing['mailgroup']),*/
					'mailgroup' => $mailing['mailgroup'],		//Mailgroup ID is already stored
					'altbody' => $mailing['altbody'],
					'body' => $mailing['body']
				)
			));
			//echo "<h2>";
			//print_r($this->poMMo->mailingData); echo "</h2>";
			//bmRedirect('mailings_send.php');
		} 
		//If all ok CHECK
		bmRedirect('../../mailings/mailings_send3.php');//?action=no');

	} //sendMailing
	
	
	public function viewMailing($mailid) {
		
		// Load a Mailid from database and return it to display
		$mailing =  $this->queuedbhandler->dbGetMailingData($mailid);
		//print_r($mailing);
		return $mailing;
	} //viewMailing
	
	
	
} //QueuePlugin



?>

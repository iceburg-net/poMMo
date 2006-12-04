<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

//(ct)

/**********************************
	INITIALIZATION METHODS
 *********************************/

 
require('../../bootstrap.php');
require_once ($pommo->_baseDir.'inc/db_history.php');

$pommo =& fireup("secure");
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/

// default key/value pairs of this page's state
$pmState = array(
	'mailid' => NULL,
	'action' => NULL
);
$pommo->stateInit('mailings_mod',$pmState);

$action = $pommo->stateVar('action',$_REQUEST['action']);
$mailid = $pommo->stateVar('mailid',$_REQUEST['mailid']);

// if mailid or action are empty - redirect
// TODO -> perhaps perform better validation of action/mailID here
//  e.g. have a validType($var,'rule') function? i.e. validType($mailid,numeirc)
if (empty($action) || empty($mailid)) {
	var_dump($action,$mailid,$_REQUEST);
	die();
	Pommo::redirect('mailings_history.php');
}

Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Mailing History'));
$smarty->assign('mailid',$mailid);
$smarty->assign('action',$action);

// perform deletions if requested
if (!empty($_REQUEST['deleteMailings']) && !empty($_REQUEST['delid'])) {
	if (dbRemoveMailFromHistory($dbo, $_REQUEST['delid']))
		Pommo::redirect('mailings_history.php');
	else
		$logger->addErr(Pommo::_T('Trouble deleteing mailgs'));
}

// ACTIONS -> choose what we want to do.
switch ($action) {

	case 'view': 
		$smarty->assign('actionStr', Pommo::_T('Mailing View'));
		$noassign = TRUE;					
	case 'delete': 
		$mailings = dbGetMailingInfo($dbo, $mailid);
		if (!isset($noassign))
			$smarty->assign('actionStr', Pommo::_T('Mailing Delete'));
		$smarty->assign('mailings',$mailings);
		
		// assign body to session mailing_data
		foreach ($mailings as $key=>$mailing) {
			if ($mailing['ishtml'] == 'on')
				$pommo->set(array(
					'mailingData'.$key => array (
						'body' => $mailing['body']
						)
					));
		}
		
		break;

	case 'reload': 
			//Mailid can only be numeric because reloading of multiple Mailings doesn't make sense
			if (is_numeric($mailid)) {
				// Get Mail Data and put in the $pommo variable for the send procedure in mailings_send1,2,3,4.php
				$mailing = current(dbGetMailingInfo($dbo, $mailid));
				$pommo->set(array(
					'mailingData' => array (
						'fromname' => $mailing['fromname'],
						'fromemail' => $mailing['fromemail'],
						'frombounce' => $mailing['frombounce'],
						'subject' => $mailing['subject'],
						'ishtml' => $mailing['ishtml'],
						'charset' => $mailing['charset'],
						'mailgroup' => ($mailing['mailgroup'] == 'all')? 'all' :
							getGroupId($dbo,$mailing['mailgroup']),
						'altbody' => $mailing['altbody'],
						'body' => $mailing['body']
						)
					));
				Pommo::redirect('mailings_send.php');
			} else {
				Pommo::redirect('mailings_history.php');
			}
			
			break;
	default:
		Pommo::kill('Error; unknown action.');
} //switch
	
$smarty->display('admin/mailings/mailings_mod.tpl');
Pommo::kill();
?>
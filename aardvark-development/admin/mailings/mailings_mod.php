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

define('_IS_VALID', TRUE);
 
require('../../bootstrap.php');

require_once (bm_baseDir.'/inc/db_history.php');

$poMMo =& fireup("secure");
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

 
 	// vars
	$appendUrl = "limit=".$_REQUEST['limit']."&order=".$_REQUEST['order']."&orderType=".$_REQUEST['orderType']; 

 
 	// Smarty Init
	$smarty = & bmSmartyInit();
	$smarty->assign('returnStr', _T('Mailing History'));


	// if mailid or action are empty - redirect
	if (empty ($_REQUEST['mailid']) || empty ($_REQUEST['action'])) {
		bmRedirect('mailings_history.php?'.$appendUrl);
	}

	// Actions with a record
	$action = $_REQUEST['action'];
	$mailid = $_REQUEST['mailid'];
	$order = $_REQUEST['order'];
	$orderType = $_REQUEST['orderType'];
	$limit = $_REQUEST['limit'];



	if (!empty($_REQUEST['submitone'])) {

		$delid = $_REQUEST['submitone'];
		if (dbRemoveMailFromHistory($dbo, $delid)) {
			$logger->addMsg(_T('Delete mailing: Delete successful.'));
		} else {
			$logger->addErr(_T("Could not delete Mailing with ID: ". $delid));
		}

	} elseif (!empty($_REQUEST['submitall'])) {

		// To delete we wait for user confirmation and then return to mailings history
		if (!empty($_REQUEST['deleteEmails'])) { 
			
			$delid = $_REQUEST['deleteEmails']; 
			if (dbRemoveMailFromHistory($dbo, $delid)) {
				$logger->addMsg(_T('Delete mailing: Delete successful.'));
				bmRedirect('mailings_history.php?'.$appendUrl);
			} else {
				$logger->addErr(_T("Could not delete Mailing with ID: ". $delid));
			}
			
		} else {
			$logger->addErr(_T('Could not delete mailing. The supplied ID is not valid.'));
		}

		// maybe some better redirecting?	
		// All submits empty
		bmRedirect('mailings_history.php?'.$appendUrl);
				
	} 


 	// ACTIONS -> choose what we want to do.
 	switch ($action) {
	
			case 'view': 

					// Get Mailing Data from DB
					$mailings = dbGetMailingInfo($dbo, $mailid);			//print_r($mailings);
					$numbertodisplay = count($mailings);
					$smarty->assign('actionStr', _T('Mailing View'));
					$smarty->assign('mailings',$mailings);
					$smarty->assign('numbertodisplay', $numbertodisplay);
					break;
					
			case 'delete': 

					// Get Mailing data from Mails that are to be deleted
					$mailings = dbGetMailingInfo($dbo, $mailid);			//print_r($mailings);
					$numbertodisplay = count($mailings);
					$smarty->assign('actionStr', _T('Mailing Delete'));
					$smarty->assign('mailings',$mailings);
					$smarty->assign('numbertodisplay', $numbertodisplay);
					
					break;

			case 'reload': 

					//Mailid can only be numeric because reloading of multiple Mailings doesn't make sense
					if (is_numeric($mailid)) {

						// Get Mail Data and put in the $pommo variable for the send procedure in mailings_send1,2,3,4.php
						$mailings = dbGetMailingInfo($dbo, $mailid);
						$body = dbGetHTMLBody($dbo, $mailid);
						$mailings[0]['body'] = $body['body'];
						$poMMo->set($mailings[0]);
					
						bmRedirect('mailings_send.php');
						
					} else {
						$logger->addMsg(_T('Could not reload mailing. The supplied ID is not valid.'));
					}
					
					break;
				
	} //switch
	
 
/* Taken From subscriber table management
			//-----
			// I dont do this:
			// What if 1 ID is not found in the DB, eg it has been deleted from DB 
			// in the time between the selection and the displaying
			//------
			if (is_array($_REQUEST['sid']) && count($_REQUEST['sid']) > 15) {
			$_REQUEST['sid'] = array_slice($_REQUEST['sid'], 0, 15);
			$subCount = 15;
			$smarty->assign('cropped', TRUE);

*/


	/**********************************
		SETUP/COMPLETE TEMPLATE PAGE
	 *********************************/

	$smarty->assign('mailid',$mailid);
	$smarty->assign('action',$action);

	$smarty->assign('limit',$limit);
	$smarty->assign('order',$order);
	$smarty->assign('orderType',$orderType);

	$smarty->display('admin/mailings/mailings_mod.tpl');
	bmKill();


?>


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
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();
 

 	// vars
	$appendUrl = "limit=".$_REQUEST['limit']."&order=".$_REQUEST['order']."&orderType=".$_REQUEST['orderType']; 

 
 	// Smarty Init
	$smarty = & bmSmartyInit();
	$smarty->assign('returnStr', _T('Mailing History'));


	// if mailid and action are empty - redirect
	if (empty ($_REQUEST['mailid']) || empty ($_REQUEST['action'])) {
		bmRedirect('mailings_history.php?'.$appendUrl);
	}

	// Actions with a record
	// Decide what we yet want to do 	
	$action = $_REQUEST['action'];
	$mailid = $_REQUEST['mailid'];


	//$typ = gettype($mailid);


	// To delete we wait for user confirmation and then return to mailings history
	if (!empty ($_POST['deleteEmails'])) {

		if (!empty($_REQUEST['deleteEmails'])) { 
				$delid = $_REQUEST['deleteEmails']; 
				$retstr = dbRemoveMailFromHistory($dbo, $delid);
		} else {
				$errorstr .= "Delete: Mail ID is empty.<br>";
		}
		// Decide where oder IF we display the errorstr, returnstring?
		// echo $errorstr; echo $retstr;
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
	
	} //switch
	
 
/*

From subscriber table management

switch ($_REQUEST['action']) {
	case "edit" :

		if (is_array($_REQUEST['sid']) && count($_REQUEST['sid']) > 15) {
			
			//-----
			// I dont do this:
			// What if 1 ID is not found in the DB, eg it has been deleted from DB 
			// in the time between the selection and the displaying
			//------
		
			$_REQUEST['sid'] = array_slice($_REQUEST['sid'], 0, 15);
			$subCount = 15;
			$smarty->assign('cropped', TRUE);
		}
		$subscribers = dbGetSubscriber($dbo, $_REQUEST['sid'], 'detailed', $table);
		$smarty->assign('subscribers',$subscribers);
		break;

	case "delete" :
	
		$emails = dbGetSubscriber($dbo, $_REQUEST['sid'], 'email', $table);
		$smarty->assign('emails',$emails);
		break;

}
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


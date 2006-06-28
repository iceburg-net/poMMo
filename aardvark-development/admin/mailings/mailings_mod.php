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
	$order = $_REQUEST['order'];
	$orderType = $_REQUEST['orderType'];
	$limit = $_REQUEST['limit'];

	//$typ = gettype($mailid);


	if (!empty($_REQUEST['submitone'])) {

		$delid = $_REQUEST['submitone'];
		$retstr = dbRemoveMailFromHistory($dbo, $delid);

	} elseif (!empty($_REQUEST['submitall'])) {

		// To delete we wait for user confirmation and then return to mailings history
		if (!empty($_REQUEST['deleteEmails'])) { 
			$delid = $_REQUEST['deleteEmails']; 
			$retstr = dbRemoveMailFromHistory($dbo, $delid);
		} else {
			echo "<i>Delete: Mail ID is empty.</i><br>";
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

					// Save for later retrieval in $poMMo CANCELLED -> do it in mailng_preview.php
/*					if (($mailings[0]['ishtml'] == "on") && (is_numeric($mailid)) ) {
						$htmltext['body'] = $mailings[0]['body'];
						$poMMo->set($htmltext);
					}
					$mailbodies = dbGetHTMLBody($dbo, $mailid);
					print_r($mailbodies);*/
	

					
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




/*	GET:{$smarty.get.page}<br>
	POST: {$smarty.post.page}<br>
	echo "POSTDATA"; print_r($_POST); echo "<br><br>";
	echo "REQUEST:"; print_r($_REQUEST);  echo "<br><br>";
*/



?>


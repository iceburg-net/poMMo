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
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_mailing.php');
require_once (bm_baseDir.'/inc/db_groups.php');
require_once (bm_baseDir . '/inc/lib.txt.php');
require_once (bm_baseDir.'/inc/db_sqlgen.php');

$poMMo = & fireup('secure', 'keep');
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

// check to see if a mailing is taking place (queue not empty)
if (!mailingQueueEmpty($dbo)) {
	bmKill(sprintf(_T('A mailing is already taking place. Please allow it to finish before creating another. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php"', '</a>'));
}

$input = $poMMo->get();

$groupName = dbGroupName($dbo, $input['group_id']);
$subscriberCount = dbGroupTally($dbo, $input['group_id']);
$input['subscriberCount'] = $subscriberCount;
$input['groupName'] = $groupName;


// redirect (restart) if body or group id are null...
if (empty($input['group_id']) || empty($input['body'])) {
	bmRedirect('mailings_send.php');
}

// send a test mail to an address if requested
if (!empty($_POST['testMail'])) {
	if (isEmail($_POST['testTo'])) {
		require_once (bm_baseDir.'/inc/lib.mailings.php');
		$logger->addMsg(bmSendTestMailing($_POST['testTo'],$input));	
		}
	else
		$logger->addMsg(_T('Invalid Email Address'));
}

// if sendaway variable is set (user confirmed mailing parameters), send mailing & redirect.
if (!empty ($_GET['sendaway'])) {
	
	$securityCode = dbMailingCreate($dbo, $input);
	dbQueueCreate($dbo, dbGetGroupSubscribers($dbo, 'subscribers', $input['group_id'], 'email'));
	
	dpoMMoingStamp($dbo, "start");
	
	bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?securityCode='.$securityCode);
	sleep(1); // allows mailing to begin...
	bmRedirect('mailing_status.php');
	}

$smarty->assign($input);
$smarty->display('admin/mailings/mailings_send3.tpl');

?>
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


require ('../../bootstrap.php');
require_once ($pommo->_baseDir . '/inc/db_mailing.php');
require_once ($pommo->_baseDir.'/inc/db_groups.php');
require_once ($pommo->_baseDir . '/inc/lib.txt.php');
require_once ($pommo->_baseDir.'/inc/db_sqlgen.php');

$pommo = & fireup('secure', 'keep');
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// check to see if a mailing is taking place (queue not empty)
if (!mailingQueueEmpty($dbo)) {
	Pommo::kill(sprintf(Pommo::_T('A mailing is already taking place. Please allow it to finish before creating another. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php">', '</a>'));
}

$input = $pommo->get('mailingData');

$groupName = dbGroupName($dbo, $input['mailgroup']);
$subscriberCount = dbGroupTally($dbo, $input['mailgroup']);
$input['subscriberCount'] = $subscriberCount;
$input['groupName'] = $groupName;


// redirect (restart) if body or group id are null...
if (empty($input['mailgroup']) || empty($input['body'])) {
	Pommo::redirect('mailings_send.php');
}

// send a test mail to an address if requested
if (!empty($_POST['testMail'])) {
	if (isEmail($_POST['testTo'])) {
		require_once ($pommo->_baseDir.'/inc/lib.mailings.php');
		$logger->addMsg(bmSendTestMailing($_POST['testTo'],$input));	
		}
	else
		$logger->addMsg(Pommo::_T('Invalid Email Address'));
}

// if sendaway variable is set (user confirmed mailing parameters), send mailing & redirect.
if (!empty ($_GET['sendaway'])) {
	if (intval($subscriberCount) >= 1) {
		$securityCode = dbMailingCreate($dbo, $input);
		dbQueueCreate($dbo, dbGetGroupSubscribers($dbo, 'subscribers', $input['mailgroup'], 'email'));
		dbMailingStamp($dbo, "start");
		
		if (bmHttpSpawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$securityCode)) {
			sleep(1); // allows mailing to begin...
			Pommo::redirect('mailing_status.php');
		}
		//die ($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$securityCode);
	}
	else {
		$logger->addMsg(Pommo::_T('Cannot send a mailing to 0 subscribers!'));
	}
}

$smarty->assign($input);
$smarty->display('admin/mailings/mailings_send3.tpl');

?>

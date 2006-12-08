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
require('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/pending.php');

$pommo->init(array('authLevel' => 0, 'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (isset($_GET['input'])) {
	$input = (unserialize($_GET['input']));
}

$pending = (isset($input['adminID'])) ? // check to see if we're resetting admin password
	PommoPending::getBySubID(0) :
	PommoPending::getByEmail($input['Email']);
if (!$pending) 	
	Pommo::redirect('login.php');

// check if user wants to reconfirm or cancel their request
if (!empty ($_POST)) {
	if (isset ($_POST['reconfirm'])) {
		Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
		
		switch ($pending['type']) {
			case "add" :
				PommoHelperMessages::sendConfirmation($input['Email'], $pending['pending_code'], 'subscribe');
				break;
			case "change" :
				PommoHelperMessages::sendConfirmation($input['Email'], $pending['pending_code'], 'update');
				break;
			case "del" :
				PommoHelperMessages::sendConfirmation($input['Email'], $pending['pending_code'], 'unsubscribe');
				break;
			case "password" :
				PommoHelperMessages::sendConfirmation($input['Email'], $pending['pending_code'], 'password');
				break;
		}
		$logger->addMsg(sprintf(Pommo::_T('A confirmation email has been sent to %s. It should arrive within the next few minutes. Please follow its instructions to complete your request. Thanks!'),$input['Email']));
	} elseif (isset($_POST['cancel'])) {
		PommoPending::cancel($pending);
		$logger->addMsg(Pommo::_T('Your pending request has been cancelled.'));		
	}
	$smarty->assign('nodisplay',TRUE);
} else {
	switch ($pending['type']) {
		case "add" :
		case "del" :
		case "change" :
		case "password" :
			$logger->addMsg(Pommo::_T('You have pending changes. Please respond to your confirmation email'));
			break;
		default :
			$logger->addErr(sprintf(Pommo::_T('Please Try Again! %s login %s'), '<a href="' . $pommo->_baseUrl . 'user/login.php">', '</a>'));
	}
}
$smarty->display('user/user_pending.tpl');
Pommo::kill();
?>
<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
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
				$status = PommoHelperMessages::sendConfirmation($input['Email'], $pending['code'], 'subscribe');
				break;
			case "change" :
				$status = PommoHelperMessages::sendConfirmation($input['Email'], $pending['code'], 'update');
				break;
			case "password" :
				$status = PommoHelperMessages::sendConfirmation($input['Email'], $pending['code'], 'password');
				break;
		}
		if (!$status) 
			$logger->addErr(Pommo::_T('Error sending mail'));
		else
			$logger->addMsg(sprintf(Pommo::_T('A confirmation email has been sent to %s. It should arrive within the next few minutes. Please follow its instructions to complete your request. Thanks!'),$input['Email']));
	} elseif (isset($_POST['cancel'])) {
		PommoPending::cancel($pending);
		$logger->addMsg(Pommo::_T('Your pending request has been cancelled.'));		
	}
	$smarty->assign('nodisplay',TRUE);
} else {
	switch ($pending['type']) {
		case "add" :
		case "change" :
		case "password" :
			$logger->addMsg(Pommo::_T('You have pending changes. Please respond to your confirmation email'));
			break;
		default :
			$logger->addErr(sprintf(Pommo::_T('Please Try Again! %s login %s'), '<a href="' . $pommo->_baseUrl . 'user/login.php">', '</a>'));
	}
}
$smarty->display('user/pending.tpl');
Pommo::kill();
?>
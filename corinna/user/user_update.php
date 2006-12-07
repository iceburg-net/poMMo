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
require ('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/pending.php');

$pommo->init(array('authLevel' => 0,'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// Prepare for subscriber form -- load in fields + POST/Saved Subscribe Form
$smarty->prepareForSubscribeForm(); 

$_POST['Email'] = $smarty->get_template_vars('Email');

$subscriber = current(PommoSubscriber::get(array('email' => $_POST['Email'])));

if (empty($subscriber))
	Pommo::redirect('login.php');
	
if (!isset($_POST['d']))
	$smarty->assign('d', $subscriber['data']); 


if (!empty ($_POST['update'])) {
	// validate new subscriber info (also converts dates to ints)
	if (!empty($_POST['newemail']) && $_POST['newemail'] != $_POST['newemail2']) {
		$logger->addErr(Pommo::_T('Emails must match.'));
	}
	elseif (PommoValidate::subscriberData($_POST['d'])) {
		
		if (!empty($_POST['newemail']) && PommoHelper::isEmail($_POST['newemail']))
			$subscriber['email'] = $_POST['newemail'];
		$subscriber['data'] = $_POST['d'];
		
		// uses less space in DB if we strip out registered, touched, flag, etc.
		//  also has MAGIC FUNCTIONALITY of removing update flag (by not remembering it)
		$newsub = array(
			'id' => $subscriber['id'],
			'email' => $subscriber['email'],
			'data' => $subscriber['data']
		);
		
		$code = PommoPending::add($newsub, 'change');
		if (empty($code)) {
			$logger->addMsg(Pommo::_T('The system could not process your request. Perhaps you already have requested a change?') . 
			sprintf(Pommo::_T('%s Click Here %s to try again.'),'<a href="'.$pommo->_baseUrl.'user/login.php?Email='.$subscriber['email'].'">','</a>'));
		} else {
			Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/mailings.php');
			PommoHelperMailings::sendConfirmation($subscriber['email'], $code, 'update');
			$logger->addMsg(Pommo::_T('Update request received.') . ' ' . Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
		}
	}
}
elseif (!empty ($_POST['unsubscribe'])) {
	$code = PommoPending::add($subscriber, 'del');
	if (empty ($code))
		$logger->addMsg(Pommo::_T('The system could not process your request. Perhaps you already have requested a change?') .
		sprintf(Pommo::_T('%s Click Here %s to try again.'),'<a href="'.$pommo->_baseUrl.'user/login.php?Email='.$subscriber['email'].'">','</a>'));
	else {
		Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/mailings.php');
		PommoHelperMailings::sendConfirmation($subscriber['email'], $code, 'unsubscribe');
		$logger->addMsg(Pommo::_T('Unsubscribe request received.') . ' ' . Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
	
	}
} 

$smarty->display('user/user_update.tpl');
Pommo::kill();
?>
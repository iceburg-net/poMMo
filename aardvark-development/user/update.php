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
$subscriber = current(PommoSubscriber::get(array('email' => $_POST['Email'], 'status' => 1)));

if (empty($subscriber))
	Pommo::redirect('login.php');
	
// check if we have pending request
if (PommoPending::isPending($subscriber['id'])) {
	$input = urlencode(serialize(array('Email' => $_POST['Email'])));
	Pommo::redirect('pending.php?input='.$input);
}
	
if(isset($_POST['logout'])) {
	PommoPending::actCodeDie($subscriber['email']);
	Pommo::redirect('login.php');
}
	
// make sure email is activated
if (!PommoPending::actCodeTry(false, $subscriber['email'])) 
	Pommo::redirect('update_activate.php?Email='.$subscriber['email']);


if (!isset($_POST['d']))
	$smarty->assign('d', $subscriber['data']); 

if (!empty ($_POST['update'])) {
	// validate new subscriber info (also converts dates to ints)
	if (!empty($_POST['newemail']) && $_POST['newemail'] != $_POST['newemail2']) {
		$logger->addErr(Pommo::_T('Emails must match.'));
	}
	elseif (PommoValidate::subscriberData($_POST['d'])) {
		
		$newsub = array(
			'id' => $subscriber['id'],
			'email' => $subscriber['email'],
			'data' => $_POST['d']
		);
		
		// only send confirmation mail if subscriber changed email address, else UPDATE
		if (!empty($_POST['newemail']) && PommoHelper::isEmail($_POST['newemail'])) {
			$newsub['email'] = $_POST['newemail'];
			
			$code = PommoPending::add($newsub, 'change');
			if (empty($code)) {
				$logger->addMsg(Pommo::_T('The system could not process your request. Perhaps you already have requested a change?') . 
				sprintf(Pommo::_T('%s Click Here %s to try again.'),'<a href="'.$pommo->_baseUrl.'user/login.php?Email='.$subscriber['email'].'">','</a>'));
			} else {
				Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
				PommoHelperMessages::sendConfirmation($newsub['email'], $code, 'update');
				$logger->addMsg(Pommo::_T('Update request received.') . ' ' . Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
				PommoPending::actCodeDie($subscriber['email']);
			}
		}
		else {
			if (!PommoSubscriber::update($newsub))
				$logger->addErr('Error updating subscriber.');
			else
				$logger->addMsg(Pommo::_T('Your records have been updated.'));
		}
	}
}
elseif (!empty ($_POST['unsubscribe'])) {
	$newsub = array(
		'id' => $subscriber['id'],
		'status' => 0,
		'data' => array()
	);
	if (!PommoSubscriber::update($newsub, FALSE))
		$logger->addErr('Error updating subscriber.');
	else {
		$dbvalues = PommoAPI::configGet(array('messages'));
		$messages = unserialize($dbvalues['messages']);
		$logger->addMsg($messages['unsubscribe']['suc']);
		$smarty->assign('unsubscribe', TRUE);
		PommoPending::actCodeDie($subscriber['email']);
	}
}

$smarty->display('user/update.tpl');
Pommo::kill();
?>
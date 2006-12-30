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
require ('../bootstrap.php');\
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

$subscriber = current(PommoSubscriber::get(array('email' => $_REQUEST['Email'], 'status' => 1)));

// make sure email be valid
if (empty($subscriber))
	Pommo::redirect('login.php');
	

// verify activation code (if sent) || that user is not already activated
$code = (isset($_GET['codeTry'])) ? $_GET['codeTry'] : false;
if (PommoPending::actCodeTry($subscriber['email']) {
	$input = urlencode(serialize(array('Email' => $subscriber['email'])));
	Pommo::redirect('update.php?input='.$input);
}
if ($code)
	$logger->addErr(Pommo::_T('Invalid Activation Code!'));


// check to see if activation code exists  --- returns false if no act code for this email.. or the code if it exists
$code = PommoPending::actCodeSent($subscriber['email']);
if (!$code)
	$code = PommoHelper::makeCode();

// check for request to send activation code
if (!empty($_GET['send'])) {
	Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
	PommoHelperMessages::sendConfirmation($subscriber['email'], $code, 'activate');
	$logger->addMsg(Pommo::_T('Activation request received.') . ' ' . Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
}

$smarty->assign('Email', $subscriber['email']);
$smarty->display('user/update_activate.tpl');
Pommo::kill();
?>
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

// make sure email be valid
$email = $_REQUEST['Email'];
if (!PommoHelper::isDupe($email))
	Pommo::redirect('login.php');

// verify activation code (if sent) || that user is not already activated
$code = (isset($_GET['codeTry'])) ? $_GET['code'] : false;
if (PommoPending::actCodeTry($code, $email)) {
	$input = urlencode(serialize(array('Email' => $email)));
	Pommo::redirect('update.php?input='.$input);
}
if ($code !== false)
	$logger->addErr(Pommo::_T('Invalid Activation Code!'));


// check for request to send activation code
if (!empty($_GET['send'])) {
	$code = PommoPending::actCodeGet($email);
	Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
	if (!PommoHelperMessages::sendConfirmation($email, $code, 'activate'))
		$logger->addErr(Pommo::_T('Error sending mail')); 
	else
		$logger->addMsg(Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
}

$smarty->assign('Email', $email);
$smarty->display('user/update_activate.tpl');
Pommo::kill();
?>
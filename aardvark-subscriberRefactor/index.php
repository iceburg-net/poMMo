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
require ('bootstrap.php');
$pommo->init(array('authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// log the user out if requested
if (isset($_GET['logout'])) {
	if (class_exists('PommoAuth'))
		$pommo->_auth->logout();
	header('Location: ' . $pommo->_http . $pommo->_baseUrl . 'index.php');
}
// check if user is already logged in
elseif (class_exists('PommoAuth') && $pommo->_auth->isAuthenticated()) {
	// If user is authenticated (has logged in), redirect to admin.php
	Pommo::redirect($pommo->_http . $pommo->_baseUrl . 'admin/admin.php');
}
// Check if user submitted correct username & password. If so, Authenticate.
elseif (!empty ($_POST['username']) || !empty ($_POST['password'])) {	
	$auth = PommoAPI::getConfig(array (
		'admin_username',
		'admin_password'
	));
	if ($_POST['username'] == $auth['admin_username'] && md5($_POST['password']) == $auth['admin_password']) {
		
		// LOGIN SUCCESS -- PERFORM MAINTENANCE, SET AUTH, REDIRECT TO REFERER
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/maintenance.php');
		PommoMaintenance::memorizeBaseURL();

		$pommo->_auth->login($_POST['username']);
		
		Pommo::redirect($pommo->_http . $_POST['referer']);
	}
	else {
		$logger->addMsg(Pommo::_T('Failed login attempt. Try again.'));
	}
}
elseif (!empty ($_POST['resetPassword'])) { // TODO -- visit this function later
	// Check if a reset password request has been received

	// check that captcha matched
	if (!isset($_POST['captcha'])) {
		// generate captcha
		$captcha = substr(md5(rand()), 0, 4);

		$smarty->assign('captcha', $captcha);
	}
	elseif ($_POST['captcha'] == $_POST['realdeal']) {
		// user inputted captcha matched. Reset password

		require_once (bm_baseDir . '/inc/db_subscribers.php');
		require_once (bm_baseDir . '/inc/lib.mailings.php');

		// see if there is already a pending request for the administrator
		if (isDupeEmail($dbo, $poMMo->_config['admin_email'], 'pending')) {
			$poMMo->set(array (
				'email' => $poMMo->_config['admin_email']
			));
			Pommo::redirect($pommo->_http . $pommo->_baseUrl . 'user/user_pending.php');
		}

		// create a password change request, send confirmation mail
		$code = dbPendingAdd($dbo, "password", $poMMo->_config['admin_email']);
		if (!empty ($code)) {
			bmSendConfirmation($poMMo->_config['admin_email'], $code, "password");
		}

		$logger->addMsg(Pommo::_T('Password reset request recieved. Check your email.'));
		$smarty->assign('captcha',FALSE);
		
	} else {
		// captcha did not match
		$logger->addMsg(Pommo::_T('Captcha did not match. Try again.'));
	}
}

// referer (used to return user to requested page upon login success)
$smarty->assign('referer',(isset($_REQUEST['referer']) ? $_REQUEST['referer'] : $pommo->_baseUrl . 'admin/admin.php'));

$smarty->display('index.tpl');
die();
?>
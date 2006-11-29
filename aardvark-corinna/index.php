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

require ('bootstrap.php');
//corinna
if ($useplugins) {
require ('plugins/multiuser/test.php');
}
///corinna

$poMMo = & fireup();
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

if (isset ($_GET['logout'])) {
	// if user chose to logout, destroy session and redirect to this page
	$poMMo->setAuthenticated(FALSE);
	session_destroy();
	header('Location: ' . bm_http . bm_baseUrl . '/index.php');
}
elseif ($poMMo->isAuthenticated()) {
	// If user is authenticated (has logged in), redirect to admin.php
	bmRedirect(bm_http . bm_baseUrl . '/admin/admin.php');
}
elseif (!empty ($_POST['username']) || !empty ($_POST['password'])) {


	//<corinna> TODO

	if ( !empty ($_REQUEST['username']) && !empty ($_REQUEST['password']) ) {


//TODO Get this from a DB !!!!
$authwithplugin = TRUE;


							/* Normal Authentication */
							// ALWAYS check for Administrator login -> else some problems when config changes
							// Check if user submitted correct username & password. If so, Authenticate.
							$auth = $poMMo->getConfig(array (
								'admin_username',
								'admin_password'
							));
							if ($_REQUEST['username'] == $auth['admin_username'] && md5($_REQUEST['password']) == $auth['admin_password']) {
								// LOGIN SUCCESS -- PERFORM MAINTENANCE, SET AUTH, REDIRECT TO REFERER
								bmMaintenance();
								$poMMo->setAuthenticated(TRUE);
								
								// Add username & encrypted password information to session: Use: Authenticate user and his permissions
								$_SESSION['pommo']['user'] = $_REQUEST['username'];	//corinna
								$_SESSION['pommo']['md5pass'] = md5($_REQUEST['password']);	//corinna
								$_SESSION['pommo']['perm'] = getPerm($dbo, $_REQUEST['username']);
													
								bmRedirect(bm_http . $_REQUEST['referer']);
								
							} elseif ($authwithplugin) {
							
								// Try to authenticate as user	
							
										
										include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/class.authfactory.php');
										include_once(bm_baseDir.'/plugins/adminplugins/adminuser/authentication/class.auth.php');
						
										// Factory returns the Object with witch to validate -> Is set in the plugin configuration
										$authfactory = new AuthFactory($dbo, $logger);
										$authobj = $authfactory->selectReturnObject();
										
						
										if ($authobj instanceof Auth) {
											
											//echo "<h3>Authentication Method: {$authfactory->getAuthMethod()}</h3>";
											
												//TODO $_REQUEST  md5
												if ( $authobj->authenticate($_REQUEST['username'], $_REQUEST['password']) == TRUE ) {
													// LOGIN SUCCESS -- PERFORM MAINTENANCE, SET AUTH, REDIRECT TO REFERER
													bmMaintenance();
																	
													$poMMo->setAuthenticated(TRUE);
													
													// Add username information to session: Use: Authenticate user and his permissions
													$_SESSION['pommo']['user'] = $_REQUEST['username'];	//corinna
													$_SESSION['pommo']['md5pass'] = md5($_REQUEST['password']);	//corinna
													$_SESSION['pommo']['perm'] = getPerm($dbo, $_REQUEST['username']); //CORINNA
													
													bmRedirect(bm_http . $_REQUEST['referer']);
													
												} else {
													$logger->addMsg(_T('Failed login attempt. Try again. (User not accepted.)'));
												}
											
											
										} else {
											$logger->addErr(_T('Login failed. Check config of auth methods.'));
										}
						


							} else { // else login failed
								$logger->addMsg(_T('Failed login attempt. Try again.'));
							}


					


	
	} else {
		
		// if both fields not empty
		$logger->addMsg(_T('Failed login attempt. Try again. (Field missing.)'));
	}
	
//</corinna>		


/*
		// Check if user submitted correct username & password. If so, Authenticate.
		$auth = $poMMo->getConfig(array (
			'admin_username',
			'admin_password'
		));
		if ($_POST['username'] == $auth['admin_username'] && md5($_POST['password']) == $auth['admin_password']) {
			
			// LOGIN SUCCESS -- PERFORM MAINTENANCE, SET AUTH, REDIRECT TO REFERER
			bmMaintenance();
			
			$poMMo->setAuthenticated(TRUE);
			bmRedirect(bm_http . $_POST['referer']);
		}
		else {
			$logger->addMsg(_T('Failed login attempt. Try again.'));
		}
*/

}
elseif (!empty ($_POST['resetPassword'])) {
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
			bmRedirect(bm_http . bm_baseUrl . '/user/user_pending.php');
		}

		// create a password change request, send confirmation mail
		$code = dbPendingAdd($dbo, "password", $poMMo->_config['admin_email']);
		if (!empty ($code)) {
			bmSendConfirmation($poMMo->_config['admin_email'], $code, "password");
		}

		$logger->addMsg(_T('Password reset request recieved. Check your email.'));
		$smarty->assign('captcha',FALSE);
		
	} else {
		// captcha did not match
		$logger->addMsg(_T('Captcha did not match. Try again.'));
	}
}

// referer (used to return user to requested page upon login success)
$smarty->assign('referer',(isset($_REQUEST['referer']) ? $_REQUEST['referer'] : bm_baseUrl . '/admin/admin.php'));

$smarty->display('index.tpl');
die();
?>
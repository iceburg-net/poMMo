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

require('../bootstrap.php');
require_once (bm_baseDir . '/inc/db_subscribers.php');

$poMMo = & fireup();
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();
$poMMo->loadConfig();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->assign('title', $poMMo->_config['site_name'] . ' - ' . _T('subscriber logon'));


$smarty->prepareForForm();

if (!SmartyValidate::is_registered_form() || empty($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate :: connect($smarty, true);
	SmartyValidate :: register_validator('email', 'Email', 'isEmail', false, false, 'trim');
	
	$formError = array();
	$formError['email'] = _T('Invalid email address');
	$smarty->assign('formError',$formError);
}
else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);
	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID __
		if (isDupeEmail($dbo, $_POST['Email'], 'pending')) {
			// __EMAIL IN PENDING TABLE, REDIRECT
			$poMMo->set(array('email' => $_POST['Email']));
			SmartyValidate :: disconnect();
			bmRedirect('user_pending.php');
}
		elseif (isDupeEmail($dbo, $_POST['Email'], 'subscribers')) {
			// __ EMAIL IN SUBSCRIBERS TABLE, REDIRECT
			$poMMo->set(array('saveSubscribeForm' => array('bm_email' => $_POST['Email'])));
			SmartyValidate :: disconnect();
			bmRedirect('user_update.php');
		} else {
			// __ REPORT STATUS
			$logger->addMsg(_T('That email address was not found in our system. Please try again.'));
		}
	}
	$smarty->assign($_POST);
}
$smarty->display('user/login.tpl');
bmKill();
?>
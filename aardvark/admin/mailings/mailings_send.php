<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://bmail.sourceforge.net/
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

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_groups.php');
require_once (bm_baseDir . '/inc/db_mailing.php');

$bMail = & fireup('secure', 'keep');
$logger = & $bMail->logger;
$dbo = & $bMail->openDB();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

// check to see if a mailing is taking place (queue not empty)
if (!mailingQueueEmpty($dbo)) {
	bmKill(sprintf(_T('A mailing is already taking place. Please allow it to finish before creating another. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php"', '</a>'));
}

// get groups for select -- key == ID, val == group name
$groups = dbGetGroups($dbo);
$smarty->assign('groups', $groups);

if ($bMail->_config['demo_mode'] == 'on')
	$logger->addMsg(_T('Demonstration Mode is on. No Emails will be sent.'));

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);

	SmartyValidate :: register_validator('fromname', 'fromname', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('subject', 'subject', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('fromemail', 'fromemail', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('frombounce', 'frombounce', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('mailtype', 'mailtype:/(html|plain)/i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('group_id', 'group_id:/(all|\d+)/i', 'isRegExp', false, false, 'trim');

	$formError = array ();
	$formError['fromname'] = $formError['subject'] = _T('Cannot be empty.');

	$formError['fromemail'] = $formError['frombounce'] = _T('Invalid email address');

	$formError['mailtype'] = $formError['group_id'] = _T('Invalid Input');

	$smarty->assign('formError', $formError);

	// populate _POST with info from database (fills in form values...) or historic input if set...
	$historic = $bMail->get();
	if (isset ($historic['fromname']))
		$_POST = $historic;
	else {
		$dbvalues = $bMail->getConfig(array (
			'list_fromname',
			'list_fromemail',
			'list_frombounce'
		));
		if (!isset ($_POST['fromname']))
			$_POST['fromname'] = $dbvalues['list_fromname'];
		if (!isset ($_POST['fromemail']))
			$_POST['fromemail'] = $dbvalues['list_fromemail'];
		if (!isset ($_POST['frombounce']))
			$_POST['frombounce'] = $dbvalues['list_frombounce'];
	}
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		SmartyValidate :: disconnect();

		$bMail->set($_POST);
		if (!empty ($bMail->_data['body']))
			bmRedirect('mailings_send3.php');
		else
			bmRedirect('mailings_send2.php');
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}
$smarty->assign($_POST);
$smarty->display('admin/mailings/mailings_send.tpl');
bmKill();
?>
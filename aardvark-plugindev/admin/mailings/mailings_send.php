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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

if (PommoMailing::isCurrent())
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the %sStatus%s page to check its progress.'),'<a href="mailing_status.php">','</a>'));

// SmartyValidate Custom Validation Function
function check_charset($value, $empty, & $params, & $formvars) {
	$validCharsets = array (
		'UTF-8',
		'ISO-8859-1',
		'ISO-8859-2',
		'ISO-8859-7',
		'ISO-8859-15',
		'cp1251',
		'KOI8-R',
		'GB2312',
		'EUC-JP'
	);
	return in_array($value, $validCharsets);
}

if ($pommo->_config['demo_mode'] == 'on')
	$logger->addMsg(Pommo::_T('Demonstration Mode is on. No Emails will be sent.'));

// Get MailingData from SESSION.
$mailingData = $pommo->get('mailingData');
if (empty($mailingData))
	$mailingData = array();

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);

	// register custom criteria
	SmartyValidate :: register_criteria('isCharSet', 'check_charset');

	SmartyValidate :: register_validator('fromname', 'fromname', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('subject', 'subject', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('fromemail', 'fromemail', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('frombounce', 'frombounce', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('ishtml', 'ishtml:/(on|off)/i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('mailgroup', 'mailgroup:/(all|\d+)/i', 'isRegExp', false, false, 'trim');

	SmartyValidate :: register_validator('charset', 'charset', 'isCharSet', false, false, 'trim');

	$formError = array ();
	$formError['fromname'] = $formError['subject'] = Pommo::_T('Cannot be empty.');
	$formError['charset'] = Pommo::_T('Invalid Character Set');
	$formError['fromemail'] = $formError['frombounce'] = Pommo::_T('Invalid email address');
	$formError['ishtml'] = $formError['mailgroup'] = Pommo::_T('Invalid Input');

	$smarty->assign('formError', $formError);

	if (!empty ($mailingData)) {
		// assign mailingData to POST
		$_POST['fromname'] = $mailingData['fromname'];
		$_POST['fromemail'] = $mailingData['fromemail'];
		$_POST['frombounce'] = $mailingData['frombounce'];
		$_POST['subject'] = $mailingData['subject'];
		$_POST['ishtml'] = $mailingData['ishtml'];
		$_POST['charset'] = $mailingData['charset'];
		$_POST['mailgroup'] = $mailingData['mailgroup'];
	} else { // mailingData Empty. Load default values from DB
		$dbvalues = PommoAPI::configGet(array (
			'list_fromname',
			'list_fromemail',
			'list_frombounce',
			'list_charset'
		));
		if (!isset ($_POST['fromname']))
			$_POST['fromname'] = $dbvalues['list_fromname'];
		if (!isset ($_POST['fromemail']))
			$_POST['fromemail'] = $dbvalues['list_fromemail'];
		if (!isset ($_POST['frombounce']))
			$_POST['frombounce'] = $dbvalues['list_frombounce'];
		if (!isset ($_POST['charset']))
			$_POST['charset'] = $dbvalues['list_charset'];
	}
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		SmartyValidate :: disconnect();

		// Save inputted data to $MailingData[] (gets stored in Session)
		$mailingData['fromname'] = $_POST['fromname'];
		$mailingData['fromemail'] = $_POST['fromemail'];
		$mailingData['frombounce'] = $_POST['frombounce'];
		$mailingData['subject'] = $_POST['subject'];
		$mailingData['ishtml'] = $_POST['ishtml'];
		$mailingData['charset'] = $_POST['charset'];
		$mailingData['mailgroup'] = $_POST['mailgroup'];
		$pommo->set(array('mailingData' => $mailingData));

		if (!empty ($mailingData['body']))
			Pommo::redirect('mailings_send3.php');
		else
			Pommo::redirect('mailings_send2.php');
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$smarty->assign('groups',PommoGroup::get());
$smarty->assign($_POST);
$smarty->display('admin/mailings/mailings_send.tpl');
Pommo::kill();
?>
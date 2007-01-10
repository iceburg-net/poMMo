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

$dbvalues = PommoAPI::configGet(array(
	'list_fromname',
	'list_fromemail',
	'list_frombounce',
	'list_charset'
));
		

// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailings_send',array(
	'fromname' => $dbvalues['list_fromname'],
	'fromemail' => $dbvalues['list_fromemail'],
	'frombounce' => $dbvalues['list_frombounce'],
	'list_charset' => $dbvalues['list_charset'],
	'subject' => '',
	'ishtml' => 'on',
	'mailgroup' => 'all'
	),
	$_POST);

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
		'EUC-JP',
		'ISO-2022-JP'
	);
	return in_array($value, $validCharsets);
}

if ($pommo->_config['demo_mode'] == 'on')
	$logger->addMsg(Pommo::_T('Demonstration Mode is on. No Emails will be sent.'));


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

	SmartyValidate :: register_validator('list_charset', 'list_charset', 'isCharSet', false, false, 'trim');

	$formError = array ();
	$formError['fromname'] = $formError['subject'] = Pommo::_T('Cannot be empty.');
	$formError['list_charset'] = Pommo::_T('Invalid Character Set');
	$formError['fromemail'] = $formError['frombounce'] = Pommo::_T('Invalid email address');
	$formError['ishtml'] = $formError['mailgroup'] = Pommo::_T('Invalid Input');
	$smarty->assign('formError', $formError);
	
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		SmartyValidate :: disconnect();

		if (@!empty($pommo->_session['state']['mailings_send2']['body']))
			Pommo::redirect('mailings_send3.php');
		else
			Pommo::redirect('mailings_send2.php');
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$smarty->assign('groups',PommoGroup::get());
$smarty->assign($state);
$smarty->display('admin/mailings/mailings_send.tpl');
Pommo::kill();
?>
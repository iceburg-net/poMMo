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

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_groups.php');
require_once (bm_baseDir . '/inc/db_mailing.php');

$poMMo = & fireup('secure', 'keep');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

function check_charset($value, $empty, & $params, & $formvars) {
	$validCharsets = array (
		'UTF-8',
		'ISO-8859-1',
		'ISO-8859-15',
		'cp1251',
		'KOI8-R',
		'GB2312',
		'EUC-JP'
	);

	return in_array($value, $validCharsets);
}


// check to see if a mailing is taking place (queue not empty)
if (!mailingQueueEmpty($dbo)) {
	bmKill(sprintf(_T('A mailing is already taking place. Please allow it to finish before creating another. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php">', '</a>'));
}

// get groups for select -- key == ID, val == group name
$groups = dbGetGroups($dbo);
$smarty->assign('groups', $groups);

if ($poMMo->_config['demo_mode'] == 'on')
	$logger->addMsg(_T('Demonstration Mode is on. No Emails will be sent.'));

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);
	
	// register custom criteria
	SmartyValidate::register_criteria('isCharSet','check_charset');

	SmartyValidate :: register_validator('fromname', 'fromname', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('subject', 'subject', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('fromemail', 'fromemail', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('frombounce', 'frombounce', 'isEmail', false, false, 'trim');
	SmartyValidate :: register_validator('mailtype', 'mailtype:/(html|plain)/i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('group_id', 'group_id:/(all|\d+)/i', 'isRegExp', false, false, 'trim');
	
	SmartyValidate :: register_validator('charset', 'charset', 'isCharSet', false, false, 'trim');
	
	$formError['charset'] = _T('Invalid Character Set');
	

	$formError = array ();
	$formError['fromname'] = $formError['subject'] = _T('Cannot be empty.');

	$formError['fromemail'] = $formError['frombounce'] = _T('Invalid email address');

	$formError['mailtype'] = $formError['group_id'] = _T('Invalid Input');

	$smarty->assign('formError', $formError);

	// populate _POST with info from database (fills in form values...) or historic input if set...
	$historic = $poMMo->get();
	if (isset ($historic['fromname'])) {
		$_POST = $historic;

//ct

		// Mailtype specific loading -> difference in html: body=html / altbody=text; plain: body=text
		// mailtype is set through mailtype plain/html (in DB its on/off)
		if ($historic['ishtml'] == 'on') {
		
			$_POST['mailtype'] = 'html';
			$_POST['body'] = $historic['body'];
			$_POST['altbody'] = $historic['altbody'];
		
		} elseif ($historic['ishtml'] == 'off') {
		
			$_POST['mailtype'] = 'plain';
			$_POST['body'] = $historic['body'];
		
		}
		
		// Mailgroup loading
		// Since the Mailgroup is saved in the DB at the date of sending and can change during time (new name,
		// other name, other subscribers, other rules) and we need to preserve the data at the time the mailng 
		// was sent we try to select the name from the actual groups, if its there it will be selected through
		// the ID
		// 'all' has extra handling, since its not a ID
		if (isset($historic['mailgroup'])) {

			// If mailgroup is numeric, its an id, else if its a string, get the group ID from it if it exists
			if (is_numeric($historic['mailgroup'])){

				$_POST['group_id'] = $historic['mailgroup'];

			} elseif (is_string($historic['mailgroup'])) {

				if ($historic['mailgroup'] == "all") {
					$_POST['group_id'] = "all";
				} else {
					$mailgroupid = getGroupID($dbo, $historic['mailgroup']);
					if (isset($mailgroupid)) {
						$_POST['group_id'] = $mailgroupid;
					} else {
						$logger->addMsg(_T("Reloaded mailgroup not valid. Select a actual one."));
					}
				}			

			} else {
				// In case the mailgroup is deprecated, out of date, ...
				$logger->addMsg(_T("This is not a valid mailgroup."));
			}
			
		} else {
			$logger->addMsg(_T("Mailgroup not set. The mailgroup 'All subscribers' is selected until you choose another one."));
		}

//ct

	} else {
		$dbvalues = $poMMo->getConfig(array (
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

		$poMMo->set($_POST);
		if (!empty ($poMMo->_data['body']))
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

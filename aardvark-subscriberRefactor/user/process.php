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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// STORE user input. Input is appended to referer URL via HTTP_GET
$input = urlencode(serialize($_POST));

/**********************************
	VALIDATE INPUT
 *********************************/

if (empty ($_POST['pommo_signup']))
	Pommo::redirect('login.php');

$subscriber = array(
	
)
if (!PommoHelper::isEmail($_POST['bm_email']))



// ** check for correct email syntax
	if (!isEmail($_POST['bm_email']))
		$logger->addErr(_T('Invalid Email Address'));
		
	// ** check if confirmation email matches. (if exists)
	if (isset($_POST['updateForm']) && $_POST['email2'] != $_POST['bm_email'])
		$logger->addErr(_T('Emails must match.'));

	// ** check if email already exists in DB ("duplicates are bad..")
	if ($dupeCheck) {
		if (isDupeEmail($dbo, $_POST['bm_email'])) {
			$logger->addErr('Email address already exists. Duplicates are not allowed');
			global $smarty;
			if (is_object($smarty))
				$smarty->assign('dupe', TRUE);
		}
	}

	// ** validate user submitted fields
	$fields = & dbGetFields($dbo, 'active');
	$subscriber_data = array ();
	
	if (!empty($fields)) {
	foreach (array_keys($fields) as $field_id) {
		$field = & $fields[$field_id];

		// check to make sure a required field is not empty
		if (empty ($_POST['d'][$field_id]) && $field['required'] == 'on') {
			$logger->addErr($field['prompt'] . ' ' . _T('was a required field.'));
			continue;
		}

		// create field array
		if (!empty ($_POST['d'][$field_id])) {
			// TODO : insert validation schemes here (ie. check options, #, date)
			switch ($field['type']) {
				case 'checkbox' :
					if ($_POST['d'][$field_id] == 'on') // don't add to subscriber_data if value is not checked..
						$subscriber_data[$field_id] = str2db($_POST['d'][$field_id]);
					break;
				default :
					$subscriber_data[$field_id] = str2db($_POST['d'][$field_id]);
					break;
			}

		}
	}
	}
	if ($logger->isErr())
		return false;
	return true;



// check if errors exist, if so print results and die.
if (!validateSubscribeForm()) {
	$smarty->assign('back', TRUE);
	
	// attempt to detect if referer was set
	// TODO; should this default to $_SERVER['HTTP_REFERER']; ? -- for those who have customized the plain html subscriberForm..
	$referer = (!empty($_POST['bmReferer'])) ? $_POST['bmReferer'] : $pommo->_http.$pommo->_baseUrl.'user/subscribe.php';
	
	// append stored input
	$smarty->assign('referer',$referer.'?input='.$input);
	
	$smarty->display('user/process.tpl');
	Pommo::kill();
}

/**********************************
	ADD SUBSCRIBER
 *********************************/
 
 // TODO.. if confirmation is not needed, don't add to pending first...
if (empty($_POST['d']))
	$_POST['d'] = FALSE;
$confirmation_key = dbPendingAdd($dbo, 'add', $_POST['bm_email'], $_POST['d']);
if (empty ($confirmation_key))
	Pommo::kill('dbPendingAdd(): Confirmation key not returned.');

// determine if we should bypass output from this page and redirect.
$config = PommoAPI::configGet(array (
	'site_success',
	'site_confirm',
	'list_confirm',
	'messages'
));

$redirectURL = FALSE;
if (!empty ($config['site_confirm']) && $config['list_confirm'] == 'on')
	$redirectURL = $config['site_confirm'];
elseif (!empty ($config['site_success']) && $config['list_confirm'] != 'on') 
	$redirectURL = $config['site_success'];

if ($config['list_confirm'] == 'on') { // email confirmation required
	// send subscription confirmation mail
	require_once ($pommo->_baseDir . '/inc/lib.mailings.php');
	if (bmSendConfirmation($_POST['bm_email'], $confirmation_key, "subscribe")) {
		$logger->addMsg(Pommo::_T('Subscription request received.').' '.Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
	} else {
		$logger->addErr(Pommo::_T('Problem sending mail! Please contact the administrator.'));
	}
} else { // no email confirmation required... subscribe user
	if (dbSubscriberAdd($dbo, $confirmation_key)) {
		$messages = unserialize($config['messages']);
		$logger->addMsg($messages['subscribe_suc']);
	} else {
		$logger->addErr(Pommo::_T('Problem adding subscriber. Please contact the administrator.'));
	}
}

if (!$logger->isErr() && $redirectURL) {
	$logger->clear(); // TODO -> maybe message clearing to bmKill??
	Pommo::redirect($redirectURL);
}

$smarty->display('user/process.tpl');
Pommo::kill();



// returns true if valid.. false if not. Adds errors/messages to logger.
function validateSubscribeForm($dupeCheck = TRUE) {
	global $logger;
	global $dbo;
	require_once (bm_baseDir . '/inc/lib.txt.php');

	
}

?>
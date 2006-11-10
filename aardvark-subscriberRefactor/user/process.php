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
require_once ($pommo->_baseDir . '/inc/db_subscribers.php');
require_once ($pommo->_baseDir . '/inc/db_fields.php');
require($pommo->_baseDir.'/inc/lib.validate_subscriber.php');

$pommo = & fireup();
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
?>